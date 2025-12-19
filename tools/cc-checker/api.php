<?php
/**
 * CC Checker API Handler
 * Secure API endpoint for credit card checking operations
 */

require_once 'config.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    handleError('Method not allowed', 405);
}

// Verify Content-Type
$contentType = isset($_SERVER['CONTENT_TYPE']) ? trim($_SERVER['CONTENT_TYPE']) : '';
if (stripos($contentType, 'application/json') === false) {
    handleError('Content-Type must be application/json', 400);
}

// Get POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    handleError('Invalid JSON', 400);
}

// Verify CSRF token
if (!isset($data['csrf_token']) || !verifyCSRFToken($data['csrf_token'])) {
    logSecurityEvent('csrf_token_validation_failed', ['data' => $data]);
    handleError('Invalid CSRF token', 403);
}

// Check rate limit
if (!checkRateLimit()) {
    logSecurityEvent('rate_limit_exceeded');
    handleError('Rate limit exceeded. Please try again later.', 429);
}

// Get action
$action = sanitizeInput($data['action'] ?? '');

// Require authentication for protected endpoints
requireAuth();

// Get user token from Authorization header
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
    $token = $matches[1];
} else {
    handleError('Missing authorization token', 401);
}

// Validate token with Node.js backend
if (!validateToken($token)) {
    logSecurityEvent('invalid_token', ['token' => substr($token, 0, 20) . '...']);
    handleError('Invalid or expired token', 401);
}

// Handle different actions
switch ($action) {
    case 'check_card':
        handleCheckCard($data, $token);
        break;
    
    case 'get_bin_info':
        handleGetBinInfo($data, $token);
        break;
    
    case 'save_to_vault':
        handleSaveToVault($data, $token);
        break;
    
    case 'get_vault_items':
        handleGetVaultItems($data, $token);
        break;
    
    case 'delete_vault_item':
        handleDeleteVaultItem($data, $token);
        break;
    
    default:
        handleError('Unknown action', 400);
}

/**
 * Handle Card Checking
 */
function handleCheckCard($data, $token) {
    // Sanitize and validate input
    $cardNumber = sanitizeInput($data['cardNumber'] ?? '');
    $expiryMonth = sanitizeInput($data['expiryMonth'] ?? '');
    $expiryYear = sanitizeInput($data['expiryYear'] ?? '');
    $cvv = sanitizeInput($data['cvv'] ?? '');
    $gateway = sanitizeInput($data['gateway'] ?? 'stripe');
    
    // Validate card number
    if (!validateCardNumber($cardNumber)) {
        handleError('Invalid card number', 400);
    }
    
    // Validate expiry
    if (!preg_match('/^(0[1-9]|1[0-2])$/', $expiryMonth)) {
        handleError('Invalid expiry month', 400);
    }
    
    if (!preg_match('/^\d{2,4}$/', $expiryYear)) {
        handleError('Invalid expiry year', 400);
    }
    
    // Validate CVV
    if (!preg_match('/^\d{3,4}$/', $cvv)) {
        handleError('Invalid CVV', 400);
    }
    
    // Deduct credits before checking (1 credit per check)
    $creditResult = deductCredits($token, 1, 'CC Check - ' . $gateway);
    if (!$creditResult['success']) {
        handleError($creditResult['message'], 400);
    }
    
    // Log the check attempt
    logSecurityEvent('card_check_attempt', [
        'gateway' => $gateway,
        'bin' => substr($cardNumber, 0, 6),
        'credits_deducted' => 1,
        'new_balance' => $creditResult['newBalance']
    ]);
    
    // Make API call to payment gateway
    $result = checkCardWithGateway($cardNumber, $expiryMonth, $expiryYear, $cvv, $gateway, $token);
    
    // Log the result
    logSecurityEvent('card_check_result', [
        'gateway' => $gateway,
        'status' => $result['status'] ?? 'unknown'
    ]);
    
    sendJSON([
        'success' => true,
        'result' => $result,
        'creditsDeducted' => 1,
        'remainingCredits' => $creditResult['newBalance']
    ]);
}

/**
 * Check Card with Payment Gateway
 */
function checkCardWithGateway($cardNumber, $expiryMonth, $expiryYear, $cvv, $gateway, $token) {
    // For security, we don't actually call real payment gateways in this demo
    // In production, this would make secure API calls to Stripe, Braintree, etc.
    
    // Simulate API call delay
    usleep(500000); // 0.5 seconds
    
    // Return simulated result
    $bin = substr($cardNumber, 0, 6);
    $last4 = substr($cardNumber, -4);
    
    // Determine card brand from BIN
    $brand = getCardBrand($bin);
    
    // Simulate different responses based on gateway
    $responses = [
        'approved' => [
            'status' => 'approved',
            'message' => 'Card Live - CVV Matched',
            'code' => '00',
            'brand' => $brand,
            'bin' => $bin,
            'last4' => $last4,
            'gateway' => $gateway
        ],
        'declined' => [
            'status' => 'declined',
            'message' => 'Insufficient Funds',
            'code' => '51',
            'brand' => $brand,
            'bin' => $bin,
            'last4' => $last4,
            'gateway' => $gateway
        ],
        'invalid' => [
            'status' => 'invalid',
            'message' => 'Invalid Card Number',
            'code' => '14',
            'brand' => $brand,
            'bin' => $bin,
            'last4' => $last4,
            'gateway' => $gateway
        ]
    ];
    
    // Randomly select a response for demo purposes
    $responseKeys = array_keys($responses);
    $randomKey = $responseKeys[array_rand($responseKeys)];
    
    return $responses[$randomKey];
}

/**
 * Get Card Brand from BIN
 */
function getCardBrand($bin) {
    $firstDigit = substr($bin, 0, 1);
    $firstTwo = substr($bin, 0, 2);
    $firstFour = substr($bin, 0, 4);
    
    if ($firstDigit === '4') {
        return 'Visa';
    } elseif ($firstTwo >= 51 && $firstTwo <= 55) {
        return 'Mastercard';
    } elseif ($firstTwo === '34' || $firstTwo === '37') {
        return 'American Express';
    } elseif ($firstTwo === '60' || $firstTwo === '65' || $firstFour === '6011') {
        return 'Discover';
    } elseif ($firstTwo >= 35 && $firstTwo <= 39) {
        return 'JCB';
    }
    
    return 'Unknown';
}

/**
 * Handle BIN Info Request
 */
function handleGetBinInfo($data, $token) {
    $bin = sanitizeInput($data['bin'] ?? '');
    
    if (!preg_match('/^\d{6,8}$/', $bin)) {
        handleError('Invalid BIN format', 400);
    }
    
    // In production, this would call a BIN lookup API
    $binInfo = [
        'bin' => $bin,
        'brand' => getCardBrand($bin),
        'type' => 'CREDIT',
        'level' => 'CLASSIC',
        'bank' => 'DEMO BANK',
        'country' => 'US',
        'countryName' => 'United States'
    ];
    
    sendJSON([
        'success' => true,
        'binInfo' => $binInfo
    ]);
}

/**
 * Deduct Credits from User Account
 */
function deductCredits($token, $amount = 1, $description = 'Card check operation') {
    // Call Node.js backend to deduct credits
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, API_BASE_URL . '/deduct-credit');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'amount' => $amount,
        'description' => $description
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        return [
            'success' => true,
            'newBalance' => $data['newBalance'] ?? 0
        ];
    } else {
        $data = json_decode($response, true);
        return [
            'success' => false,
            'message' => $data['message'] ?? 'Failed to deduct credits'
        ];
    }
}

/**
 * Handle Save to Vault
 */
function handleSaveToVault($data, $token) {
    $cardData = [
        'cardNumber' => sanitizeInput($data['cardNumber'] ?? ''),
        'expiryMonth' => sanitizeInput($data['expiryMonth'] ?? ''),
        'expiryYear' => sanitizeInput($data['expiryYear'] ?? ''),
        'cvv' => sanitizeInput($data['cvv'] ?? ''),
        'gateway' => sanitizeInput($data['gateway'] ?? ''),
        'result' => sanitizeInput($data['result'] ?? '')
    ];
    
    // Forward to Node.js backend
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, API_BASE_URL . '/vault/save');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($cardData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        sendJSON(['success' => true, 'message' => 'Saved to vault']);
    } else {
        handleError('Failed to save to vault', 500);
    }
}

/**
 * Handle Get Vault Items
 */
function handleGetVaultItems($data, $token) {
    // Forward to Node.js backend
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, API_BASE_URL . '/vault/items');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $items = json_decode($response, true);
        sendJSON(['success' => true, 'items' => $items]);
    } else {
        handleError('Failed to retrieve vault items', 500);
    }
}

/**
 * Handle Delete Vault Item
 */
function handleDeleteVaultItem($data, $token) {
    $itemId = sanitizeInput($data['itemId'] ?? '');
    
    if (empty($itemId)) {
        handleError('Item ID required', 400);
    }
    
    // Forward to Node.js backend
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, API_BASE_URL . '/vault/delete/' . $itemId);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        sendJSON(['success' => true, 'message' => 'Item deleted']);
    } else {
        handleError('Failed to delete item', 500);
    }
}
?>
