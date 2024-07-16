<?php
session_start();
include 'db_connection.php';
include 'session_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $review_id = $_POST['review_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    // Prepare and bind the SQL statement
    $sql = "UPDATE reviews SET rating = ?, comment = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("isii", $rating, $comment, $review_id, $user_id);

    // Execute the statement and check for errors
    if ($stmt->execute()) {
        // Redirect back to the index page after successful update
        header("Location: index.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    echo "Invalid request.";
}

// Close the database connection
$conn->close();
?>
