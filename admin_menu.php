<?php
include 'session_config.php';
session_start(); // Start the session
include 'admin_navbar.php';
// Include database connection
include 'db_connection.php';


$message = ""; // Initialize the message variable

// Define low stock threshold
$low_stock_threshold = 10; // Define your threshold here


function logAction($action, $details) {
    $logFile = 'C:/xampp/htdocs/SECWB_MP/app.log';
    $timestamp = date('[Y-m-d H:i:s]');

    $logMessage = "$timestamp [$action] $details" . PHP_EOL;

    if (!is_writable(dirname($logFile))) {
        error_log("Directory is not writable: " . dirname($logFile));
        return;
    }

    if (file_exists($logFile) && !is_writable($logFile)) {
        error_log("File is not writable: " . $logFile);
        return;
    }

    if (file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX) === false) {
        error_log("Failed to write to log file: " . $logFile);
    }
}

// Log access to the menu page
logAction('Access', 'Admin accessed the menu page');


// Check if form for creating menu items is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_item'])) {
    // Check if the $_POST keys are set before accessing them
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : '';
    $category = isset($_POST['category']) ? $_POST['category'] : '';
    $stock_quantity = isset($_POST['stock_quantity']) ? $_POST['stock_quantity'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';

    // Image upload handling
    $fileName = $_FILES["image"]["name"];
    $fileSize = $_FILES["image"]["size"];
    $tmpName = $_FILES["image"]["tmp_name"];
    $validImageExtension = ['jpg', 'jpeg', 'png', 'webp'];
    $imageExtension = explode('.', $fileName);
    $imageExtension = strtolower(end($imageExtension));
    
    if ($_FILES["image"]["error"] != 4) { // Check if an image is uploaded
        if (!in_array($imageExtension, $validImageExtension)) {
            $message = "Error: Invalid Image Extension";
        } elseif ($fileSize > 1000000) {
            $message = "Error: Image Size Is Too Large";
        } else {
            $newImageName = uniqid() . '.' . $imageExtension;
            move_uploaded_file($tmpName, 'D:/xampp/htdocs/KapeKadaCoffeeShop/images/' . $newImageName); // Move uploaded image to image directory

            // Insert menu item into database along with image filename
            $sql = "INSERT INTO menu_items (name, price, category, description, stock_quantity, image) 
                    VALUES ('$name', '$price', '$category', '$description', '$stock_quantity', '$newImageName')";
            
            if (mysqli_query($conn, $sql)) {
                // Set the success message
                $message = "Menu item created successfully.";
                // Refresh after 2 seconds
                echo '<meta http-equiv="refresh" content="2;url=admin_menu.php">';
                logAction('Create', "Admin created a menu item: $menuName with price $menuPrice");
            } else {
                $message = "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        }
    } else {
        $message = "Error: Image Does Not Exist";
    }
}

// Check if delete button is clicked
if(isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    // Delete menu item from database
    $delete_sql = "DELETE FROM menu_items WHERE id = '$delete_id'";
    if (mysqli_query($conn, $delete_sql)) {
        $message = "Menu item successfully deleted.";
        // Refresh after 2 seconds
        echo '<meta http-equiv="refresh" content="2;url=admin_menu.php">';
        logAction('Delete', "Admin deleted menu item ID: $menuId");
    } else {
        $message = "Error deleting menu item: " . mysqli_error($conn);
    }
}

// Fetch menu items from database
$sql = "SELECT * FROM menu_items";
$result = mysqli_query($conn, $sql);
$menu_items = [];
$low_stock_alerts = []; // Array to store low stock alerts
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $menu_items[] = $row;
        // Check if stock quantity is below the threshold
        if ($row['stock_quantity'] < $low_stock_threshold) {
            // Store low stock alerts in an array
            $low_stock_alerts[] = $row['name'];
        }
    }
}

// Concatenate low stock alerts into a single message
if (!empty($low_stock_alerts)) {
    $message = "Low stock alerts: " . implode(", ", $low_stock_alerts) . " stock quantity is below the threshold.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Item Management - Your Restaurant Name</title>
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
        }
        .promotion-container {
            background-color: #FFFFFF; /* White container background */
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Soft shadow */
        }
        .promotion-title {
            color: #A52A2A; /* Rich brown color for titles */
            font-weight: 700;
            margin-bottom: 30px;
        }
        .promotion-form input[type="text"],
        .promotion-form input[type="number"],
        .promotion-form select {
            margin-bottom: 20px;
            border: 1px solid #ccc; /* Light border */
            border-radius: 5px;
            padding: 8px;
            width: 100%;
        }
        .promotion-form button[type="submit"] {
            background-color: #A52A2A; /* Rich brown color for buttons */
            color: #FFFFFF; /* White text color */
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
        }
        .promotion-form button[type="submit"]:hover {
            background-color: #8B4513; /* Darker shade on hover */
        }
        .promotion-list h3 {
            margin-top: 40px; 
            margin-bottom: 15px;
        } 
        .promotion-list .promotion-item {
            background-color: #F9F9F9; /* Light gray item background */
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .promotion-list .promotion-item h4 {
            color: #A52A2A; /* Rich brown color for titles */
            font-weight: 700;
            margin-bottom: 5px;
        }
        .promotion-list .promotion-item p {
            margin-bottom: 5px;
        }
        .promotion-buttons {
            display: flex;
        }
        .promotion-buttons .delete-btn,
        .promotion-buttons .update-btn {
            margin-right: 10px;
        }
        .promotion-buttons .delete-btn {
            background-color: #A52A2A; /* Rich brown color for buttons */
            color: #FFFFFF; /* White text color */
        }
        .promotion-buttons .update-btn {
            background-color: #A52A2A; /* Rich brown color for buttons */
            color: #FFFFFF; /* White text color */
        }
        .alert-slide {
            position: fixed;
            top: 170px;
            right: 20px;
            z-index: 9999;
            background-color: #A52A2A; /* Rich brown color for alerts */
            color: #FFFFFF; /* White text color */
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

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Modified form for creating menu items -->
            <div class="promotion-container">
                <h2 class="promotion-title">Menu Item Management</h2>
                <form class="promotion-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" class="form-control" id="price" name="price" min="0.01" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select class="form-control" id="category" name="category" required>
                            <option value="Mains">Mains</option>
                            <option value="Sides">Sides</option>
                            <option value="Drink">Drink</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="stock_quantity">Stock Quantity</label>
                        <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="image">Image</label>
                        <input type="file" class="form-control-file" id="image" name="image" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-block" name="create_item">Create Menu Item</button>
                </form>
            </div>
            <!-- Display existing menu items -->
            <div class="promotion-list">
                <h3 class="promotion-title">Current Menu Items</h3>
                <?php if (!empty($menu_items)) : ?>
                    <?php foreach ($menu_items as $key => $menu_item) : ?>
                        <div class="promotion-item" id="item_<?php echo $key; ?>">
                            <h4><?php echo $menu_item['name']; ?></h4>
                            <p>Category: <?php echo $menu_item['category']; ?></p>
                            <p>Price: ₱<?php echo number_format($menu_item['price'], 2); ?></p>
                            <p>Description: <?php echo $menu_item['description']; ?></p>
                            <p>Stock Quantity: <?php echo $menu_item['stock_quantity']; ?></p>
                            <img src="images/<?php echo $menu_item['image']; ?>" alt="Menu Item Image" width = 200>
                            <br><br>
                            <div class="promotion-buttons">
                                <form method="post" action="update_menu.php">
                                    <input type="hidden" name="update_id" value="<?php echo $menu_item['id']; ?>">
                                    <button type="submit" class="btn btn-primary update-btn">Update</button>
                                </form>
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <input type="hidden" name="delete_id" value="<?php echo $menu_item['id']; ?>">
                                    <button type="submit" class="btn btn-danger delete-btn" onclick="return confirm('Are you sure you want to delete this menu item?')">Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p>No menu items available.</p>
                <?php endif; ?>
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