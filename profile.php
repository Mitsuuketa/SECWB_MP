<?php 
include 'session_config.php';
include 'navbar.php'; 

// Include database connection
include 'db_connection.php';

// Fetch user details from the database based on session email
$email = $_SESSION['email'];
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    // Set session variables
    $_SESSION['profile_photo'] = $user['profile_photo'];
    $_SESSION['description'] = $user['description'];
    $_SESSION['wallet'] = $user['wallet'];
    $_SESSION['address'] = $user['address'];
    $_SESSION['phone'] = $user['phone'];
} else {
    echo "User not found.";
    exit;
}

// Update description if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['description'])) {
    $newDescription = mysqli_real_escape_string($conn, $_POST['description']);
    $sql = "UPDATE users SET description = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $newDescription, $email);
    if ($stmt->execute()) {
        $_SESSION['description'] = $newDescription;
        $message = "Description updated successfully.";
    } else {
        $message = "Error updating description: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css">
    <style>
        body {
            background-color: #F5F5DC; /* Beige background for a warm feel */
            color: #6B4F4E; /* Coffee brown text for contrast */
            font-family: 'Montserrat', sans-serif;
        }
        .profile-container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .profile-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }
        #edit-description-form {
            display: none; /* Initially hide the form */
        }
    </style>
    <script>
        function toggleEditDescription() {
            var editDescriptionForm = document.getElementById('edit-description-form');
            if (editDescriptionForm.style.display === 'none' || editDescriptionForm.style.display === '') {
                editDescriptionForm.style.display = 'block';
            } else {
                editDescriptionForm.style.display = 'none';
            }
        }
    </script>
</head>
<body>
<div class="container profile-container">
    <div class="text-center mb-4">
        <?php if (isset($_SESSION['profile_photo']) && $_SESSION['profile_photo']): ?>
            <img src="uploads/6695382f67faa_logo.png" class="profile-photo" alt="Profile Photo">
        <?php else: ?>
            <img src="default_profile.png" class="profile-photo" alt="Default Profile Photo">
        <?php endif; ?>
    </div>
    <h2 class="text-center">Name: <?php echo htmlspecialchars($_SESSION['fullname']); ?></h2>
    <div id="description-div">
        <p class="text-center">Description<br><?php echo htmlspecialchars($_SESSION['description'] ?? 'No description provided.'); ?></p>
        <button class="btn btn-primary" onclick="toggleEditDescription()">Edit Description</button>
    </div>
    <form id="edit-description-form" method="POST" action="">
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($_SESSION['description'] ?? ''); ?></textarea>
        </div>
        <button type="submit" class="btn btn-success">Save</button>
        <button type="button" class="btn btn-secondary" onclick="toggleEditDescription()">Cancel</button>
    </form>
    <?php if (isset($message)): ?>
        <div class="alert alert-info mt-3"><?php echo $message; ?></div>
    <?php endif; ?>
    <ul class="list-group mt-4">
        <li class="list-group-item"><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></li>
        <li class="list-group-item"><strong>Wallet Balance:</strong> ₱<?php echo number_format($_SESSION['wallet'] ?? 0, 2); ?></li>
        <li class="list-group-item"><strong>Address:</strong> <?php echo htmlspecialchars($_SESSION['address'] ?? 'No address provided.'); ?></li>
        <li class="list-group-item"><strong>Phone:</strong> <?php echo htmlspecialchars($_SESSION['phone'] ?? 'No phone number provided.'); ?></li>
    </ul>
</div>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
