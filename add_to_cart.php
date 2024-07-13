<?php
include 'session_config.php';

session_start(); // Start the session to use session variables

// Ensure the cart structure can accommodate both items and combos
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = ['items' => [], 'combos' => []];
}

$type = $_POST['type'] ?? 'item'; // Determine if the POST request is for an item or a combo
$itemId = $_POST['itemId'] ?? ''; // This could be an item ID or a combo ID depending on the type
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

// Simple validation
if ($itemId && $quantity > 0) {
    if ($type === 'combo') {
        // Handle adding/updating a combo in the cart
        if (isset($_SESSION['cart']['combos'][$itemId])) {
            // Update quantity if the combo already exists
            $_SESSION['cart']['combos'][$itemId]['quantity'] += $quantity;
        } else {
            // Add the new combo to the cart
            $_SESSION['cart']['combos'][$itemId] = ['quantity' => $quantity];
        }
    } else {
        // Handle adding/updating an item in the cart
        if (isset($_SESSION['cart']['items'][$itemId])) {
            // Update quantity if the item exists
            $_SESSION['cart']['items'][$itemId]['quantity'] += $quantity;
        } else {
            // Add the new item to the cart
            $_SESSION['cart']['items'][$itemId] = ['quantity' => $quantity];
        }
    }

    // Redirect to the cart page with a success message
    header('Location: cart.php?status=success');
} else {
    // Redirect back with an error message if validation fails
    $redirectUrl = $type === 'combo' ? 'combo_detail.php?id=' . $itemId : 'item_detail.php?id=' . $itemId;
    header("Location: $redirectUrl&status=error");
}
?>
