<?php
include 'session_config.php';


// Include database connection
include 'db_connection.php';

// Include password hashing library
include 'password_compat.php'; // Ensure you have the password_compat.php file that includes password_hash and password_verify functions

// If user is already logged in, redirect to appropriate page
if (isset($_SESSION['email']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Administrator') {
        header("Location: admin.php");
        exit();
    } else if ($_SESSION['role'] == 'User') {
        header("Location: index.php");
        exit();
    }
}

include 'navbar.php'; 

$message = ""; // Initialize the message variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve user inputs and sanitize them
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = 'User'; // Default role for signed up users
    $address = isset($_POST['address']) ? mysqli_real_escape_string($conn, $_POST['address']) : null; // Get address if provided
    $wallet = isset($_POST['wallet']) ? floatval($_POST['wallet']) : 0.00; // Get wallet balance if provided
    $phone = isset($_POST['phone']) ? mysqli_real_escape_string($conn, $_POST['phone']) : null; // Get phone number if provided

    // Validate email domain
    if (!preg_match("/@(yahoo\.com|gmail\.com)$/", $email)) {
        $message = "Error: Email must be from yahoo.com or gmail.com.";
    } else {
        // Check if the email is already registered
        $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "Error: Email is already registered.";
        } else {
            // Handle profile photo upload
            $profile_photo = null;
            if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
                $allowedExtensions = array("jpg", "jpeg", "png");
                $uploadDir = 'uploads/';
                $photoName = $_FILES['profile_photo']['name'];
                $photoTmpPath = $_FILES['profile_photo']['tmp_name'];
                $photoExtension = strtolower(pathinfo($photoName, PATHINFO_EXTENSION));

                // Check if the uploaded file has a valid extension
                if (!in_array($photoExtension, $allowedExtensions)) {
                    $message = "Error: Profile photo must be in JPEG or PNG format.";
                } else {
                    // Ensure the upload directory exists, create it if it doesn't
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true); // Create directory with  permissions
                    }

                    // Generate a unique filename to prevent conflicts
                    $uniqueFilename = uniqid() . '_' . $photoName;
                    $dest_path = $uploadDir . $uniqueFilename;

                    if (move_uploaded_file($photoTmpPath, $dest_path)) {
                        $profile_photo = $dest_path;
                    } else {
                        $message = "Error uploading profile photo.";
                    }
                }
            }

            // Check if password has been breached
            if (checkPasswordBreached($password)) {
                $message = "Password has been involved in a data breach. Please choose a different password.";
            }else if (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[\W_]/', $password)) {
                    $message = "Error: Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.";
            } else {
                // Hash the password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Prepare the SQL statement to prevent SQL injection
                $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role, wallet, address, phone, profile_photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                // Bind parameters to the statement
                $stmt->bind_param("ssssdsss", $fullname, $email, $hashedPassword, $role, $wallet, $address, $phone, $profile_photo);

                // Execute the statement
                if ($stmt->execute()) {
                    $message = "Welcome, $fullname! Registration successful. Redirecting...";
                    // Set session variables after successful registration
                    $_SESSION['email'] = $email;
                    $_SESSION['fullname'] = $fullname;
                    $_SESSION['role'] = $role;
                    // Redirect to index.php after 2 seconds
                    echo '<meta http-equiv="refresh" content="2;url=index.php">';
                } else {
                    $message = "Error: " . $stmt->error;
                }

                // Close the statement
                $stmt->close();
            }
        }

        // Close the email check statement
        $stmt->close();
    }
}


// Function to check if password has been breached
function checkPasswordBreached($password) {
    // Calculate SHA-1 hash of the password
    $sha1Hash = strtoupper(sha1($password));

    // Take the first 5 characters of the hash to be used as a prefix for the API request
    $prefix = substr($sha1Hash, 0, 5);
    $suffix = substr($sha1Hash, 5);

    // Make a request to the Have I Been Pwned API
    $apiUrl = "https://api.pwnedpasswords.com/range/" . $prefix;
    $response = file_get_contents($apiUrl);

    // Check if the response contains the suffix of the hash
    if (strpos($response, $suffix) !== false) {
        // Password has been breached
        return true;
    } else {
        // Password is not breached
        return false;
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Kape-Kada Coffee Shop</title>
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
        .signup-container {
            margin-top: 125px;
        }
        .signup-form {
            background: #FFFFFF;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .signup-title {
            color: #A52A2A; /* Rich brown color for titles */
            font-weight: 700;
            margin-bottom: 30px;
        }
        .form-control {
            margin-bottom: 20px;
        }
        .btn-signup {
            background-color: #A52A2A;
            color: #FFFFFF;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        .login-link a {
            color: #A52A2A;
            text-decoration: none;
        }
        .login-link a:hover {
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

<div class="container signup-container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form class="signup-form" method="post" action="signup.php" enctype="multipart/form-data">
                <h2 class="signup-title">Sign Up</h2>
                <div class="form-group">
                    <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Full Name" required> <!-- Add name attribute -->
                </div>
                <div class="form-group">
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" required> <!-- Add name attribute -->
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required> <!-- Add name attribute -->
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" id="address" name="address" placeholder="Address" required> <!-- Add address field -->
                </div>
                <div class="form-group">
                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="Phone Number (e.g. 9171234567)" pattern="[0-9]{10}" maxlength="10" minlength="10" required> <!-- Add phone attribute -->
                </div>
                <div class="form-group">
                    <input type="number" step="0.01" min="1" class="form-control" id="wallet" name="wallet" placeholder="Wallet Balance" required> <!-- Add wallet balance field -->
                </div>
                <div class="form-group">
                    <input type="file" class="form-control-file" id="profile_photo" name="profile_photo" accept="image/jpeg, image/png" required>
                </div>  
                <button type="submit" class="btn btn-block btn-signup">Sign Up</button>
                <div class="login-link">
                    <a href="login.php">Already have an account?</a>
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
<script>
    function validateForm() {
        var email = document.getElementById('email').value;
        var password = document.getElementById('password').value;
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        var passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/;
        
        if (!emailRegex.test(email)) {
            alert("Please enter a valid email address.");
            return false;
        }
        
        if (!passwordRegex.test(password)) {
            alert("Password must be at least 8 characters long and contain at least one letter and one number.");
            return false;
        }
        
        return true;
    }

    function validateFileType() {
        var fileInput = document.getElementById('profile_photo');
        var filePath = fileInput.value;
        var file = fileInput.files[0];
        var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
        var maxSize = 2 * 1024 * 1024; // 2MB in bytes

        if (!allowedExtensions.exec(filePath)) {
            alert('Invalid file type. Please upload a JPEG or PNG image.');
            fileInput.value = ''; // Clear the file input
            return false;
        }

        if (file.size > maxSize) {
            alert('File size exceeds 2MB. Please upload a smaller image.');
            fileInput.value = ''; // Clear the file input
            return false;
        }
        return true;
    }

    // Bind the validateFileType function to the change event of the file input
    document.getElementById('profile_photo').addEventListener('change', validateFileType);
</script>
</body>
</html>