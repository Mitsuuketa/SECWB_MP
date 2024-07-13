<?php
include 'session_config.php';
session_start();
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // Redirect to the login page
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customerName = htmlspecialchars($_POST['name']);
    $customerAddress = htmlspecialchars($_POST['address']);
    
    // If a new address is provided, use it; otherwise, use the existing user address
    if (!empty($_POST['new_address'])) {
        $customerAddress = htmlspecialchars($_POST['new_address']);
    }
    
    // Store user's name and address in the session
    $_SESSION['last_order_info'] = ['name' => $customerName, 'address' => $customerAddress];
    
    $totalPrice = 0;
    $totalQuantity = 0; // Initialize total quantity variable

    // Calculate total price for items and total quantity
    foreach ($_SESSION['cart']['items'] as $itemId => $details) {
        $itemPriceQuery = "SELECT price FROM menu_items WHERE id = $itemId";
        $itemPriceResult = mysqli_query($conn, $itemPriceQuery);
        if ($itemPriceResult && mysqli_num_rows($itemPriceResult) > 0) {
            $itemPrice = mysqli_fetch_assoc($itemPriceResult)['price'];
            $totalPrice += $itemPrice * $details['quantity'];
            $totalQuantity += $details['quantity']; // Accumulate total quantity
        }
    }

    // Calculate total price for combos and total quantity
    foreach ($_SESSION['cart']['combos'] as $comboId => $details) {
        $comboPriceQuery = "SELECT price FROM combo_meals WHERE id = $comboId";
        $comboPriceResult = mysqli_query($conn, $comboPriceQuery);
        if ($comboPriceResult && mysqli_num_rows($comboPriceResult) > 0) {
            $comboPrice = mysqli_fetch_assoc($comboPriceResult)['price'];
            $totalPrice += $comboPrice * $details['quantity'];
            $totalQuantity += $details['quantity']; // Accumulate total quantity
        }
    }

    // Check if user's wallet has sufficient balance
    $userEmail = $_SESSION['email'];
    $walletQuery = "SELECT wallet FROM users WHERE email = '$userEmail'";
    $walletResult = mysqli_query($conn, $walletQuery);
    if ($walletResult && mysqli_num_rows($walletResult) > 0) {
        $userWalletBefore = mysqli_fetch_assoc($walletResult)['wallet']; // User's wallet balance before payment
        if ($userWalletBefore < $totalPrice) {
            $message = "Insufficient balance in your wallet. Please add funds.";
            echo "<script>window.location.href='checkout.php?message=$message';</script>";
            exit;
        } else {
            // Deduct the total price from the user's wallet
            $updateWallet = "UPDATE users SET wallet = wallet - $totalPrice WHERE email = '$userEmail'";
            mysqli_query($conn, $updateWallet);

            // Update quantity in the database for items
            foreach ($_SESSION['cart']['items'] as $itemId => $details) {
                $updateItemQuery = "UPDATE menu_items SET stock_quantity = stock_quantity - {$details['quantity']} WHERE id = $itemId";
                mysqli_query($conn, $updateItemQuery);
            }

            // Update quantity in the database for combos
            foreach ($_SESSION['cart']['combos'] as $comboId => $details) {
                $updateComboQuery = "UPDATE combo_meals SET quantity = quantity - {$details['quantity']} WHERE id = $comboId";
                mysqli_query($conn, $updateComboQuery);
            }

            // Insert order into the orders table
            $orderDate = date('Y-m-d H:i:s');
            $discountAmount = 0; // Assuming no discount for now
            $insertOrderQuery = "INSERT INTO orders (user_id, order_date, total_price, discount_amount, quantity, customer_address) VALUES ('$userEmail', '$orderDate', '$totalPrice', '$discountAmount', '$totalQuantity', '$customerAddress')";
            mysqli_query($conn, $insertOrderQuery);

            $_SESSION['cart'] = []; // Clear the cart after processing
        }
    }

    // Get the updated wallet balance after deduction
    $walletResult = mysqli_query($conn, $walletQuery);
    $userWalletAfter = mysqli_fetch_assoc($walletResult)['wallet']; // User's wallet balance after payment

    $confirmationMessage = "Thank you, {$customerName}. Your order has been received and will be shipped to {$customerAddress}. Total payment: ₱" . number_format($totalPrice, 2);
} else {
    header('Location: checkout.php');
    exit;
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Processed - Kape-Kada Coffee Shop</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #F5F5DC; /* Beige background for a warm feel */
            color: #6B4F4E; /* Coffee brown text for contrast */
            font-family: 'Montserrat', sans-serif;
        }

        .container {
            background-color: #FFF;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }

        h2 {
            color: #A52A2A; /* Dark red for headings */
            margin-bottom: 20px;
        }

        p {
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: #A52A2A; /* Dark red for primary button */
            border-color: #A52A2A;
        }

        .btn-primary:hover {
            background-color: #8B0000; /* Darker red on hover */
            border-color: #8B0000;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>Payment Confirmation</h2>
    <div class="receipt">
        <p><strong>Thank you for your order!</strong></p>
        <p>Customer Name: <?php echo $customerName; ?></p>
        <p>Customer Address: <?php echo $customerAddress; ?></p>
        <p>Total Payment: ₱<?php echo number_format($totalPrice, 2); ?></p>
        <p>Payment Method: Wallet</p>
        <p>Wallet Balance Before Payment: ₱<?php echo number_format($userWalletBefore, 2); ?></p>
        <p>Wallet Balance After Payment: ₱<?php echo number_format($userWalletAfter, 2); ?></p>
    </div>
    <a href="menu.php" class="btn btn-primary">Continue Shopping</a>
</div>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
