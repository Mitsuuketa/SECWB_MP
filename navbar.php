<?php

session_start(); // Start the session

// Include database connection
include 'db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kape-Kada Coffee Shop</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Google Font - Playfair Display -->
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan&display=swap" rel="stylesheet">
    <style>
        body {
            padding-top: 46px; /* Ensure body content does not overlap navbar */
            color: #FFFFFF; /* Set default text color to white */
        }
        .navbar {
            background-color: #A0522D; /* Rich coffee brown background */
            transition: background-color 0.5s ease; /* Smooth transition for background color */
        }
        .navbar-brand {
            color: #FFFFFF; /* White color for the title */
            font-family: 'League Spartan', sans-serif;
            font-weight: bold; /* Make the title bold */
            transition: transform 0.5s ease, color 0.3s ease; /* Smooth logo scaling and color transition on hover */
        }
        .navbar-brand img {
            transition: transform 0.5s ease; /* Smooth logo scaling on hover */
        }
        .navbar-brand:hover img {
            transform: scale(1.1); /* Slightly enlarge logo on hover */
        }
        .navbar-brand:hover {
            color: #000000; /* Black color for the title on hover */
        }
        .nav-link {
            color: #FFFFFF !important; /* White color for links */
            transition: color 0.3s ease; /* Smooth color transition on hover */
        }
        .nav-link:hover {
            color: #FFDEAD !important; /* Lighter shade on hover */
        }
        .navbar-toggler {
            border-color: #FFF8DC; /* Make toggler visible against dark background */
        }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='%23FFF8DC' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
        }
        /* Style for the dropdown login */
        .dropdown-menu {
            background-color: #A0522D; /* Background color */
            border: none; /* Remove border */
            padding: 5px; /* Add padding */
        }
        .dropdown-menu-login {
            left: auto !important;
            right: 0;
        }
        .dropdown-item {
            color: #A52A2A !important;
            transition: color 0.3s ease; /* Smooth color transition on hover */
        }
        .dropdown-item:hover {
            color: blue !important; /* Lighter shade on hover */
            background-color: transparent !important; /* Remove background color on hover */
        }
        .nav-link {
            font-family: 'League Spartan', sans-serif; /* Apply League Spartan font */
            color: #FFFFFF !important; /* White color for links */
            transition: color 0.3s ease; /* Smooth color transition on hover */
            font-size: 18px;
        }
        .nav-link,
        .dropdown-item {
            font-family: 'League Spartan', sans-serif; /* Apply League Spartan font */
            font-size: 18px; /* Set the font size to 20 pixels */
            color: solid black !important; /* White color for links */
            transition: color 0.3s ease; /* Smooth color transition on hover */
        }

    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top">
    <a class="navbar-brand" href="index.php">
        <img src="logo.png" alt="Kape-Kada Coffee Shop" style="width: 120px; height: auto;"> Kape-Kada Coffee Shop
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item active">
                <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="menu.php">Menu</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="combos.php">Combo Deals</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="checkout.php">Checkout</a>
            </li>
            <?php if (isset($_SESSION['fullname'])) : ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo $_SESSION['fullname']; ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-login" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="profile.php">Profile</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="wallet.php">Wallet</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php">Logout</a>
                    </div>
                </li>
            <?php else : ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Login
                    </a>
                    <div class="dropdown-menu dropdown-menu-login" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="login.php">Login</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="signup.php">Sign Up</a>
                    </div>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<!-- Your page content here -->

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<!-- Bootstrap JavaScript -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- Bootstrap Bundle with Popper.js -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
