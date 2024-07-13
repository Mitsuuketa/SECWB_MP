<?php
session_start();
// include 'navbar.php';
include 'db_connection.php';
include 'session_config.php';
// Check if there are items stored in the cookie
if(isset($_COOKIE['cart_data'])) {
    // Retrieve cart data from the cookie and unserialize it
    $cookieCartData = unserialize($_COOKIE['cart_data']);
    // Merge the cart data from the cookie with the session cart data
    $_SESSION['cart'] = array_merge($_SESSION['cart'], $cookieCartData);
    // Remove the cookie
    setcookie('cart_data', '', time() - 3600, "/"); // Set the expiration time to the past to delete the cookie
}

// Initialize total price
$totalPrice = 0;

// Fetch menu items from the database
$menuItemsQuery = "SELECT * FROM menu_items";
$menuItemsResult = mysqli_query($conn, $menuItemsQuery);
$menuItems = [];
if ($menuItemsResult) {
    while ($menuItem = mysqli_fetch_assoc($menuItemsResult)) {
        $menuItems[$menuItem['id']] = $menuItem;
    }
}

// Fetch combo meals from the database
$combosQuery = "SELECT * FROM combo_meals";
$combosResult = mysqli_query($conn, $combosQuery);
$combos = [];
if ($combosResult) {
    while ($combo = mysqli_fetch_assoc($combosResult)) {
        $combos[$combo['id']] = $combo;
    }
}

// Check if the user is authenticated
if (isset($_SESSION['email'])) {
    // Retrieve client's information from the session if available
    $userEmail = $_SESSION['email'];
    if (isset($_SESSION['client_info'][$userEmail])) {
        $clientInfo = $_SESSION['client_info'][$userEmail];
    } else {
        // Retrieve client's information from the database if available
        $clientInfoQuery = "SELECT * FROM users WHERE email = '$userEmail'";
        $clientInfoResult = mysqli_query($conn, $clientInfoQuery);
        if ($clientInfoResult && mysqli_num_rows($clientInfoResult) > 0) {
            $clientInfo = mysqli_fetch_assoc($clientInfoResult);
            // Store client info in session
            $_SESSION['client_info'][$userEmail] = $clientInfo;
        }
    }
} else {
    // If not authenticated, save the cart items in session
    if (isset($_SESSION['cart'])) {
        $_SESSION['cart_serialized'] = serialize($_SESSION['cart']);
    }
}

// Retrieve cart items from session if available
if (isset($_SESSION['cart_serialized'])) {
    $cartItems = unserialize($_SESSION['cart_serialized']);
    if ($cartItems === false) {
        // Handle unserialization error
        echo "Error: Unable to unserialize cart data.";
        $cartItems = []; // Initialize empty cart items
    }
}

// Populate the name and address fields if available in session
$clientName = isset($clientInfo['name']) ? htmlspecialchars($clientInfo['name']) : '';
$clientAddress = isset($clientInfo['address']) ? htmlspecialchars($clientInfo['address']) : '';
$newAddress = ''; // Initialize $newAddress variable

// Check if there is any stored name and address information from the user's last order
if (isset($_SESSION['last_order_info'])) {
    $lastOrderInfo = $_SESSION['last_order_info'];
    if (isset($lastOrderInfo['name'])) {
        $clientName = htmlspecialchars($lastOrderInfo['name']);
    }
    if (isset($lastOrderInfo['address'])) {
        $clientAddress = htmlspecialchars($lastOrderInfo['address']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Kape-Kada Coffee Shop</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css">
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
            margin-top: 80px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Checkout Summary</h2>
    <form action="process_payment.php" method="post">
        <div class="mb-3">
            <label for="name" class="form-label">Name:</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo $clientName; ?>" required>
        </div>
        
        <div class="mb-3">
            <label for="address" class="form-label">Address:</label>
            <select class="form-control" id="address" name="address">
                <option value="">Select an existing address or enter a new one...</option>
                <?php 
                // Display existing user address if available
                if($clientAddress !== '') {
                    echo '<option value="' . $clientAddress . '">' . $clientAddress . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="new_address" class="form-label">Or enter a new address:</label>
            <input type="text" class="form-control" id="new_address" name="new_address" value="<?php echo $newAddress; ?>">
        </div>
        <h4>Items in Your Cart:</h4>
        <?php
        // Initialize total price
        $totalPrice = 0;

        // Calculate total price for items
        if (isset($_SESSION['cart']['items'])) {
            foreach ($_SESSION['cart']['items'] as $itemId => $details) {
                $itemPrice = $menuItems[$itemId]['price']; // Get the price of the item
                $totalPrice += $itemPrice * $details['quantity']; // Multiply price by quantity and add to total
            }
        }

        // Calculate total price for combos
        if (isset($_SESSION['cart']['combos'])) {
            foreach ($_SESSION['cart']['combos'] as $comboId => $details) {
                $comboPrice = $combos[$comboId]['price']; // Get the price of the combo
                $totalPrice += $comboPrice * $details['quantity']; // Multiply price by quantity and add to total
            }
        }
        ?>
        <ul>
            <?php if (isset($_SESSION['cart']['items'])): ?>
                <?php foreach ($_SESSION['cart']['items'] as $itemId => $details): ?>
                    <li><?php echo htmlspecialchars($menuItems[$itemId]['name']) . " - Quantity: " . $details['quantity'] . " - Price: ₱" . number_format($menuItems[$itemId]['price'] * $details['quantity'], 2); ?></li>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['cart']['combos'])): ?>
                <?php foreach ($_SESSION['cart']['combos'] as $comboId => $details): ?>
                    <li><?php echo htmlspecialchars($combos[$comboId]['name']) . " - Quantity: " . $details['quantity'] . " - Price: ₱" . number_format($combos[$comboId]['price'] * $details['quantity'], 2); ?></li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
        <p><strong>Total Price: ₱<?php echo number_format($totalPrice, 2); ?></strong></p>
        <button type="submit" class="btn btn-primary">Pay</button>
    </form>
</div>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>