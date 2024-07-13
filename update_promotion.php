<?php
include 'session_config.php';
session_start(); // Start the session
include 'admin_navbar.php';
// Include database connection
include 'db_connection.php';

$message = ""; // Initialize the message variable

// Check if promotion to update is selected
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_id'])) {
    $update_id = $_POST['update_id'];
    // Fetch promotion data from the database
    $sql = "SELECT * FROM promotions WHERE id = '$update_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        $promotion = mysqli_fetch_assoc($result);
    } else {
        // Promotion not found, redirect back to admin_promotion.php
        $_SESSION['message'] = "Promotion not found.";
        header("Location: admin_promotion.php");
        exit();
    }
} else {
    // If update_id is not set, redirect back to admin_promotion.php
    $_SESSION['message'] = "Invalid request to update promotion.";
    header("Location: admin_promotion.php");
    exit();
}

// Initialize form input values
$name = isset($_POST['name']) ? $_POST['name'] : $promotion['name'];
$description = isset($_POST['description']) ? $_POST['description'] : $promotion['description'];
$price = isset($_POST['price']) ? $_POST['price'] : $promotion['price'];
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : $promotion['start_date'];
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : $promotion['end_date'];

// Check if form for updating promotion is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_promotion'])) {
    // Check if the $_POST keys are set before accessing them
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : '';
    $update_id = isset($_POST['update_id']) ? $_POST['update_id'] : '';

    // Validate start and end dates
    $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
    $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';

    // Check if start date is before end date
    if ($start_date > $end_date) {
        $message = "End date should be after start date.";
    } else {
        // Update promotion in the database
        $sql = "UPDATE promotions SET name='$name', description='$description', price='$price', start_date='$start_date', end_date='$end_date' WHERE id='$update_id'";
        if (mysqli_query($conn, $sql)) {
            // Set the success message
            $message = "Promotion details have been successfully updated. You will now be redirected to the promotion page.";
            // Redirect back to admin_promotion.php after 4 seconds
            echo '<meta http-equiv="refresh" content="4;url=admin_promotion.php">';
        } else {
            $message = "Error: " . $sql . "<br>" . mysqli_error($conn);
        }        
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Promotion - Kape-Kada Coffee Shop</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #F5F5DC; /* Beige background for a warm feel */
            color: #6B4F4E; /* Coffee brown text for contrast */
            font-family: 'Montserrat', sans-serif;
        }
        .navbar {
            font-family: 'Merriweather', serif;
        }
        .container {
            margin-top: 70px;
            margin-bottom: 50px;
        }
        .promotion-container {
            background-color: #FFFFFF;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .promotion-title {
            color: #A52A2A; /* Rich brown color for titles */
            font-weight: 700;
            margin-bottom: 30px;
        }
        .promotion-form input[type="text"],
        .promotion-form input[type="number"],
        .promotion-form input[type="date"] {
            margin-bottom: 20px;
        }
        .promotion-form button[type="submit"] {
            background-color: #A52A2A;
            color: #FFFFFF;
        }
        .alert-slide {
            position: fixed;
            top: 170px;
            right: 20px;
            z-index: 9999;
            background-color: #A52A2A;
            color: #FFFFFF;
            padding: 10px 20px;
            border-radius: 8px;
            animation: slideIn 0.5s ease forwards;
        }
        @keyframes slideIn {
            0% {
                right: -100%;
            }
            100% {
                right: 20px;
            }
        }
        @keyframes slideOut {
            0% {
                right: 20px;
            }
            100% {
                right: -100%; 
            }
        }
    </style>
</head>
<body>

<!-- Form for updating promotion -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="promotion-container">
                <h2 class="promotion-title">Update Promotion</h2>
                <form class="promotion-form" id="updateForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="form-group">
                        <label for="name">Promotion Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo $promotion['name']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <input type="text" class="form-control" id="description" name="description" value="<?php echo $promotion['description']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" class="form-control" id="price" name="price" value="<?php echo $promotion['price']; ?>" min="0" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $promotion['start_date']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $promotion['end_date']; ?>" required>
                    </div>
                    <input type="hidden" name="update_id" value="<?php echo $update_id; ?>">
                    <button type="submit" class="btn btn-block" name="update_promotion" onclick="return confirm('Are you sure you want to update this promotion?')">Update Promotion</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($message)) : ?>
<div class="alert-slide" id="alertSlide"><?php echo $message; ?></div>
<script>
    // Slide up the prompt message after 5 seconds
    setTimeout(function() {
        var alertSlide = document.getElementById('alertSlide');
        if (alertSlide && alertSlide.innerText.trim() !== '') {
            alertSlide.style.animation = 'slideOut 0.5s ease forwards';
            setTimeout(function() {
                alertSlide.style.display = 'none'; // Hide the prompt message after sliding up
            }, 500);
        }
    }, 5000);
</script>
<?php endif; ?>

<!-- Bootstrap JavaScript -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
