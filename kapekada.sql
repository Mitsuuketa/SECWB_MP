-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 14, 2024 at 09:11 AM
-- Server version: 8.0.34
-- PHP Version: 8.2.12
CREATE DATABASE IF NOT EXISTS `kapekada`;
USE `kapekada`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kapekada`
--

-- --------------------------------------------------------

--
-- Table structure for table `combo_meals`
--

CREATE TABLE `combo_meals` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `main_dish` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `side_dish` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `drink` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount_percentage` decimal(5,2) NOT NULL,
  `category` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `quantity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `combo_meals`
--

INSERT INTO `combo_meals` (`id`, `name`, `description`, `main_dish`, `side_dish`, `drink`, `price`, `discount_percentage`, `category`, `quantity`) VALUES
(1, 'Cheesy Potato Delight Combo', 'Enjoy the indulgent flavors of our Ultimate Cheese Bagel, paired with a Baked Potato, and complemented by an Iced Chocolate.', 'Ultimate Cheese Bagel', 'Baked Potato', 'Iced Chocolate', 250.00, 15.00, 'Morning', 47),
(2, 'Mediterranean Wedges Bliss Combo', 'Savor the Mediterranean taste with our Italian Grilled Bagel, accompanied by Steamed Vegetables, and served with refreshing Water.', 'Italian Grilled Bagel', 'Steamed Vegetables', 'Water', 300.00, 10.00, 'Evening', 50),
(3, 'Morning Energy Boost Combo', 'Start your day with energy! Our Morning Energy Boost Combo includes Pancake, Egg Toast, and a refreshing Coke.', 'Breakfast Wrap', 'Crisscut Fries', 'Chocolate Cream Frappuccino', 200.00, 10.00, 'Morning', 50),
(4, 'Evening Delight Combo', 'Experience an evening delight with our Grilled Chicken Wrap, served with crispy French Fries and a delightful Mocha Frappe.', 'Grilled Chicken Wrap', 'French Fries', 'Mocha Frappe', 280.00, 10.00, 'Evening', 50),
(5, 'Aprils Specials', 'Enjoy the specials', 'Meatball Pasta', 'Crisscut Fries', 'Iced Chocolate', 199.00, 10.00, 'Morning', 20);

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `email` varchar(50) NOT NULL,
  `attempt_count` int NOT NULL,
  `last_attempt_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `category` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `stock_quantity` int NOT NULL,
  `image` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `name`, `category`, `price`, `description`, `stock_quantity`, `image`) VALUES
(1, 'Ultimate Cheese Bagel', 'Mains', 180.00, 'A delicious bagel filled with a variety of cheeses.', 43, '660182133258c.webp'),
(2, 'Breakfast Wrap', 'Mains', 220.00, 'A hearty breakfast wrap filled with eggs, bacon, and cheese.', 48, '660182324c8d0.webp'),
(3, 'Italian Grilled Bagel', 'Mains', 200.00, 'Grilled bagel with Italian seasoning and cheese.', 50, '6601825024b2f.webp'),
(4, 'Meatball Pasta', 'Mains', 240.00, 'Pasta served with homemade meatballs and marinara sauce.', 50, '6601826233d7c.webp'),
(5, 'Baked Potato', 'Sides', 120.00, 'Baked potato served with butter and sour cream.', 49, '6601827671d9b.webp'),
(6, 'Potato Wedges', 'Sides', 150.00, 'Crispy potato wedges seasoned to perfection.', 50, '66018293ad169.webp'),
(7, 'Crisscut Fries', 'Sides', 200.00, 'Crisscut fries with a crispy exterior and fluffy interior.', 50, '660182aa45198.webp'),
(8, 'Onion Rings', 'Sides', 130.00, 'Golden-brown onion rings served with dipping sauce.', 50, '660182c147c4e.webp'),
(9, 'Iced Chocolate', 'Drink', 150.00, 'Refreshing iced chocolate beverage.', 49, '660182db60f94.webp'),
(10, 'Iced/Hot Caramel Macchiato', 'Drink', 180.00, 'A perfect blend of espresso, vanilla, and caramel.', 50, '660182f46917c.webp'),
(11, 'Chocolate Cream Frappuccino', 'Drink', 210.00, 'Creamy chocolate frappuccino topped with whipped cream.', 50, '6601831029d96.webp'),
(12, 'Bottled Water', 'Drink', 25.00, 'Chilled bottled water.', 40, '6601833ee4cd3.webp');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `order_date` datetime NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `quantity` int NOT NULL,
  `customer_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_date`, `total_price`, `discount_amount`, `quantity`, `customer_address`) VALUES
(9, 0, '2024-03-23 15:26:09', 25.00, 0.00, 1, '121'),
(10, 0, '2024-03-25 13:39:03', 180.00, 0.00, 1, 'PH'),
(11, 0, '2024-03-25 15:03:05', 450.00, 0.00, 3, 'here'),
(12, 0, '2024-04-08 09:57:52', 0.00, 0.00, 0, ''),
(13, 0, '2024-04-08 09:58:07', 180.00, 0.00, 1, ''),
(14, 0, '2024-04-08 09:58:48', 180.00, 0.00, 1, 'DLSU'),
(15, 0, '2024-04-08 10:08:27', 220.00, 0.00, 1, 'hhrr'),
(16, 0, '2024-04-08 11:32:10', 180.00, 0.00, 1, '1331'),
(17, 0, '2024-04-08 16:30:49', 430.00, 0.00, 2, ''),
(18, 0, '2024-04-08 16:40:11', 250.00, 0.00, 1, 'dad'),
(19, 0, '2024-04-08 16:45:42', 430.00, 0.00, 2, 'hhrr');

-- --------------------------------------------------------

--
-- Table structure for table `specials`
--

CREATE TABLE `specials` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `specials`
--

INSERT INTO `specials` (`id`, `name`, `description`, `price`, `start_date`, `end_date`) VALUES
(1, 'Happy Hour Special', 'Enjoy discounted prices on selected drinks during happy hour.', 199.99, '2024-03-18', '2024-03-31'),
(65, 'Weekend Brunch Deal', 'Indulge in our special brunch menu at a discounted price every weekend.', 299.99, '2024-03-20', '2024-03-26'),
(66, 'Lunch Combo Special', 'Get a combo meal for a great price during lunch hours on weekdays.', 399.49, '2024-03-21', '2024-03-25');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `fullname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `wallet` decimal(10,2) DEFAULT '0.00',
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` int NOT NULL,
  `profile_photo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `role`, `wallet`, `address`, `phone`, `profile_photo`) VALUES
(3, 'itprog', 'itprog@gmail.com', '$2y$10$ynNIJ2WkeYGA5TZF5mUyVux47XAr/cdjnVwr0ze.U.g3.hbIPrBmm', 'User', 18710.00, 'DLSU', 0, ''),
(4, 'Administrator', 'admin@example.com', 'admin', 'Administrator', 500.00, '123 Admin Street', 0, ''),
(5, 'hello', 'hello@gmail.com', '$2y$10$WpkQRu6uHxpxd9x3nK8J5uRwischimzZJewMUB8GigPJJDxqDFBy.', 'User', 99420.00, 'DLSU', 0, ''),
(6, 'DLSU', 'dlsu@gmail.com', '$2y$10$1HcxIQl9lsNCIyg7QbeodONG8fHdEavMA2ca/S8q5iIFxfFTdebce', 'User', 20000.00, 'dqdq', 0, ''),
(103, 'hello', 'hello@gmail.com', '$2y$10$X.sTDHfriiFLhc2JKmBmzOH6V.t7pNa.DUBaIRY7I.I6PmehYwDzy', 'User', 99999419.99, '231 STREET', 0, ''),
(110, 'Juan Tamad', 'juan@gmail.com', '$2y$10$prWtnBtHVWQ5qWLcA8XRBeMpLljS/SeLheD0R7bMpTKOoKoBmlDu.', 'User', 100.00, 'Juan City', 1231231231, 'uploads/666be6226462c_logo.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `specials`
--
ALTER TABLE `specials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `specials`
--
ALTER TABLE `specials`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
