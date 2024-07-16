<?php 
session_start(); // Start the session

// Include database connection
include 'db_connection.php';
include 'navbar.php';

// Fetch specials from the database
$sql = "SELECT * FROM specials";
$result = mysqli_query($conn, $sql);

// Fetch reviews from the database
$reviews_sql = "SELECT r.*, u.fullname FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.special_id = ?";
$stmt = $conn->prepare($reviews_sql);

// Check if there are any specials
if (mysqli_num_rows($result) > 0) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Special Promotions - Kape-Kada Coffee Shop</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-image: url('bg.svg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed; 
            background-position: center;
            margin-top: 120px;
        }
        .navbar {
            font-family: 'Merriweather', serif;
        }
        .promotions-section {
            padding: 60px 0;
        }
        .promotion-item {
            background-color: #FFF;
            padding: 30px;
            margin-bottom: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }
        .promotion-item:hover {
            transform: scale(1.1); /* Scale up on hover */
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2); /* Increase shadow on hover */
        }
        .promotion-title {
            color: #1e1e1e;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .promotion-price {
            color: #cc0000;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .promotion-description {
            color: #555;
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 15px;
        }

        .promotion-duration {
            color: #777;
            font-size: 14px;
            font-style: italic;
        }
        h1.text-center {
            color: white; /* Set text color to white */
            font-size: 36px; /* Increase font size */
            font-weight: bold; /* Make font bold */
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5); /* Add text shadow for depth */
            margin-bottom: 30px; /* Add bottom margin for spacing */
            letter-spacing: 1px; /* Increase letter spacing for readability */
            text-transform: uppercase; /* Convert text to uppercase */
        }
        .banner { 
            margin: 30px 0; 
        }
        
        .slider-container {
            display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -webkit-align-items: center;
            -ms-flex-align: center;
                align-items: center;
        gap: 10px;
        -webkit-border-radius: var(--border-radius-md);
                border-radius: var(--border-radius-md);
        overflow: auto hidden;
        -webkit-scroll-snap-type: inline mandatory;
            -ms-scroll-snap-type: inline mandatory;
                scroll-snap-type: inline mandatory;
        overscroll-behavior-inline: contain;
        }
        
        .slider-item {
            position: relative;
            min-width: 100%;
            max-height: 450px;
            aspect-ratio: 1 / 1;
            -webkit-border-radius: var(--border-radius-md);
                    border-radius: var(--border-radius-md);
            overflow: hidden;
            scroll-snap-align: start;
        }
        
        .slider-item .banner-img {
            width: 100%;
            height: 100%;
            -o-object-fit: cover;
                object-fit: cover;
            -o-object-position: right;
                object-position: right;
                border-radius: 5px;
        }
        
        .banner-content {
            background: hsla(0, 0%, 100%, 0.8);
            position: absolute;
            bottom: 25px;
            left: 25px;
            right: 25px;
            padding: 20px 25px;
            -webkit-border-radius: var(--border-radius-md);
                    border-radius: var(--border-radius-md);
        }

        .banner-btn {
            background: var(--malibec);
            color: var(--white);
            width: -webkit-max-content;
            width: -moz-max-content;
            width: max-content;
            font-size: var(--fs-11);
            font-weight: var(--weight-600);
            text-transform: uppercase;
            padding: 4px 10px;
            -webkit-border-radius: var(--border-radius-sm);
                    border-radius: var(--border-radius-sm);
            -webkit-transition: var(--transition-timing);
            -o-transition: var(--transition-timing);
            transition: var(--transition-timing);
            z-index: 1;
        }

        .banner-btn:hover { background: var(--eerie-black); }
        
        .about-section {
            background-color: #F9F5F1; /* Light beige background */
            padding: 60px 0;
        }

        .about-heading {
            text-align: center;
            margin-bottom: 40px;
            color: #6B4F4E; /* Coffee brown text */
            font-family: 'Merriweather', serif;
            font-size: 32px;
        }

        .about-content {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
            color: #6B4F4E; /* Coffee brown text */
            font-family: 'Montserrat', sans-serif;
            font-size: 18px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
<div class="banner">
    <div class="container">
        <div class="slider-container has-scrollbar">
            <div class="slider-item">
                <img src="banner.svg" alt="bg1 banner" class="banner-img">
            </div>
            <div class="slider-item">
                <img src="banner2.svg" alt="bg2 banner" class="banner-img">
            </div>
        </div>
    </div>
</div>
<div class="container promotions-section">
    <h1 class="text-center">This Season's Specials</h1>
    <?php
    // Loop through each promotion and display it
    while ($row = mysqli_fetch_assoc($result)) {
    ?>
    <div class="promotion-item">
        <h2 class="promotion-title"><?php echo $row['name']; ?></h2>
        <p class="promotion-price">₱<?php echo number_format($row['price'], 2); ?></p>
        <p class="promotion-description"><?php echo $row['description']; ?></p>
        <p class="promotion-duration">Duration: <?php echo $row['start_date']; ?> to <?php echo $row['end_date']; ?></p>
        
        <!-- Review Section -->
        <div class="reviews-section">
            <h3>Reviews</h3>
            <?php
            $stmt->bind_param('i', $row['id']);
            $stmt->execute();
            $reviews_result = $stmt->get_result();

            if (mysqli_num_rows($reviews_result) > 0) {
                while ($review = mysqli_fetch_assoc($reviews_result)) {
                    ?>
                    <div class="review">
                        <h4><?php echo $review['fullname']; ?></h4>
                        <p>Rating: <?php echo $review['rating']; ?>/5</p>
                        <p><?php echo $review['comment']; ?></p>
                        <?php if ($review['user_id'] == $_SESSION['user_id']) { ?>
                            <!-- Edit Form -->
                            <form action="edit_review.php" method="post">
                                <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                <div class="form-group">
                                    <label for="rating">Rating:</label>
                                    <select name="rating" id="rating" class="form-control" required>
                                        <option value="1" <?php echo ($review['rating'] == 1) ? 'selected' : ''; ?>>1</option>
                                        <option value="2" <?php echo ($review['rating'] == 2) ? 'selected' : ''; ?>>2</option>
                                        <option value="3" <?php echo ($review['rating'] == 3) ? 'selected' : ''; ?>>3</option>
                                        <option value="4" <?php echo ($review['rating'] == 4) ? 'selected' : ''; ?>>4</option>
                                        <option value="5" <?php echo ($review['rating'] == 5) ? 'selected' : ''; ?>>5</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="comment">Comment:</label>
                                    <textarea name="comment" id="comment" class="form-control" rows="3" required><?php echo $review['comment']; ?></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Review</button>
                            </form>
                        <?php } ?>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No reviews yet. Be the first to review!</p>";
            }
            ?>

            <!-- Review Form -->
            <?php if (isset($_SESSION['user_id'])) { ?>
            <form action="post_review.php" method="post">
                <input type="hidden" name="special_id" value="<?php echo $row['id']; ?>">
                <div class="form-group">
                    <label for="rating">Rating:</label>
                    <select name="rating" id="rating" class="form-control" required>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="comment">Comment:</label>
                    <textarea name="comment" id="comment" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Post Review</button>
            </form>
            <?php } else { ?>
            <p><a href="login.php">Log in</a> to post a review.</p>
            <?php } ?>
        </div>
    </div>
    <?php
    }
    ?>
</div>
<div class="about-section">
    <div class="container">
        <h2 class="about-heading">About Us</h2>
        <div class="about-content">
            <p>Welcome to Kape-Kada Coffee Shop, your go-to place for delicious coffee and cozy ambiance. At Kape-Kada, we believe in creating a warm and inviting space where friends can gather, conversations flow, and memories are made over a perfect cup of coffee.</p>
            <p>Our journey began with a passion for crafting exceptional coffee experiences. From carefully sourced beans to expertly brewed blends, each cup is a testament to our commitment to quality and flavor. But Kape-Kada is more than just a coffee shop; it's a community hub where people come together to unwind, connect, and savor the simple joys of life.</p>
            <p>Whether you're seeking a peaceful moment alone or catching up with friends, we invite you to join us at Kape-Kada and experience the magic of great coffee and genuine hospitality.</p>
        </div>
    </div>
</div>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
} else {
    echo "No specials available at the moment.";
}
?>
