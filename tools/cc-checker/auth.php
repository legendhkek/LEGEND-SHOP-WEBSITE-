<?php
/**
 * CC Checker Authentication Handler
 * Handles authentication and session management
 */

require_once 'config.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    handleError('Method not allowed', 405);
}

// Get POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    handleError('Invalid JSON', 400);
}

// Get action
$action = sanitizeInput($data['action'] ?? '');

switch ($action) {
    case 'init_session':
        handleInitSession();
        break;
    
    case 'verify_token':
        handleVerifyToken($data);
        break;
    
    case 'logout':
        handleLogout();
        break;
    
    default:
        handleError('Unknown action', 400);
}

/**
 * Initialize Session
 */
function handleInitSession() {
    $csrfToken = generateCSRFToken();
    
    sendJSON([
        'success' => true,
        'csrf_token' => $csrfToken,
        'session_id' => session_id()
    ]);
}

/**
 * Verify Token
 */
function handleVerifyToken($data) {
    $token = sanitizeInput($data['token'] ?? '');
    
    if (empty($token)) {
        handleError('Token required', 400);
    }
    
    // Check rate limit for auth attempts
    if (!checkRateLimit('auth_' . $_SERVER['REMOTE_ADDR'])) {
        logSecurityEvent('auth_rate_limit_exceeded');
        handleError('Too many authentication attempts', 429);
    }
    
    // Validate token with Node.js backend
    if (validateToken($token)) {
        $_SESSION['authenticated'] = true;
        $_SESSION['last_activity'] = time();
        $_SESSION['token'] = $token;
        
        logSecurityEvent('authentication_success');
        
        sendJSON([
            'success' => true,
            'message' => 'Authentication successful',
            'csrf_token' => generateCSRFToken()
        ]);
    } else {
        logSecurityEvent('authentication_failed', ['token' => substr($token, 0, 20) . '...']);
        handleError('Invalid token', 401);
    }
}

/**
 * Handle Logout
 */
function handleLogout() {
    logSecurityEvent('logout');
    
    session_unset();
    session_destroy();
    
    sendJSON([
        'success' => true,
        'message' => 'Logged out successfully'
    ]);
}
?>
