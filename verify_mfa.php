<?php
include 'db_connection.php';
session_start();

if (!isset($_GET['token'])) {
    die("Invalid request.");
}

$token = $_GET['token'];

// Verify MFA Token
$mfaSql = "SELECT user_id FROM mfa_tokens WHERE token = ? AND expires_at > NOW()";
$mfaStmt = $conn->prepare($mfaSql);
$mfaStmt->bind_param("s", $token);
$mfaStmt->execute();
$mfaResult = $mfaStmt->get_result();

if ($mfaResult->num_rows > 0) {
    $mfaData = $mfaResult->fetch_assoc();
    $userId = $mfaData['user_id'];

    // Retrieve user data
    $userSql = "SELECT id, email, fullname, role FROM users WHERE id = ?";
    $userStmt = $conn->prepare($userSql);
    $userStmt->bind_param("i", $userId);
    $userStmt->execute();
    $userResult = $userStmt->get_result();

    if ($userResult->num_rows > 0) {
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
    }
}

// Delete expired or invalid token to prevent reuse
$deleteSql = "DELETE FROM mfa_tokens WHERE token = ?";
$deleteStmt = $conn->prepare($deleteSql);
$deleteStmt->bind_param("s", $token);
$deleteStmt->execute();

echo "Invalid or expired authentication link.";
?>
