-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 22, 2025 at 10:59 AM
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
-- Database: `b2b`
--

-- --------------------------------------------------------

--
-- Table structure for table `businesses`
--

CREATE TABLE `businesses` (
  `id` int(11) NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `owner_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `registration_date` date NOT NULL,
  `status` enum('Pending','Approved','Rejected') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `businesses`
--

INSERT INTO `businesses` (`id`, `business_name`, `owner_name`, `email`, `registration_date`, `status`) VALUES
(1, 'XYZ Retailers', 'XYZ Retailers', 'user@xyz.com', '2025-05-07', 'Approved'),
(2, '', '', 'mark@gmail.com', '2025-05-21', 'Rejected'),
(3, '', '', 'dacuba@gmail.com', '2025-05-21', 'Approved'),
(4, '', '', 'carr@gmail.com', '2025-05-21', 'Approved'),
(5, '', '', 'lebron@gmail.com', '2025-05-21', 'Approved'),
(6, '', '', 'jjohn@gmail.com', '2025-05-22', 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `username`, `product_id`, `quantity`, `added_at`) VALUES
(3, 'user_1', 2, 3, '2025-05-21 22:15:40'),
(4, 'user_1', 4, 1, '2025-05-21 22:17:43'),
(5, 'user_1', 5, 1, '2025-05-21 22:50:14'),
(6, 'user_1', 6, 1, '2025-05-21 22:50:56'),
(12, '', 5, 1, '2025-05-22 08:30:27'),
(13, 'user_5', 5, 1, '2025-05-22 08:31:06'),
(14, 'user_5', 2, 1, '2025-05-22 08:41:57');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `company_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `gcash_qr_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`company_id`, `name`, `address`, `email`, `phone`, `gcash_qr_url`) VALUES
(1, 'ABC Electronics', '123 Tech St.', 'contact@abc.com', '123-456-7890', 'images/gcash_abc_electronics.png'),
(2, 'XYZ Retailers', '456 Retail Rd.', 'support@xyz.com', '987-654-3210', 'images/gcash_xyz_retailers.png'),
(3, 'marks spalding', NULL, 'mark@gmail.com', '12345678910', 'images/spalding.jpg'),
(4, 'LaLebron Corp', NULL, 'lebron@gmail.com', '12345472334', NULL),
(5, 'Jj Company', NULL, 'jjohn@gmail.com', '09706684707', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `delivery`
--

CREATE TABLE `delivery` (
  `delivery_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `shipped_date` date DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Processing',
  `tracking_number` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `invoice_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `issue_date` date DEFAULT curdate(),
  `total_amount` decimal(10,2) NOT NULL,
  `paid` tinyint(1) DEFAULT 0,
  `invoice_number` varchar(50) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `client_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_products`
--

CREATE TABLE `invoice_products` (
  `product_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) GENERATED ALWAYS AS (`quantity` * `unit_price`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `buyer_company_id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `gcash_number` varchar(20) DEFAULT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `status` enum('pending','completed','shipped') DEFAULT 'pending',
  `order_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `buyer_company_id`, `email`, `shipping_address`, `payment_method`, `gcash_number`, `order_date`, `status`, `order_total`) VALUES
(1, 1, NULL, NULL, NULL, NULL, '2025-05-01 14:30:00', 'pending', 150.00),
(2, 2, NULL, NULL, NULL, NULL, '2025-05-02 10:00:00', 'completed', 230.50),
(3, 1, NULL, NULL, NULL, NULL, '2025-05-03 16:45:00', 'shipped', 99.99),
(4, 3, 'mark@gmail.com', '123, sample, 456', 'gcash', '09123456789', '2025-05-22 10:03:19', 'completed', 123.00),
(5, 3, 'mark@gmail.com', '123, 456, 689', 'gcash', '09123456788', '2025-05-22 10:26:34', 'shipped', 109.00),
(6, 3, 'mark@gmail.com', '123, 456 ,789', 'gcash', '09123456789', '2025-05-22 10:31:00', 'shipped', 123.00),
(7, 1, 'mark@gmail.com', 'San Isidro', 'gcash', '0970684707', '2025-05-22 15:28:46', 'pending', 37452.00),
(8, 3, 'mark@gmail.com', '4507, libon, albay', 'gcash', '0970684707', '2025-05-22 16:31:53', 'pending', 120.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `order_detail_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`order_detail_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 4, 11, 1, 123.00),
(2, 5, 12, 1, 109.00),
(3, 6, 11, 1, 123.00),
(4, 7, 2, 2, 12000.00),
(5, 7, 8, 1, 13452.00),
(6, 8, 11, 1, 120.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `payment_date` date DEFAULT curdate(),
  `amount_paid` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `status` varchar(255) DEFAULT NULL,
  `seller` varchar(255) DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `company_id`, `name`, `description`, `price`, `stock_quantity`, `status`, `seller`, `image_url`) VALUES
(2, 1, 'leby nike', NULL, 12000.00, 23, 'Approved', NULL, NULL),
(3, 1, 'lebron nike', NULL, 12000.00, 23, 'Rejected', NULL, NULL),
(4, 1, 'lebron nike', NULL, 12000.00, 23, 'Approved', NULL, NULL),
(5, 1, 'coda', NULL, 113343.00, 234, 'Approved', NULL, NULL),
(6, 1, 'coda', NULL, 2123423.00, 34, 'Approved', NULL, NULL),
(7, 1, 'domi', NULL, 2452.00, 213, 'Approved', NULL, NULL),
(8, 1, 'vicky minaj', NULL, 13452.00, 23, 'Approved', NULL, NULL),
(9, 1, 'mark airr', NULL, 13343.00, 132, 'Rejected', NULL, NULL),
(11, 3, 'testing', NULL, 120.00, 5, 'Approved', NULL, NULL),
(12, 3, 'qwerty', NULL, 109.00, 3, 'Approved', NULL, NULL),
(13, 3, 'james', NULL, 123.00, 23, 'Approved', NULL, NULL),
(14, 3, 'nike lebron bundle 1', NULL, 100000.00, 1, 'Approved', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `role` enum('User','Admin') NOT NULL DEFAULT 'User',
  `company_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `company_name`, `email`, `username`, `phone`, `password`, `created_at`, `status`, `role`, `company_id`) VALUES
(1, 'Dacuba Trading', 'dacuba@gmail.com', 'user_1', '09123456789', '$2y$10$GBD8pkgiqtuRrm6ALd.6eOWGSZ3s9VN6vEuY5nb653NI.rBno5u.W', '2025-05-21 04:14:59', 'Approved', 'User', 1),
(2, 'admin trading', 'admin@gmail.com', 'user_2', '09123456789', '$2y$10$ckVP3lCFEGRFA/M1jvAw6.tq6SV6nFwQJ.4d4mALfaSFLqk5ZDguS', '2025-05-21 04:18:00', 'Rejected', 'Admin', NULL),
(5, 'marks spalding', 'mark@gmail.com', 'user_5', '12345678910', '$2y$10$pOzKRGtEUgVXA3gmRy/8CO1N7AlXL7qMoLvSPtyHHZmttiTe33FN.', '2025-05-21 12:58:02', 'Approved', 'User', 3),
(6, 'LaLebron Corp', 'lebron@gmail.com', 'user_6', '12345472334', '$2y$10$kr6/RieUaer.BE/tO78zIuLIHxSD9b8Xk0YIj1NxyQoFYYEe2896.', '2025-05-21 13:08:14', 'Approved', 'User', 4),
(7, 'Jj Company', 'jjohn@gmail.com', '', '09706684707', '$2y$10$QGAcHmcxdbbYjPdIwjRdguNiWJ/mdDlzWfxSyJx0R6/zn7HezBaQ6', '2025-05-22 08:27:24', 'Approved', 'User', 5);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `businesses`
--
ALTER TABLE `businesses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `username` (`username`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`company_id`);

--
-- Indexes for table `delivery`
--
ALTER TABLE `delivery`
  ADD PRIMARY KEY (`delivery_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`invoice_id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `invoice_products`
--
ALTER TABLE `invoice_products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `buyer_company_id` (`buyer_company_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`order_detail_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `users_ibfk_1` (`company_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `businesses`
--
ALTER TABLE `businesses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `delivery`
--
ALTER TABLE `delivery`
  MODIFY `delivery_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_products`
--
ALTER TABLE `invoice_products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `order_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `delivery`
--
ALTER TABLE `delivery`
  ADD CONSTRAINT `delivery_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `invoice_products`
--
ALTER TABLE `invoice_products`
  ADD CONSTRAINT `invoice_products_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`invoice_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`buyer_company_id`) REFERENCES `companies` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`invoice_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
