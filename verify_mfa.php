<?php
include 'db_connection.php';
session_start();

if (!isset($_GET['token'])) {
    die("Invalid request.");
}

$token = $_GET['token'];

// Verify MFA Token
$mfaSql = "SELECT * FROM mfa_tokens WHERE token = ? AND expires_at > NOW()";
$mfaStmt = $conn->prepare($mfaSql);
$mfaStmt->bind_param("s", $token);
$mfaStmt->execute();
$mfaResult = $mfaStmt->get_result();

if ($mfaResult->num_rows > 0) {
    $mfaData = $mfaResult->fetch_assoc();

    // Retrieve user data
    $userSql = "SELECT * FROM users WHERE id = ?";
    $userStmt = $conn->prepare($userSql);
    $userStmt->bind_param("i", $mfaData['user_id']);
    $userStmt->execute();
    $userResult = $userStmt->get_result();

    if ($userResult->num_rows > 0) {
        $user = $userResult->fetch_assoc();

        // Set session variables
        $_SESSION['email'] = $user['email'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['user_id'] = $user['id'];

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

echo "Invalid or expired authentication link.";
?>
