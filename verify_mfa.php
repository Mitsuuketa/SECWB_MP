<?php
include 'db_connection.php';
session_start();

// Check if token is provided
if (!isset($_GET['token'])) {
    die("Invalid request. No token provided.");
}

$token = urldecode($_GET['token']); // Decode token to prevent encoding issues

// Verify MFA Token
$mfaSql = "SELECT user_id, expires_at FROM mfa_tokens WHERE token = ?";
$mfaStmt = $conn->prepare($mfaSql);
$mfaStmt->bind_param("s", $token);
$mfaStmt->execute();
$mfaResult = $mfaStmt->get_result();

if ($mfaResult->num_rows == 0) {
    die("Invalid or expired authentication link (Token not found).");
}

$mfaData = $mfaResult->fetch_assoc();
$userId = $mfaData['user_id'];
$expiresAt = strtotime($mfaData['expires_at']);

// Check if the token is expired
if ($expiresAt < time()) {
    // Delete expired token
    $deleteSql = "DELETE FROM mfa_tokens WHERE token = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bind_param("s", $token);
    $deleteStmt->execute();
    
    die("Invalid or expired authentication link (Token expired).");
}

// Retrieve user data
$userSql = "SELECT id, email, fullname, role FROM users WHERE id = ?";
$userStmt = $conn->prepare($userSql);
$userStmt->bind_param("i", $userId);
$userStmt->execute();
$userResult = $userStmt->get_result();

if ($userResult->num_rows == 0) {
    die("User not found for the given token.");
}

$user = $userResult->fetch_assoc();

// Prevent session fixation
session_regenerate_id(true);

// Set session variables
$_SESSION['user_id'] = $user['id'];
$_SESSION['email'] = $user['email'];
$_SESSION['fullname'] = $user['fullname'];
$_SESSION['role'] = $user['role'];

// Delete used MFA token
$deleteSql = "DELETE FROM mfa_tokens WHERE token = ?";
$deleteStmt = $conn->prepare($deleteSql);
$deleteStmt->bind_param("s", $token);
$deleteStmt->execute();

// Redirect to dashboard
header("Location: index.php");
exit;
?>
