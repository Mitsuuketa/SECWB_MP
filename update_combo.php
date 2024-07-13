<?php
include 'session_config.php';
session_start(); // Start the session
include 'admin_navbar.php';
// Include database connection
include 'db_connection.php';

$message = ""; // Initialize the message variable
// Define low stock threshold
$low_stock_threshold = 10; // Define your threshold here

// Check if combo meal to update is selected
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_id'])) {
    $update_id = $_POST['update_id'];
    // Fetch combo meal data from the database
    $sql = "SELECT * FROM combo_meals WHERE id = '$update_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        $combo_meal = mysqli_fetch_assoc($result);
    } else {
        // Combo meal not found, redirect back to admin_combo.php
        $_SESSION['message'] = "Combo meal not found.";
        header("Location: admin_combo.php");
        exit();
    }
} else {
    // If update_id is not set, redirect back to admin_combo.php
    $_SESSION['message'] = "Invalid request to update combo meal.";
    header("Location: admin_combo.php");
    exit();
}

// Initialize form input values
$name = isset($_POST['name']) ? $_POST['name'] : $combo_meal['name'];
$description = isset($_POST['description']) ? $_POST['description'] : $combo_meal['description'];
$main_dish = isset($_POST['main_dish']) ? $_POST['main_dish'] : $combo_meal['main_dish'];
$side_dish = isset($_POST['side_dish']) ? $_POST['side_dish'] : $combo_meal['side_dish'];
$drink = isset($_POST['drink']) ? $_POST['drink'] : $combo_meal['drink'];
$price = isset($_POST['price']) ? $_POST['price'] : $combo_meal['price'];
$discount_percentage = isset($_POST['discount_percentage']) ? $_POST['discount_percentage'] : $combo_meal['discount_percentage'];
$category = isset($_POST['category']) ? $_POST['category'] : $combo_meal['category'];
$quantity = isset($_POST['quantity']) ? $_POST['quantity'] : $combo_meal['quantity']; // Add the quantity field here

// Fetch combo meals from database
$sql = "SELECT * FROM combo_meals";
$result = $conn->query($sql);
$combo_meals = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $combo_meals[] = $row;
    }
}

// Fetch menu items from database based on category
$menu_items = [];
$categories = ['Mains', 'Sides', 'Drink'];
foreach ($categories as $cat) {
    $sql = "SELECT * FROM menu_items WHERE category = '$cat'";
    $result = mysqli_query($conn, $sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $menu_items[$cat][] = $row;
        }
    }
}

// Check if form for updating combo meal is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_combo'])) {
    // Check if the $_POST keys are set before accessing them
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $main_dish = isset($_POST['main_dish']) ? $_POST['main_dish'] : '';
    $side_dish = isset($_POST['side_dish']) ? $_POST['side_dish'] : '';
    $drink = isset($_POST['drink']) ? $_POST['drink'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : '';
    $discount_percentage = isset($_POST['discount_percentage']) ? $_POST['discount_percentage'] : '';
    $category = isset($_POST['category']) ? $_POST['category'] : '';
    $update_id = isset($_POST['update_id']) ? $_POST['update_id'] : '';
    $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : ''; // Added

    // Update combo meal in the database
    $sql = "UPDATE combo_meals SET name='$name', description='$description', main_dish='$main_dish', side_dish='$side_dish', drink='$drink', price='$price', discount_percentage='$discount_percentage', category='$category', quantity='$quantity' WHERE id='$update_id'"; // Added
    // Check if the stock quantity is lower than the threshold
    if ($quantity < $low_stock_threshold) {
        // Set the alert message
        $message = "Alert: Stock quantity is below the threshold.";
    } else {
        // Update combo meal in the database
        $sql = "UPDATE combo_meals SET name='$name', description='$description', main_dish='$main_dish', side_dish='$side_dish', drink='$drink', price='$price', discount_percentage='$discount_percentage', category='$category', quantity='$quantity' WHERE id='$update_id'"; // Added
        if (mysqli_query($conn, $sql)) {
            // Set the success message
            $message = "Combo meal details have been successfully updated. You will now be redirected to the combo meal page.";
            // Redirect back to admin_combo.php after 4 seconds
            echo '<meta http-equiv="refresh" content="4;url=admin_combo.php">';
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
    <title>Update Combo Meal - Kape-Kada Coffee Shop</title>
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

<!-- Form for updating combo meal -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="promotion-container">
                <h2 class="promotion-title">Update Combo Meal</h2>
                <form class="promotion-form" id="updateForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="form-group">
                        <label for="name">Combo Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" required><?php echo $description; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="main_dish">Main Dish</label>
                        <select class="form-control" id="main_dish" name="main_dish" required>
                            <option value="">Select Main Dish</option>
                            <?php
                            foreach ($menu_items['Mains'] as $item) {
                                echo "<option value='" . $item['name'] . "'";
                                if ($main_dish == $item['name']) {
                                    echo " selected";
                                }
                                echo ">" . $item['name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="side_dish">Side Dish</label>
                        <select class="form-control" id="side_dish" name="side_dish" required>
                            <option value="">Select Side Dish</option>
                            <?php
                            foreach ($menu_items['Sides'] as $item) {
                                echo "<option value='" . $item['name'] . "'";
                                if ($side_dish == $item['name']) {
                                    echo " selected";
                                }
                                echo ">" . $item['name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="drink">Drink</label>
                        <select class="form-control" id="drink" name="drink" required>
                            <option value="">Select Drink</option>
                            <?php
                            foreach ($menu_items['Drink'] as $item) {
                                echo "<option value='" . $item['name'] . "'";
                                if ($drink == $item['name']) {
                                    echo " selected";
                                }
                                echo ">" . $item['name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" class="form-control" id="price" name="price" value="<?php echo $price; ?>" min="0" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" value="<?php echo $quantity; ?>" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="discount_percentage">Discount Percentage</label>
                        <input type="number" class="form-control" id="discount_percentage" name="discount_percentage" value="<?php echo $discount_percentage; ?>" min="0" max="100" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select class="form-control" id="category" name="category" required>
                            <option value="Morning" <?php if($category == 'Morning') echo 'selected'; ?>>Morning</option>
                            <option value="Evening" <?php if($category == 'Evening') echo 'selected'; ?>>Evening</option>
                        </select>
                    </div>
                    <input type="hidden" name="update_id" value="<?php echo $update_id; ?>">
                    <button type="submit" class="btn btn-block" name="update_combo" onclick="return confirm('Are you sure you want to update this combo meal?')">Update Combo Meal</button>
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

<!-- Include necessary JavaScript -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
