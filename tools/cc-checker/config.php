<?php
/**
 * CC Checker Security Configuration
 * Advanced security settings for the CC checker tool
 */

// Start secure session with strict settings
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: no-referrer");
header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
header("Content-Security-Policy: default-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com https://fonts.googleapis.com; img-src 'self' data: https:;");

// Prevent caching of sensitive data
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Configuration Constants
define('API_RATE_LIMIT', 10); // Max requests per minute
define('SESSION_TIMEOUT', 1800); // 30 minutes
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 900); // 15 minutes
define('CSRF_TOKEN_EXPIRY', 3600); // 1 hour

// API Configuration
define('STRIPE_API_KEY', getenv('STRIPE_API_KEY') ?: '');
define('BRAINTREE_API_KEY', getenv('BRAINTREE_API_KEY') ?: '');
define('PAYPAL_API_KEY', getenv('PAYPAL_API_KEY') ?: '');

// Database Configuration (using Node.js backend)
define('API_BASE_URL', 'http://localhost:3000/api');

// Encryption Key for sensitive data
define('ENCRYPTION_KEY', getenv('ENCRYPTION_KEY') ?: bin2hex(random_bytes(32)));

// Error Reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../logs/php_errors.log');

/**
 * Generate CSRF Token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token']) || 
        !isset($_SESSION['csrf_token_time']) || 
        (time() - $_SESSION['csrf_token_time']) > CSRF_TOKEN_EXPIRY) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF Token
 */
function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
        return false;
    }
    
    if ((time() - $_SESSION['csrf_token_time']) > CSRF_TOKEN_EXPIRY) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Sanitize Input
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validate JWT Token from Node.js backend
 */
function validateToken($token) {
    if (empty($token)) {
        return false;
    }
    
    // Call Node.js backend to validate token
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, API_BASE_URL . '/validate-token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For localhost
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        return $data !== null;
    }
    
    return false;
}

/**
 * Rate Limiting Check
 */
function checkRateLimit($identifier = null) {
    $identifier = $identifier ?: $_SERVER['REMOTE_ADDR'];
    $key = 'rate_limit_' . md5($identifier);
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 0, 'time' => time()];
    }
    
    $rateData = $_SESSION[$key];
    
    // Reset if time window passed
    if ((time() - $rateData['time']) > 60) {
        $_SESSION[$key] = ['count' => 1, 'time' => time()];
        return true;
    }
    
    // Check limit
    if ($rateData['count'] >= API_RATE_LIMIT) {
        return false;
    }
    
    $_SESSION[$key]['count']++;
    return true;
}

/**
 * Log Security Event
 */
function logSecurityEvent($event, $details = []) {
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        'event' => $event,
        'details' => $details
    ];
    
    $logFile = __DIR__ . '/../../logs/security.log';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    file_put_contents(
        $logFile,
        json_encode($logEntry) . PHP_EOL,
        FILE_APPEND | LOCK_EX
    );
}

/**
 * Encrypt Sensitive Data
 */
function encryptData($data) {
    $iv = random_bytes(16);
    $encrypted = openssl_encrypt(
        $data,
        'AES-256-CBC',
        ENCRYPTION_KEY,
        0,
        $iv
    );
    return base64_encode($iv . $encrypted);
}

/**
 * Decrypt Sensitive Data
 */
function decryptData($data) {
    $data = base64_decode($data);
    $iv = substr($data, 0, 16);
    $encrypted = substr($data, 16);
    return openssl_decrypt(
        $encrypted,
        'AES-256-CBC',
        ENCRYPTION_KEY,
        0,
        $iv
    );
}

/**
 * Check if user is authenticated
 */
function isAuthenticated() {
    if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
        return false;
    }
    
    // Check session timeout
    if (isset($_SESSION['last_activity']) && 
        (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
        session_unset();
        session_destroy();
        return false;
    }
    
    $_SESSION['last_activity'] = time();
    return true;
}

/**
 * Require Authentication
 */
function requireAuth() {
    if (!isAuthenticated()) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'error' => 'Authentication required'
        ]);
        exit;
    }
}

/**
 * Validate Card Number Format (basic Luhn algorithm)
 */
function validateCardNumber($number) {
    $number = preg_replace('/\D/', '', $number);
    
    if (strlen($number) < 13 || strlen($number) > 19) {
        return false;
    }
    
    $sum = 0;
    $length = strlen($number);
    
    for ($i = 0; $i < $length; $i++) {
        $digit = (int)$number[$length - $i - 1];
        
        if ($i % 2 === 1) {
            $digit *= 2;
            if ($digit > 9) {
                $digit -= 9;
            }
        }
        
        $sum += $digit;
    }
    
    return ($sum % 10) === 0;
}

/**
 * Send JSON Response
 */
function sendJSON($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Handle Errors
 */
function handleError($message, $code = 400) {
    logSecurityEvent('error', ['message' => $message, 'code' => $code]);
    sendJSON(['success' => false, 'error' => $message], $code);
}

// Initialize session security
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
    $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
}

// Verify session consistency
if (isset($_SESSION['ip_address']) && $_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
    session_unset();
    session_destroy();
    logSecurityEvent('session_hijack_attempt', [
        'original_ip' => $_SESSION['ip_address'] ?? 'unknown',
        'current_ip' => $_SERVER['REMOTE_ADDR']
    ]);
}
?>
