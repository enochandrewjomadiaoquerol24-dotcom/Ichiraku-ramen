-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 15, 2025 at 05:55 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ichikaru_ramen_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category_name`) VALUES
(1, 'Ramen'),
(2, 'Drinks');

-- --------------------------------------------------------

--
-- Table structure for table `customers_info`
--

CREATE TABLE `customers_info` (
  `customer_id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_address` varchar(500) NOT NULL,
  `contact_number` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers_info`
--

INSERT INTO `customers_info` (`customer_id`, `customer_name`, `customer_address`, `contact_number`) VALUES
(1, 'Admin Owner', 'Ichiraku HQ', '0000000000'),
(2, 'Naruto', 'Not Set', '09611785115');

-- --------------------------------------------------------

--
-- Table structure for table `delivery`
--

CREATE TABLE `delivery` (
  `delivery_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `rider_id` int(11) DEFAULT 0,
  `delivery_date` datetime DEFAULT NULL,
  `delivery_status_id` int(11) NOT NULL DEFAULT 1,
  `delivery_fee` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `delivery`
--

INSERT INTO `delivery` (`delivery_id`, `order_id`, `rider_id`, `delivery_date`, `delivery_status_id`, `delivery_fee`) VALUES
(1, 1, 0, NULL, 5, 0.00),
(2, 2, 0, NULL, 5, 0.00),
(3, 3, 0, NULL, 5, 0.00),
(4, 4, 0, NULL, 5, 0.00),
(5, 5, 0, NULL, 5, 0.00),
(6, 6, 0, NULL, 5, 0.00),
(7, 7, 0, NULL, 5, 0.00),
(8, 8, 0, NULL, 5, 0.00),
(9, 9, 0, NULL, 5, 0.00),
(10, 10, 0, NULL, 4, 0.00),
(11, 11, 0, NULL, 4, 0.00),
(12, 12, 0, NULL, 4, 0.00),
(13, 13, 0, NULL, 4, 0.00),
(14, 14, 0, NULL, 4, 0.00),
(15, 15, 0, NULL, 4, 0.00),
(16, 16, 0, NULL, 4, 0.00),
(17, 17, 0, NULL, 4, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `delivery_status`
--

CREATE TABLE `delivery_status` (
  `delivery_status_id` int(11) NOT NULL,
  `status_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `delivery_status`
--

INSERT INTO `delivery_status` (`delivery_status_id`, `status_name`) VALUES
(1, 'Pending'),
(2, 'Preparing'),
(3, 'Out for Delivery'),
(4, 'Delivered'),
(5, 'Cancelled');

-- --------------------------------------------------------

--
-- Table structure for table `ingredients`
--

CREATE TABLE `ingredients` (
  `ingredient_id` int(11) NOT NULL,
  `ingredient_name` varchar(255) NOT NULL,
  `stock_qty` int(11) NOT NULL DEFAULT 0,
  `min_qty` int(11) NOT NULL DEFAULT 10
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ingredients`
--

INSERT INTO `ingredients` (`ingredient_id`, `ingredient_name`, `stock_qty`, `min_qty`) VALUES
(1, 'Noodles', 77, 20),
(2, 'Broth', 58, 15),
(3, 'Pork Chashu', 38, 10),
(4, 'Egg', 47, 20),
(5, 'Green Onion', 50, 10),
(6, 'Spicy Oil', 40, 10);

-- --------------------------------------------------------

--
-- Table structure for table `ingredient_usage_logs`
--

CREATE TABLE `ingredient_usage_logs` (
  `log_id` int(11) NOT NULL,
  `ingredient_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty_used` int(11) NOT NULL,
  `used_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` varchar(500) NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `customer_id`, `order_id`, `title`, `message`, `is_read`, `created_at`) VALUES
(1, 2, 13, 'INVOICE: Order #13 Confirmed', 'Total: ₱535.00\nStatus: Kitchen Preparing', 1, '2025-12-14 04:54:01'),
(2, 2, 13, 'UPDATE: Order #13 Dispatched', 'Rider is on the way.', 1, '2025-12-14 04:55:14'),
(3, 2, 14, 'INVOICE: Order #14 Confirmed', 'Total: ₱305.00\nStatus: Kitchen Preparing', 1, '2025-12-14 05:07:36'),
(4, 2, 14, 'UPDATE: Order #14 Dispatched', 'Rider is on the way.', 1, '2025-12-14 05:11:18'),
(5, 2, 14, 'RECEIPT: Order #14 Completed', 'Paid: ₱305.00\nThank you!', 1, '2025-12-14 05:11:23'),
(6, 2, 13, 'RECEIPT: Order #13 Completed', 'Paid: ₱535.00\nThank you!', 1, '2025-12-14 05:11:26'),
(7, 2, 10, 'RECEIPT: Order #10 Completed', 'Paid: ₱305.00\nThank you!', 1, '2025-12-14 05:11:29'),
(8, 2, 11, 'RECEIPT: Order #11 Completed', 'Paid: ₱305.00\nThank you!', 1, '2025-12-14 05:11:30'),
(9, 2, 15, 'INVOICE: Order #15 Confirmed', 'Your order has been confirmed by the kitchen.\nAmount: ₱305.00\nStatus: Preparing', 1, '2025-12-14 05:28:02'),
(10, 2, 16, 'INVOICE: Order #16 Confirmed', 'Your order has been confirmed by the kitchen.\nAmount: ₱305.00\nStatus: Preparing', 1, '2025-12-14 05:54:34'),
(11, 2, 17, 'INVOICE: Order #17 Confirmed', 'Your order has been confirmed by the kitchen.\nAmount: ₱305.00\nStatus: Preparing', 1, '2025-12-14 06:00:31'),
(12, 2, 15, 'DISPATCH: Order #15 On the Way', 'Rider has picked up your order.\nDestination: Your Address\nEst. Time: 15-20 mins', 1, '2025-12-14 06:08:31'),
(13, 2, 16, 'DISPATCH: Order #16 On the Way', 'Rider has picked up your order.\nDestination: Your Address\nEst. Time: 15-20 mins', 1, '2025-12-14 06:08:32'),
(14, 2, 17, 'DISPATCH: Order #17 On the Way', 'Rider has picked up your order.\nDestination: Your Address\nEst. Time: 15-20 mins', 1, '2025-12-14 06:08:32'),
(15, 2, 15, 'RECEIPT: Order #15 Completed', 'Transaction Complete.\nPaid: ₱305.00\nThank you for dining with Ichiraku!', 1, '2025-12-14 06:08:35'),
(16, 2, 16, 'RECEIPT: Order #16 Completed', 'Transaction Complete.\nPaid: ₱305.00\nThank you for dining with Ichiraku!', 1, '2025-12-14 06:08:36'),
(17, 2, 17, 'RECEIPT: Order #17 Completed', 'Transaction Complete.\nPaid: ₱305.00\nThank you for dining with Ichiraku!', 1, '2025-12-14 06:08:37');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_date` datetime NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'COD',
  `shipping_address` text DEFAULT NULL,
  `rating` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `order_date`, `total_amount`, `payment_method`, `shipping_address`, `rating`) VALUES
(1, 2, '2025-12-12 15:38:03', 325.00, 'COD', 'Tabaco City', 0),
(2, 2, '2025-12-12 16:53:12', 305.00, 'COD', 'Tabaco City', 0),
(3, 2, '2025-12-12 18:16:40', 535.00, 'COD', 'Tabaco City', 0),
(4, 2, '2025-12-12 18:19:09', 305.00, 'COD', 'Tabaco City', 0),
(5, 2, '2025-12-12 18:28:40', 535.00, 'COD', 'Tabaco City', 0),
(6, 2, '2025-12-12 18:32:22', 535.00, 'COD', 'Tabaco City', 0),
(7, 2, '2025-12-12 18:46:25', 575.00, 'COD', 'Tabaco City', 0),
(8, 2, '2025-12-12 19:07:32', 305.00, 'COD', 'Tabaco City', 0),
(9, 2, '2025-12-12 19:17:51', 305.00, 'COD', 'Tabaco City', 0),
(10, 2, '2025-12-12 19:32:42', 305.00, 'COD', 'Tabaco City', 5),
(11, 2, '2025-12-13 01:38:55', 305.00, 'COD', 'Tabaco City', 5),
(12, 2, '2025-12-13 21:42:26', 535.00, 'COD', 'Tabaco City', 2),
(13, 2, '2025-12-13 21:53:42', 535.00, 'COD', 'Tabaco City', 5),
(14, 2, '2025-12-13 22:07:04', 305.00, 'COD', 'Tabaco City', 5),
(15, 2, '2025-12-13 22:27:43', 305.00, 'COD', 'Tabaco City', 5),
(16, 2, '2025-12-13 22:54:15', 305.00, 'COD', 'Tabaco City', 2),
(17, 2, '2025-12-13 23:00:09', 305.00, 'COD', 'Tabaco City', 2);

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `orderdetail_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`orderdetail_id`, `order_id`, `product_id`, `quantity`, `subtotal`) VALUES
(1, 1, 2, 1, 250.00),
(2, 2, 1, 1, 230.00),
(3, 3, 1, 2, 460.00),
(4, 4, 1, 1, 230.00),
(5, 5, 1, 2, 460.00),
(6, 6, 1, 2, 460.00),
(7, 7, 1, 1, 230.00),
(8, 7, 1, 1, 270.00),
(9, 8, 1, 1, 230.00),
(10, 9, 1, 1, 230.00),
(11, 10, 1, 1, 230.00),
(12, 11, 1, 1, 230.00),
(13, 12, 1, 2, 460.00),
(14, 13, 1, 1, 230.00),
(15, 13, 1, 1, 230.00),
(16, 14, 1, 1, 230.00),
(17, 15, 1, 1, 230.00),
(18, 16, 1, 1, 230.00),
(19, 17, 1, 1, 230.00);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_status_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_status`
--

CREATE TABLE `payment_status` (
  `payment_status_id` int(11) NOT NULL,
  `status_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_status`
--

INSERT INTO `payment_status` (`payment_status_id`, `status_name`) VALUES
(1, 'Unpaid'),
(2, 'Paid'),
(3, 'Refunded');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `category_id` int(11) NOT NULL,
  `image_url` text NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `price`, `category_id`, `image_url`, `description`) VALUES
(1, 'Ramen stir-fries', 230.00, 1, 'https://github.com/arjiannahcarmelle/Ichiraku_Ramen/blob/main/Ichiraku_Ramen_Assets/Ramen%20stir-fries.png?raw=true', 'A quick and customizable option with noodles stir-fried with sauces, and protein.'),
(2, 'Hiyashi chuka', 250.00, 1, 'https://github.com/arjiannahcarmelle/Ichiraku_Ramen/blob/main/Ichiraku_Ramen_Assets/Hiyashi%20chuka.png?raw=true', 'A refreshing chilled noodle dish topped with crisp vegetables, savory ham, and a tangy soy dressing.'),
(3, 'Ramen snack mix', 250.00, 1, 'https://github.com/arjiannahcarmelle/Ichiraku_Ramen/blob/main/Ichiraku_Ramen_Assets/Ramen%20snack%20mix.png?raw=true', 'Spicy seasoned ramen wrapped in chewy rice paper for a fun snack.'),
(4, 'Spicy noodle roll', 200.00, 1, 'https://github.com/arjiannahcarmelle/Ichiraku_Ramen/blob/main/Ichiraku_Ramen_Assets/Spicy%20noodle%20roll.png?raw=true', 'A savory and crunchy party mix featuring toasted ramen bits and nuts.');

-- --------------------------------------------------------

--
-- Table structure for table `product_ingredients`
--

CREATE TABLE `product_ingredients` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `ingredient_id` int(11) NOT NULL,
  `qty_required` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_ingredients`
--

INSERT INTO `product_ingredients` (`id`, `product_id`, `ingredient_id`, `qty_required`) VALUES
(1, 1, 1, 1),
(2, 1, 2, 1),
(3, 1, 3, 1),
(4, 1, 4, 1),
(5, 2, 1, 1),
(6, 2, 4, 1),
(7, 3, 1, 1),
(8, 3, 6, 1),
(9, 4, 1, 1),
(10, 4, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `riders`
--

CREATE TABLE `riders` (
  `rider_id` int(11) NOT NULL,
  `rider_name` varchar(255) NOT NULL,
  `contact_number` varchar(50) NOT NULL,
  `vehicle` varchar(100) NOT NULL,
  `status` enum('Available','Busy','Offline') NOT NULL DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `riders`
--

INSERT INTO `riders` (`rider_id`, `rider_name`, `contact_number`, `vehicle`, `status`) VALUES
(1, 'Kakashi Hatake', '09123456789', 'Ninja Bike', 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `ticket_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Open',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `support_tickets`
--

INSERT INTO `support_tickets` (`ticket_id`, `customer_id`, `subject`, `message`, `status`, `created_at`) VALUES
(1, 2, 'Feedback', 'Excellent service.', 'Resolved', '2025-12-14 06:24:33'),
(2, 2, 'General Inquiry', 'Does my enemy eat here?', 'Resolved', '2025-12-14 06:33:37');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_account_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `customer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_account_id`, `username`, `password`, `customer_id`) VALUES
(1, 'admin@ichiraku.com', '$2y$10$n6Z33EwUcTS4icINZ4dZgOHTp77N96L5GudJLlC6ybubC3pMTtk5O', 1),
(2, 'Naruto@gmail.com', '$2y$10$W8LeoKdrAtY8gV0T/PaZkuhN5YEL8ogDYgiEhn8KrOjOpClc9kU.W', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `customers_info`
--
ALTER TABLE `customers_info`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `delivery`
--
ALTER TABLE `delivery`
  ADD PRIMARY KEY (`delivery_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `delivery_status_id` (`delivery_status_id`);

--
-- Indexes for table `delivery_status`
--
ALTER TABLE `delivery_status`
  ADD PRIMARY KEY (`delivery_status_id`);

--
-- Indexes for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`ingredient_id`);

--
-- Indexes for table `ingredient_usage_logs`
--
ALTER TABLE `ingredient_usage_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `ingredient_id` (`ingredient_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`orderdetail_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `payment_status_id` (`payment_status_id`);

--
-- Indexes for table `payment_status`
--
ALTER TABLE `payment_status`
  ADD PRIMARY KEY (`payment_status_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_ingredients`
--
ALTER TABLE `product_ingredients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `ingredient_id` (`ingredient_id`);

--
-- Indexes for table `riders`
--
ALTER TABLE `riders`
  ADD PRIMARY KEY (`rider_id`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`ticket_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_account_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `customer_id` (`customer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customers_info`
--
ALTER TABLE `customers_info`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `delivery`
--
ALTER TABLE `delivery`
  MODIFY `delivery_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `delivery_status`
--
ALTER TABLE `delivery_status`
  MODIFY `delivery_status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `ingredient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `ingredient_usage_logs`
--
ALTER TABLE `ingredient_usage_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `orderdetail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_status`
--
ALTER TABLE `payment_status`
  MODIFY `payment_status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `product_ingredients`
--
ALTER TABLE `product_ingredients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `riders`
--
ALTER TABLE `riders`
  MODIFY `rider_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `delivery`
--
ALTER TABLE `delivery`
  ADD CONSTRAINT `delivery_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `delivery_ibfk_2` FOREIGN KEY (`delivery_status_id`) REFERENCES `delivery_status` (`delivery_status_id`) ON UPDATE CASCADE;

--
-- Constraints for table `ingredient_usage_logs`
--
ALTER TABLE `ingredient_usage_logs`
  ADD CONSTRAINT `ingredient_usage_logs_ibfk_1` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`ingredient_id`),
  ADD CONSTRAINT `ingredient_usage_logs_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers_info` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`payment_status_id`) REFERENCES `payment_status` (`payment_status_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON UPDATE CASCADE;

--
-- Constraints for table `product_ingredients`
--
ALTER TABLE `product_ingredients`
  ADD CONSTRAINT `product_ingredients_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_ingredients_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`ingredient_id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers_info` (`customer_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
