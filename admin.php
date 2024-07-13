<?php 
include 'admin_navbar.php'; 
include 'db_connection.php';
include 'session_config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Specials - Kape-Kada Coffee Shop</title>
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
        .logo {
            width: 100px; /* Adjust size as needed */
            height: auto;
            opacity: 0.9; /* Adjust for desired transparency level */
        }
        /* White container style */
        .white-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 150px;
            color: #6B4F4E;
            font-family: 'Montserrat', sans-serif;
            display: flex; /* Change display to flex */
            flex-direction: column; /* Align children vertically */
            align-items: center; /* Center children horizontally */
            max-width: 700px; /* Set maximum width */
            margin-left: auto; /* Auto margin left and right to center horizontally */
            margin-right: auto;
        }
        .white-container h1 {
            color: #A52A2A; /* Rich brown color for titles */
            font-weight: 700;
            margin-bottom: 18px;
        }
    </style>
</head>
<body>
<div class="white-container">
    <h1>Welcome to Admin Dashboard!</h1>
    <p>This is the admin panel of Kape-Kada Coffee Shop. You can manage menu items, promotions, combo meals, view orders, and more from here.</p>
</div>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>