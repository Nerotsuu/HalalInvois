-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 22, 2026 at 11:29 AM
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
-- Database: `halalinvois`
--

-- --------------------------------------------------------

--
-- Table structure for table `ingredients`
--

CREATE TABLE `ingredients` (
  `ingredient_id` int(11) NOT NULL,
  `ingredient_name` varchar(100) NOT NULL,
  `supplier_name` varchar(100) NOT NULL,
  `expiry_date` date NOT NULL,
  `current_stock` decimal(10,2) NOT NULL DEFAULT 0.00,
  `unit` varchar(20) NOT NULL,
  `halal_cert_no` varchar(100) DEFAULT NULL,
  `cert_expiry_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `status` enum('available','unavailable') NOT NULL DEFAULT 'available',
  `image` varchar(255) DEFAULT 'assets/default-food.jpeg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `category`, `price`, `stock_quantity`, `status`, `image`) VALUES
(1, 'Nasi Lemak Special', 'Mains', 8.50, 37, 'available', 'assets/default-food.jpeg'),
(2, 'Teh Tarik Ais', 'Beverages', 3.20, 46, 'available', 'assets/default-food.jpeg'),
(3, 'Chicken Chop Set', 'Mains', 15.00, -11, 'available', 'assets/default-food.jpeg'),
(4, 'Sirap Bandung', 'Beverages', 3.00, 67, 'available', 'assets/default-food.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `product_ingredients`
--

CREATE TABLE `product_ingredients` (
  `product_id` int(11) NOT NULL,
  `ingredient_id` int(11) NOT NULL,
  `quantity_required` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `sst_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','card','duitnow_qr') NOT NULL,
  `lhdn_uuid` varchar(36) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `user_id`, `total_amount`, `sst_amount`, `payment_method`, `lhdn_uuid`, `created_at`) VALUES
(1, 2, 3.20, 0.00, 'duitnow_qr', '9a66010f-6bb1-4c65-8aa7-e34e5173c9eb', '2026-11-17 16:11:27'),
(2, 2, 3.20, 0.00, 'duitnow_qr', '87952bc2-737d-457d-a490-a60c6a8ff495', '2026-11-20 17:53:09'),
(3, 2, 15.00, 0.00, 'duitnow_qr', '253bffbb-d220-409d-aa3f-4df7e4e52d29', '2026-11-20 17:53:17'),
(4, 2, 18.20, 0.00, 'cash', 'c99b30fb-b080-4c5e-91e3-ba09bf312712', '2026-11-20 17:53:58'),
(5, 2, 18.20, 0.00, 'cash', '33c063c2-6b01-4f96-981c-adb7f566cb5a', '2026-11-20 18:00:18'),
(6, 2, 18.20, 0.00, 'cash', '1d057968-ef0b-4a48-beb1-0ca5b9eeded5', '2026-11-20 18:00:21'),
(7, 2, 18.20, 0.00, 'cash', 'e5d719c3-fa0f-41c4-9886-a6029413b83d', '2026-11-20 18:00:22'),
(8, 2, 18.20, 0.00, 'cash', 'e32664ec-a868-4e90-9d68-de8ada45c72f', '2026-11-20 18:00:23'),
(9, 2, 18.20, 0.00, 'cash', 'e481d3f2-5107-4dd6-b501-eb038621b74a', '2026-11-20 18:00:24'),
(10, 2, 18.20, 0.00, 'cash', '16f94575-1284-4e11-b05e-b93edcc35d95', '2026-11-20 18:00:24'),
(11, 2, 18.20, 0.00, 'cash', '8bfb2ccc-adfe-4339-8d64-87ac021f1de8', '2026-11-20 18:00:25'),
(12, 2, 18.20, 0.00, 'cash', 'ff90bf3b-ec54-4a56-8515-bff7b251a5a4', '2026-11-20 18:00:25'),
(13, 2, 18.20, 0.00, 'cash', '728945d8-d463-4130-bfc3-b824d24fb496', '2026-11-20 18:00:26'),
(14, 2, 18.20, 0.00, 'cash', 'a7dfdea5-0836-4e71-a3fe-e06cd899c609', '2026-11-20 18:00:26'),
(15, 2, 18.20, 0.00, 'cash', '657789a0-10f7-44aa-a104-1d03acd6ba03', '2026-11-20 18:00:27'),
(16, 2, 18.20, 0.00, 'cash', 'f7e97df3-ef82-4535-b6ff-06628c7723e3', '2026-11-20 18:00:27'),
(17, 2, 18.20, 0.00, 'cash', '02c1aee8-3308-473a-ba40-e8129b0b3845', '2026-11-20 18:00:28'),
(18, 2, 18.20, 0.00, 'cash', '8f809e56-2fa8-4e24-90ef-3421cb9d0a2d', '2026-11-20 18:00:28'),
(19, 2, 18.20, 0.00, 'cash', '14b307c1-a91b-48e8-a464-6d0060ec78dc', '2026-11-20 18:58:12'),
(20, 2, 18.20, 0.00, 'cash', 'e773b772-9a75-4a23-a3a9-f39162ff6919', '2026-11-20 18:58:17'),
(21, 2, 18.20, 0.00, 'cash', '83e8897e-69f1-455c-96c1-b20483fa6e7c', '2026-11-20 18:58:22'),
(22, 2, 18.20, 0.00, 'cash', 'ff8e98a6-39ed-4720-b2ba-fb1740d1993e', '2026-11-20 19:50:04'),
(23, 2, 18.20, 0.00, 'cash', '0c312cb2-25ef-484a-ac54-b1a7b3f03565', '2026-11-20 19:54:52'),
(24, 2, 18.20, 0.00, 'cash', '5f8aeb76-e4c7-45a4-ac11-f695b65befd4', '2026-11-20 19:54:55'),
(25, 2, 18.20, 0.00, 'cash', '5d95aea9-7dc3-4966-85b2-b2dfe4cd6717', '2026-11-20 19:55:01'),
(26, 2, 18.20, 0.00, 'cash', 'a6443fd4-8d3e-4d2d-b229-493d948bf1e2', '2026-11-20 20:01:00'),
(27, 2, 19.29, 1.09, 'cash', '19057928-b4fe-4e87-8c38-e226da5037ff', '2026-11-20 20:23:23'),
(28, 2, 19.29, 1.09, 'cash', '0395228f-ff5f-4995-81ff-9fa66685b7fd', '2026-11-20 20:29:23'),
(29, 2, 19.29, 1.09, 'cash', '0b9afcde-4210-440b-b7f9-be625ae68972', '2026-11-20 20:30:08'),
(30, 2, 19.29, 1.09, 'cash', '7b9bd62b-07bb-4cc0-95cf-7b893cc56519', '2026-11-20 20:33:35'),
(31, 2, 19.29, 1.09, 'cash', '901afe4d-3f63-44df-abfe-be0b637e342c', '2026-11-20 20:33:42'),
(32, 2, 19.29, 1.09, 'cash', '8ddc48a5-32a0-4552-9ef5-76c4936bc504', '2026-11-20 20:33:44'),
(33, 2, 19.29, 1.09, 'cash', '4679d2b8-bdce-45e9-8583-8ce2742b8511', '2026-11-20 20:34:22'),
(34, 2, 19.29, 1.09, 'cash', 'c9dc4f70-6f6f-4378-8eab-ee7f2a4c0fc6', '2026-11-20 20:34:30'),
(35, 2, 19.29, 1.09, 'cash', '67308a02-1907-41d2-9ff2-e466153140c2', '2026-11-20 20:34:43'),
(36, 2, 19.29, 1.09, 'cash', '8214e078-3a13-4b1c-bb99-2e9bcbafc2f3', '2026-11-20 20:35:38'),
(37, 2, 19.29, 1.09, 'cash', 'eded8f12-1c32-490f-80ad-cbf350b18490', '2026-11-20 20:36:15'),
(38, 2, 19.29, 1.09, 'cash', '5fdce538-322a-452d-895c-38eb6f49f826', '2026-11-20 20:36:31'),
(39, 2, 19.29, 1.09, 'cash', '49910999-aaf1-4df3-a0dc-ad9300b31047', '2026-11-20 20:37:21'),
(40, 2, 19.29, 1.09, 'cash', '0fb9d4d0-827c-4078-957a-d5f0f77e10c7', '2026-11-20 20:40:25'),
(41, 2, 19.29, 1.09, 'cash', '37c321f8-c304-4fef-ba94-a22a4d35fea3', '2026-11-20 20:40:48'),
(42, 2, 19.29, 1.09, 'cash', 'bd7cbc6d-382d-40ff-9d0c-876708350af9', '2026-11-20 20:41:04'),
(43, 2, 19.29, 1.09, 'cash', 'e8047d58-ffb2-4ed0-9fc7-bbc7d4d0500c', '2026-11-20 20:43:51'),
(44, 1, 24.59, 1.39, 'cash', 'e1881cb3-231a-4765-b7ae-7b7523c1f82b', '2026-11-21 13:43:54'),
(45, 1, 24.59, 1.39, 'cash', '2973b243-a7fd-48f9-9b37-3f303097bbd8', '2026-11-21 13:50:45'),
(46, 1, 24.59, 1.39, 'cash', '6caed09a-311b-486b-9f46-b8678172d3a9', '2026-11-21 15:09:27'),
(47, 1, 24.59, 1.39, 'cash', '442f08cd-c958-4a0f-8675-c75db0114112', '2026-11-21 15:10:04'),
(48, 1, 24.59, 1.39, 'cash', 'ca2a276c-2619-4980-9034-a8cbf946e565', '2026-11-21 15:16:11'),
(49, 1, 24.59, 1.39, 'cash', '9fdeb06c-60ed-4e67-b7f8-6b78d00316b6', '2026-11-21 15:16:15'),
(50, 2, 6.57, 0.37, 'duitnow_qr', '3d1f5b10-463e-42ce-a8ae-134af6870205', '2026-11-21 15:38:51'),
(51, 2, 6.57, 0.37, 'duitnow_qr', '6df8e60b-6cb4-46a8-a183-699721c5fc07', '2026-11-21 15:55:01'),
(52, 2, 6.57, 0.37, 'duitnow_qr', 'a9af5d2a-e30d-4c8f-a056-de8299102b61', '2026-11-21 15:55:05'),
(53, 1, 12.72, 0.72, 'duitnow_qr', 'e9e2c0d1-77f9-4d5f-99b7-1d7a41066841', '2026-11-21 16:52:57'),
(54, 1, 9.01, 0.51, 'duitnow_qr', '20c5cd16-c153-4ff0-be2a-5ce6a906961b', '2026-11-21 16:53:16'),
(55, 1, 10.18, 0.58, 'cash', '377b57c3-4e7d-459b-a985-a8b98bdaf195', '2026-11-21 16:53:55');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_items`
--

CREATE TABLE `transaction_items` (
  `item_id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_items`
--

INSERT INTO `transaction_items` (`item_id`, `transaction_id`, `product_id`, `quantity`, `subtotal`) VALUES
(1, 1, 2, 1, 3.20),
(2, 2, 2, 1, 3.20),
(3, 3, 3, 1, 15.00),
(4, 4, 3, 1, 15.00),
(5, 4, 2, 1, 3.20),
(6, 5, 3, 1, 15.00),
(7, 5, 2, 1, 3.20),
(8, 6, 3, 1, 15.00),
(9, 6, 2, 1, 3.20),
(10, 7, 3, 1, 15.00),
(11, 7, 2, 1, 3.20),
(12, 8, 3, 1, 15.00),
(13, 8, 2, 1, 3.20),
(14, 9, 3, 1, 15.00),
(15, 9, 2, 1, 3.20),
(16, 10, 3, 1, 15.00),
(17, 10, 2, 1, 3.20),
(18, 11, 3, 1, 15.00),
(19, 11, 2, 1, 3.20),
(20, 12, 3, 1, 15.00),
(21, 12, 2, 1, 3.20),
(22, 13, 3, 1, 15.00),
(23, 13, 2, 1, 3.20),
(24, 14, 3, 1, 15.00),
(25, 14, 2, 1, 3.20),
(26, 15, 3, 1, 15.00),
(27, 15, 2, 1, 3.20),
(28, 16, 3, 1, 15.00),
(29, 16, 2, 1, 3.20),
(30, 17, 3, 1, 15.00),
(31, 17, 2, 1, 3.20),
(32, 18, 3, 1, 15.00),
(33, 18, 2, 1, 3.20),
(34, 19, 3, 1, 15.00),
(35, 19, 2, 1, 3.20),
(36, 20, 3, 1, 15.00),
(37, 20, 2, 1, 3.20),
(38, 21, 3, 1, 15.00),
(39, 21, 2, 1, 3.20),
(40, 22, 3, 1, 15.00),
(41, 22, 2, 1, 3.20),
(42, 23, 3, 1, 15.00),
(43, 23, 2, 1, 3.20),
(44, 24, 3, 1, 15.00),
(45, 24, 2, 1, 3.20),
(46, 25, 3, 1, 15.00),
(47, 25, 2, 1, 3.20),
(48, 26, 3, 1, 15.00),
(49, 26, 2, 1, 3.20),
(50, 27, 3, 1, 15.00),
(51, 27, 2, 1, 3.20),
(52, 28, 3, 1, 15.00),
(53, 28, 2, 1, 3.20),
(54, 29, 3, 1, 15.00),
(55, 29, 2, 1, 3.20),
(56, 30, 3, 1, 15.00),
(57, 30, 2, 1, 3.20),
(58, 31, 3, 1, 15.00),
(59, 31, 2, 1, 3.20),
(60, 32, 3, 1, 15.00),
(61, 32, 2, 1, 3.20),
(62, 33, 3, 1, 15.00),
(63, 33, 2, 1, 3.20),
(64, 34, 3, 1, 15.00),
(65, 34, 2, 1, 3.20),
(66, 35, 3, 1, 15.00),
(67, 35, 2, 1, 3.20),
(68, 36, 3, 1, 15.00),
(69, 36, 2, 1, 3.20),
(70, 37, 3, 1, 15.00),
(71, 37, 2, 1, 3.20),
(72, 38, 3, 1, 15.00),
(73, 38, 2, 1, 3.20),
(74, 39, 3, 1, 15.00),
(75, 39, 2, 1, 3.20),
(76, 40, 3, 1, 15.00),
(77, 40, 2, 1, 3.20),
(78, 41, 3, 1, 15.00),
(79, 41, 2, 1, 3.20),
(80, 42, 3, 1, 15.00),
(81, 42, 2, 1, 3.20),
(82, 43, 3, 1, 15.00),
(83, 43, 2, 1, 3.20),
(84, 44, 2, 1, 3.20),
(85, 44, 4, 1, 3.00),
(86, 44, 1, 2, 17.00),
(87, 45, 2, 1, 3.20),
(88, 45, 4, 1, 3.00),
(89, 45, 1, 2, 17.00),
(90, 46, 2, 1, 3.20),
(91, 46, 4, 1, 3.00),
(92, 46, 1, 2, 17.00),
(93, 47, 2, 1, 3.20),
(94, 47, 4, 1, 3.00),
(95, 47, 1, 2, 17.00),
(96, 48, 2, 1, 3.20),
(97, 48, 4, 1, 3.00),
(98, 48, 1, 2, 17.00),
(99, 49, 2, 1, 3.20),
(100, 49, 4, 1, 3.00),
(101, 49, 1, 2, 17.00),
(102, 50, 2, 1, 3.20),
(103, 50, 4, 1, 3.00),
(104, 51, 2, 1, 3.20),
(105, 51, 4, 1, 3.00),
(106, 52, 2, 1, 3.20),
(107, 52, 4, 1, 3.00),
(108, 53, 4, 4, 12.00),
(109, 54, 1, 1, 8.50),
(110, 55, 2, 3, 9.60);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('owner','cashier','auditor') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'owner_store1', '$2y$10$B20CIX7VAff/.XOKht9g3.zniXkZsoxFVSFi6hlTSayWDy2y15NS.', 'owner', '2026-11-17 09:13:51'),
(2, 'cashier_1', '$2y$10$B20CIX7VAff/.XOKht9g3.zniXkZsoxFVSFi6hlTSayWDy2y15NS.', 'cashier', '2026-11-17 09:13:51'),
(3, 'auditor_gov', '$2y$10$B20CIX7VAff/.XOKht9g3.zniXkZsoxFVSFi6hlTSayWDy2y15NS.', 'auditor', '2026-11-17 09:13:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`ingredient_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `product_ingredients`
--
ALTER TABLE `product_ingredients`
  ADD PRIMARY KEY (`product_id`,`ingredient_id`),
  ADD KEY `ingredient_id` (`ingredient_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `ingredient_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `transaction_items`
--
ALTER TABLE `transaction_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `product_ingredients`
--
ALTER TABLE `product_ingredients`
  ADD CONSTRAINT `product_ingredients_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_ingredients_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`ingredient_id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD CONSTRAINT `transaction_items_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`transaction_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
