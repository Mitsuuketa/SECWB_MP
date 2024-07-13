<?php
include 'session_config.php';
session_start(); // Start the session
include 'admin_navbar.php';
// Include database connection
include 'db_connection.php';

$message = ""; // Initialize the message variable

// Define low stock threshold
$low_stock_threshold = 10; // Define your threshold here

// Check if menu item to update is selected
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_id'])) {
    $update_id = $_POST['update_id'];
    // Fetch menu item data from the database
    $sql = "SELECT * FROM menu_items WHERE id = '$update_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        $menu_item = mysqli_fetch_assoc($result);
    } else {
        // Menu item not found, redirect back to admin_menu.php
        $_SESSION['message'] = "Menu item not found.";
        header("Location: admin_menu.php");
        exit();
    }
} else {
    // If update_id is not set, redirect back to admin_menu.php
    $_SESSION['message'] = "Invalid request to update menu item.";
    header("Location: admin_menu.php");
    exit();
}

// Initialize form input values
$name = isset($_POST['name']) ? $_POST['name'] : $menu_item['name'];
$price = isset($_POST['price']) ? $_POST['price'] : $menu_item['price'];
$category = isset($_POST['category']) ? $_POST['category'] : $menu_item['category'];
$stock_quantity = isset($_POST['stock_quantity']) ? $_POST['stock_quantity'] : $menu_item['stock_quantity'];
$description = isset($_POST['description']) ? $_POST['description'] : $menu_item['description'];

// Check if form for updating menu item is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_menu'])) {
    // Check if the $_POST keys are set before accessing them
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : '';
    $category = isset($_POST['category']) ? $_POST['category'] : '';
    $stock_quantity = isset($_POST['stock_quantity']) ? $_POST['stock_quantity'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $update_id = isset($_POST['update_id']) ? $_POST['update_id'] : '';

    $fileName = $_FILES["image"]["name"];
    $fileSize = $_FILES["image"]["size"];
    $tmpName = $_FILES["image"]["tmp_name"];
    $validImageExtension = ['jpg', 'jpeg', 'png', 'webp'];
    $imageExtension = explode('.', $fileName);
    $imageExtension = strtolower(end($imageExtension));

    // Check if the stock quantity is lower than the threshold
    if ($stock_quantity < $low_stock_threshold) {
        // Set the alert message
        $message = "Alert: Stock quantity is below the threshold.";
    } else {
        if ($_FILES["image"]["error"] != 4) { // Check if an image is uploaded
            if (!in_array($imageExtension, $validImageExtension)) {
                $message = "Error: Invalid Image Extension";
            } elseif ($fileSize > 1000000) {
                $message = "Error: Image Size Is Too Large";
            } else {
                $newImageName = uniqid() . '.' . $imageExtension;
                move_uploaded_file($tmpName, 'D:/xampp/htdocs/KapeKadaCoffeeShop/images/' . $newImageName); // Move uploaded image to image directory

                // Update menu item in the database along with image filename
                $sql = "UPDATE menu_items SET name='$name', price='$price', category='$category', description='$description', stock_quantity='$stock_quantity', image='$newImageName' WHERE id='$update_id'";
                if (mysqli_query($conn, $sql)) {
                    // Set the success message
                    $message = "Menu item details have been successfully updated. You will now be redirected to the menu item page.";
                    // Redirect back to admin_menu.php after 4 seconds
                    echo '<meta http-equiv="refresh" content="4;url=admin_menu.php">';
                } else {
                    $message = "Error: " . $sql . "<br>" . mysqli_error($conn);
                }
            }
        } else {
            // If no new image is uploaded, update other details without changing the image
            $sql = "UPDATE menu_items SET name='$name', price='$price', category='$category', description='$description', stock_quantity='$stock_quantity' WHERE id='$update_id'";
            if (mysqli_query($conn, $sql)) {
            // Set the success message
                $message = "Menu item details have been successfully updated. You will now be redirected to the menu item page.";
            // Redirect back to admin_menu.php after 4 seconds
                echo '<meta http-equiv="refresh" content="4;url=admin_menu.php">';
            } else {
                $message = "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        }
    }
}

// Check if form for uploading XML is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload_xml'])) {
    // Check if the uploaded XML file is valid
    if ($_FILES['xml_file']['error'] == UPLOAD_ERR_OK && $_FILES['xml_file']['tmp_name'] != "") {
        // Check if uploaded file is XML
        $xmlFileType = strtolower(pathinfo($_FILES['xml_file']['name'], PATHINFO_EXTENSION));
        if ($xmlFileType == 'xml') {
            // Read XML file
            $xmlData = simplexml_load_file($_FILES['xml_file']['tmp_name']);
            if ($xmlData !== false) {
                // Process XML data and insert menu items into database
                foreach ($xmlData->menu_item as $menuItem) {
                    // Extract menu item details
                    $name = $menuItem->name;
                    $price = $menuItem->price;
                    $category = $menuItem->category;
                    $stock_quantity = $menuItem->stock_quantity;
                    $description = $menuItem->description;

                    // Insert menu item into database
                    $sql = "INSERT INTO menu_items (name, price, category, description, stock_quantity) VALUES ('$name', '$price', '$category', '$description', '$stock_quantity')";
                    if (mysqli_query($conn, $sql)) {
                        $message .= "Menu item '$name' added successfully.<br>";
                        echo '<meta http-equiv="refresh" content="4;url=admin_menu.php">';
                    } else {
                        $message .= "Error adding menu item '$name'.<br>";
                    }
                }
            } else {
                $message = "Error: Invalid XML file.";
            }
        } else {
            $message = "Error: Please upload a valid XML file.";
        }
    } else {
        $message = "Error uploading XML file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Menu Item - Your Restaurant Name</title>
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
        .promotion-form select,
        .promotion-form textarea {
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

<!-- Main content -->
<div class="container">
    <!-- Menu item update form -->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="promotion-container">
                <h2 class="promotion-title">Update Menu Item</h2>
                <form class="promotion-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" class="form-control" id="price" name="price" min="0.01" step="0.01" value="<?php echo $price; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select class="form-control" id="category" name="category" required>
                            <option value="Mains" <?php if($category == 'Mains') echo 'selected'; ?>>Mains</option>
                            <option value="Sides" <?php if($category == 'Sides') echo 'selected'; ?>>Sides</option>
                            <option value="Drink" <?php if($category == 'Drink') echo 'selected'; ?>>Drink</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required><?php echo $description; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="stock_quantity">Stock Quantity</label>
                        <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" min="0" value="<?php echo $stock_quantity; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="image">Image</label>
                        <input type="file" class="form-control-file" id="image" name="image" accept="image/*" required>
                    </div>
                    <input type="hidden" name="update_id" value="<?php echo $update_id; ?>">
                    <button type="submit" class="btn btn-block" name="update_menu" onclick="return confirm('Are you sure you want to update this combo meal?')">Update Menu Item</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- XML Upload Form -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="promotion-container">
                <h2 class="promotion-title">Upload XML for Bulk Menu Item Addition</h2>
                <form class="promotion-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="xml_file">Select XML File:</label>
                        <input type="file" class="form-control-file" id="xml_file" name="xml_file" accept=".xml" required>
                    </div>
                    <button type="submit" class="btn btn-block" name="upload_xml">Upload XML</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($message)) : ?>
<!-- Alert message -->
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