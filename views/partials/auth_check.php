<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in (using the correct session variable)
if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_role'])) {
    header('Location: admin');
    exit();
}

// Check session timeout (optional) - 30 minutes default
$session_timeout = 1800; // 30 minutes in seconds
if (isset($_SESSION['admin_last_activity']) && 
    (time() - $_SESSION['admin_last_activity']) > $session_timeout) {
    
    // Clear all session data
    session_destroy();
    session_start();
    $_SESSION['error'] = 'Session expired. Please login again.';
    header('Location: admin');
    exit();
}

// Update last activity time
$_SESSION['admin_last_activity'] = time();
