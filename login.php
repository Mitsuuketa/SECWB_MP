<?php
putenv('DEBUG=true'); // Set debug mode as needed

include 'session_config.php';
include 'error_handling.php'; // Include the error handling script
session_start();

// Include database connection
include 'db_connection.php';
include 'navbar.php';


$message = ""; // Initialize the message variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Check if there's an existing login attempt tracking record for this email
        $attemptSql = "SELECT * FROM login_attempts WHERE email=?";
        $attemptStmt = $conn->prepare($attemptSql);
        if (!$attemptStmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }
        $attemptStmt->bind_param("s", $email);
        $attemptStmt->execute();
        $attemptResult = $attemptStmt->get_result();

        if ($attemptResult && $attemptResult->num_rows > 0) {
            $attemptData = $attemptResult->fetch_assoc();
            $lastAttemptTime = strtotime($attemptData['last_attempt_time']);
            $currentTime = time();
            $timeDifference = $currentTime - $lastAttemptTime;

            // If the last attempt was within the last hour and attempts exceed threshold, lock the account
            if ($timeDifference < 3600 && $attemptData['attempt_count'] >= 5) {
                $message = "Your account has been temporarily locked due to multiple failed login attempts. Please try again later.";
                logAction('Account Locked', "Email: $email - Multiple failed login attempts.");
                exit; // Exit script to prevent further login attempts
            }
        }

        // Query to fetch user data based on email
        $sql = "SELECT * FROM users WHERE email=?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Verify password
            if ($user['role'] == 'Administrator') {
                // Verify plain password for administrator
                if ($password == $user['password']) {
                    // Reset login attempts if successful login
                    resetLoginAttempts($email);
                    $message = "Welcome back, " . $user['fullname'] . "! You are logged in as an Administrator. Redirecting...";
                    // Set session variables after successful login
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['fullname'] = $user['fullname'];
                    $_SESSION['role'] = 'Administrator';
                    $_SESSION['user_id'] = $user['id']; // Add user_id to session
                    // Log successful login
                    logAction('Login Successful', "Email: $email - Role: Administrator");
                    // Redirect to admin.php after 2 seconds
                    echo '<meta http-equiv="refresh" content="2;url=admin.php">';
                } else {
                    handleFailedLogin($email);
                    $message = "Incorrect password. Please try again.";
                    logAction('Login Failed', "Email: $email - Incorrect password for Administrator.");
                }
            } else {
                // Verify password using password_verify for regular users
                if (password_verify($password, $user['password'])) {
                    // Reset login attempts if successful login
                    resetLoginAttempts($email);
                    $message = "Welcome back, " . $user['fullname'] . "! You are logged in as a User. Redirecting...";
                    // Set session variables after successful login
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['fullname'] = $user['fullname'];
                    $_SESSION['role'] = 'User';
                    $_SESSION['user_id'] = $user['id']; // Add user_id to session
                    // Log successful login
                    logAction('Login Successful', "Email: $email - Role: User");
                    // Redirect to index.php after 2 seconds
                    echo '<meta http-equiv="refresh" content="2;url=index.php">';
                } else {
                    handleFailedLogin($email);
                    $message = "Incorrect password. Please try again.";
                    logAction('Login Failed', "Email: $email - Incorrect password for User.");
                }
            }
        } else {
            $message = "No user found with this email. Please sign up.";
            logAction('Login Failed', "Email: $email - No user found.");
        }
    } catch (Exception $e) {
        echo handle_error($e); // Display detailed error message or generic message based on debug mode
    }
}

// Function to handle failed login attempts
function handleFailedLogin($email) {
    global $conn;
    // Update login attempt record or create new record if not exists
    $attemptSql = "INSERT INTO login_attempts (email, attempt_count, last_attempt_time) VALUES (?, 1, NOW()) ON DUPLICATE KEY UPDATE attempt_count = attempt_count + 1, last_attempt_time = NOW()";
    $attemptStmt = $conn->prepare($attemptSql);
    $attemptStmt->bind_param("s", $email);
    $attemptStmt->execute();
}

// Function to reset login attempts for a given email
function resetLoginAttempts($email) {
    global $conn;
    // Reset login attempt count
    $resetSql = "DELETE FROM login_attempts WHERE email=?";
    $resetStmt = $conn->prepare($resetSql);
    $resetStmt->bind_param("s", $email);
    $resetStmt->execute();
}

// Function to log actions to admin
function logAction($action, $details) {
    $logFile = 'C:/xampp/htdocs/SECWB_MP/app.log';
    $timestamp = date('[Y-m-d H:i:s]');

    $logMessage = "$timestamp [$action] $details" . PHP_EOL;

    // Check if the directory is writable
    if (!is_writable(dirname($logFile))) {
        error_log("Directory is not writable: " . dirname($logFile));
        return;
    }

    // Check if the file exists and is writable
    if (file_exists($logFile) && !is_writable($logFile)) {
        error_log("File is not writable: " . $logFile);
        return;
    }

    // Attempt to write to the log file
    if (file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX) === false) {
        error_log("Failed to write to log file: " . $logFile);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kape-Kada Coffee Shop</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #F5F5DC; /* Beige background for a warm feel */
            color: #6B4F4E; /* Coffee brown text for contrast */
            font-family: 'Montserrat', sans-serif;
            background-image: url('loginbg.svg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed; 
            background-position: center;
        }
        .navbar {
            font-family: 'Merriweather', serif;
        }
        .login-container {
            margin-top: 125px;
        }
        .login-form {
            background: #FFFFFF;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .login-title {
            color: #A52A2A; /* Rich brown color for titles */
            font-weight: 700;
            margin-bottom: 30px;
        }
        .form-control {
            margin-bottom: 20px;
        }
        .btn-login {
            background-color: #A52A2A;
            color: #FFFFFF;
        }
        .signup-link {
            text-align: center;
            margin-top: 20px;
        }
        .signup-link a {
            color: #A52A2A;
            text-decoration: none;
        }
        .signup-link a:hover {
            text-decoration: underline;
        }
        .alert-slide {
            position: fixed;
            top: 170px; /* Adjusted top position to be below the navbar */
            left: 20px; /* Adjusted left position */
            z-index: 9999;
            background-color: #A52A2A;
            color: #FFFFFF;
            padding: 10px 20px;
            border-radius: 8px;
            animation: slideIn 0.5s ease forwards;
        }
        @keyframes slideIn {
            0% {
                left: -100%;
            }
            100% {
                left: 20px; /* Slide in from the left */
            }
        }
        @keyframes slideOut {
            0% {
                left: 20px;
            }
            100% {
                left: -100%; /* Slide out to the left */
            }
        }
    </style>
</head>
<body>

<div class="container login-container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form class="login-form" method="post" action="login.php">
                <h2 class="login-title">Login</h2>
                <div class="form-group">
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email"> <!-- Add name attribute -->
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password"> <!-- Add name attribute -->
                </div>
                <button type="submit" class="btn btn-block btn-login">Login</button>
                <div class="signup-link">
                    <a href="signup.php">Don't have an account? Sign Up</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Display slide prompt message if set -->
<?php if (!empty($message)) : ?>
<div class="alert-slide" id="alertSlide"><?php echo $message; ?></div>
<script>
    // Slide up the prompt message after 5 seconds
    setTimeout(function() {
        var alertSlide = document.getElementById('alertSlide');
        if (alertSlide) {
            alertSlide.style.animation = 'slideIn 0.5s ease forwards';
            setTimeout(function() {
                alertSlide.style.display = 'none'; // Hide the prompt message after sliding up
            }, 5000);
        }
    }, 500);
</script>
<?php endif; ?>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>