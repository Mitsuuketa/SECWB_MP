<?php
// session_config.php

// Start or resume the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Configure session settings
if (!isset($_SESSION['initiated'])) {
    ini_set('session.sid_length', 32); // Adjusted to a valid value within the allowed range
    ini_set('session.entropy_length', 16);
    ini_set('session.hash_function', 'sha256');
    ini_set('session.cookie_secure', 1); // Only send the cookie over HTTPS
    ini_set('session.cookie_httponly', 1); // Make the cookie accessible only through the HTTP protocol
    ini_set('session.use_strict_mode', 1); // Use strict mode to prevent session fixation
    $_SESSION['initiated'] = true;
}

// Set session timeout
$timeout_duration = 1800; // 30 minutes
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    session_start();
    session_regenerate_id(true);
}
$_SESSION['LAST_ACTIVITY'] = time();
?>
