-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 24, 2026 at 08:33 PM
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

--
-- Dumping data for table `ingredients`
--

INSERT INTO `ingredients` (`ingredient_id`, `ingredient_name`, `supplier_name`, `expiry_date`, `current_stock`, `unit`, `halal_cert_no`, `cert_expiry_date`) VALUES
(1, 'Olive Oil', 'Terlajak Laris Sdn Bhd', '2026-12-31', 10.00, 'L', 'JAKIM 123', '2027-01-22'),
(10, 'Coffe Beans', 'Nestle Sdn Bhd', '2026-11-26', 10.25, 'kg', 'JAKIM 456', '2026-11-25'),
(11, 'Wagyu', 'Pak Man Lembu Enterprise', '2026-11-27', 30.00, 'kg', 'JAKIM 459', '2026-12-24');

-- --------------------------------------------------------

--
-- Table structure for table `pending_qr_payments`
--

CREATE TABLE `pending_qr_payments` (
  `order_id` varchar(64) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 2,
  `total_amount` decimal(10,2) NOT NULL,
  `sst_amount` decimal(10,2) NOT NULL,
  `cart_json` longtext NOT NULL,
  `status` enum('waiting','paid') NOT NULL DEFAULT 'waiting',
  `transaction_id` int(11) DEFAULT NULL,
  `lhdn_uuid` varchar(36) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pending_qr_payments`
--

INSERT INTO `pending_qr_payments` (`order_id`, `user_id`, `total_amount`, `sst_amount`, `cart_json`, `status`, `transaction_id`, `lhdn_uuid`, `created_at`) VALUES
('ORD-20261124193806-538', 2, 15.90, 0.90, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":45}]', 'waiting', NULL, NULL, '2026-11-24 18:38:06'),
('ORD-20261124193810-211', 2, 19.29, 1.09, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":45},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":39}]', 'waiting', NULL, NULL, '2026-11-24 18:38:10'),
('ORD-20261124193814-432', 2, 19.29, 1.09, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":45},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":39}]', 'waiting', NULL, NULL, '2026-11-24 18:38:14'),
('ORD-20261124193911-237', 2, 15.90, 0.90, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":44}]', 'waiting', NULL, NULL, '2026-11-24 18:39:11'),
('ORD-20261124193917-242', 2, 15.90, 0.90, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":44}]', 'waiting', NULL, NULL, '2026-11-24 18:39:17'),
('ORD-20261124193957-870', 2, 15.90, 0.90, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":44}]', 'waiting', NULL, NULL, '2026-11-24 18:39:57'),
('ORD-20261124194157-226', 2, 15.90, 0.90, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":44}]', 'waiting', NULL, NULL, '2026-11-24 18:41:57'),
('ORD-20261124194158-873', 2, 19.29, 1.09, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":44},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":38}]', 'waiting', NULL, NULL, '2026-11-24 18:41:58'),
('ORD-20261124194159-352', 2, 28.30, 1.60, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":44},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":38},{\"product_id\":1,\"product_name\":\"Nasi Lemak Special\",\"price\":8.5,\"quantity\":1,\"stock\":32}]', 'waiting', NULL, NULL, '2026-11-24 18:41:59'),
('ORD-20261124194952-430', 2, 28.30, 1.60, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":44},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":38},{\"product_id\":1,\"product_name\":\"Nasi Lemak Special\",\"price\":8.5,\"quantity\":1,\"stock\":32}]', 'waiting', NULL, NULL, '2026-11-24 18:49:52'),
('ORD-20261124195038-254', 2, 19.29, 1.09, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":44},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":38}]', 'waiting', NULL, NULL, '2026-11-24 18:50:38'),
('ORD-20261124195038-745', 2, 15.90, 0.90, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":44}]', 'waiting', NULL, NULL, '2026-11-24 18:50:38'),
('ORD-20261124195041-281', 2, 19.29, 1.09, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":44},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":38}]', 'paid', 62, '45f30569-fc5b-4855-a5a1-49fa3c993d5d', '2026-11-24 18:50:41'),
('ORD-20261124195232-825', 2, 15.90, 0.90, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":43}]', 'waiting', NULL, NULL, '2026-11-24 18:52:32'),
('ORD-20261124195233-253', 2, 19.29, 1.09, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":43},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":37}]', 'paid', 63, 'b63f4466-d8c6-4a63-b190-bd1b78657f55', '2026-11-24 18:52:33'),
('ORD-20261124195317-317', 2, 3.39, 0.19, '[{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":37}]', 'waiting', NULL, NULL, '2026-11-24 18:53:17'),
('ORD-20261124195318-807', 2, 12.40, 0.70, '[{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":37},{\"product_id\":1,\"product_name\":\"Nasi Lemak Special\",\"price\":8.5,\"quantity\":1,\"stock\":32}]', 'waiting', NULL, NULL, '2026-11-24 18:53:18'),
('ORD-20261124195320-975', 2, 28.30, 1.60, '[{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":37},{\"product_id\":1,\"product_name\":\"Nasi Lemak Special\",\"price\":8.5,\"quantity\":1,\"stock\":32},{\"product_id\":5,\"product_name\":\"Tiramisu Cake\",\"price\":15,\"quantity\":1,\"stock\":49}]', 'paid', 64, 'ed12d93d-a6be-44d4-8d83-cd4743190c0f', '2026-11-24 18:53:20'),
('ORD-20261124195718-673', 2, 19.29, 1.09, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":42},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":35}]', 'waiting', NULL, NULL, '2026-11-24 18:57:18'),
('ORD-20261124195718-695', 2, 15.90, 0.90, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":42}]', 'waiting', NULL, NULL, '2026-11-24 18:57:18'),
('ORD-20261124195727-153', 2, 15.90, 0.90, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":42}]', 'waiting', NULL, NULL, '2026-11-24 18:57:27'),
('ORD-20261124195727-667', 2, 19.29, 1.09, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":42},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":35}]', 'waiting', NULL, NULL, '2026-11-24 18:57:27'),
('ORD-20261124195737-468', 2, 19.29, 1.09, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":41},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":34}]', 'waiting', NULL, NULL, '2026-11-24 18:57:37'),
('ORD-20261124195737-900', 2, 15.90, 0.90, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":41}]', 'waiting', NULL, NULL, '2026-11-24 18:57:37'),
('ORD-20261124195738-470', 2, 35.19, 1.99, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":41},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":34},{\"product_id\":5,\"product_name\":\"Tiramisu Cake\",\"price\":15,\"quantity\":1,\"stock\":48}]', 'paid', 66, 'f75161aa-c909-45e3-b7ef-44716cd5a7c6', '2026-11-24 18:57:38'),
('ORD-20261124195830-236', 2, 15.90, 0.90, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":40}]', 'waiting', NULL, NULL, '2026-11-24 18:58:30'),
('ORD-20261124195831-751', 2, 19.29, 1.09, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":40},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":33}]', 'waiting', NULL, NULL, '2026-11-24 18:58:31'),
('ORD-20261124195832-701', 2, 35.19, 1.99, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":40},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":33},{\"product_id\":5,\"product_name\":\"Tiramisu Cake\",\"price\":15,\"quantity\":1,\"stock\":47}]', 'waiting', NULL, NULL, '2026-11-24 18:58:32'),
('ORD-20261124195833-618', 2, 38.37, 2.17, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":40},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":33},{\"product_id\":5,\"product_name\":\"Tiramisu Cake\",\"price\":15,\"quantity\":1,\"stock\":47},{\"product_id\":4,\"product_name\":\"Sirap Bandung\",\"price\":3,\"quantity\":1,\"stock\":66}]', 'paid', 67, 'bc7637c4-0f08-4cb6-9d35-749d78865386', '2026-11-24 18:58:33'),
('ORD-20261124195909-661', 2, 15.90, 0.90, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":39}]', 'waiting', NULL, NULL, '2026-11-24 18:59:09'),
('ORD-20261124195910-484', 2, 19.29, 1.09, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":39},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":32}]', 'waiting', NULL, NULL, '2026-11-24 18:59:10'),
('ORD-20261124195924-456', 2, 15.90, 0.90, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":37}]', 'waiting', NULL, NULL, '2026-11-24 18:59:24'),
('ORD-20261124201009-625', 2, 15.90, 0.90, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":36}]', 'waiting', NULL, NULL, '2026-11-24 19:10:09'),
('ORD-20261124201009-851', 2, 19.29, 1.09, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":36},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":29}]', 'waiting', NULL, NULL, '2026-11-24 19:10:09'),
('ORD-20261124201719-833', 2, 15.90, 0.90, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":36}]', 'waiting', NULL, NULL, '2026-11-24 19:17:19'),
('ORD-20261124201720-701', 2, 19.29, 1.09, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":36},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":29}]', 'waiting', NULL, NULL, '2026-11-24 19:17:20'),
('ORD-20261124201729-748', 2, 19.29, 1.09, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":36},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":29}]', 'waiting', NULL, NULL, '2026-11-24 19:17:29'),
('ORD-20261124201750-713', 2, 19.29, 1.09, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":36},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":29}]', 'waiting', NULL, NULL, '2026-11-24 19:17:50'),
('ORD-20261124201753-264', 2, 15.90, 0.90, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":36}]', 'waiting', NULL, NULL, '2026-11-24 19:17:53'),
('ORD-20261124201754-439', 2, 19.29, 1.09, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":36},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":29}]', 'waiting', NULL, NULL, '2026-11-24 19:17:54'),
('ORD-20261124201755-875', 2, 35.19, 1.99, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":36},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":29},{\"product_id\":5,\"product_name\":\"Tiramisu Cake\",\"price\":15,\"quantity\":1,\"stock\":46}]', 'waiting', NULL, NULL, '2026-11-24 19:17:55'),
('ORD-20261124201756-673', 2, 38.37, 2.17, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":36},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":29},{\"product_id\":5,\"product_name\":\"Tiramisu Cake\",\"price\":15,\"quantity\":1,\"stock\":46},{\"product_id\":4,\"product_name\":\"Sirap Bandung\",\"price\":3,\"quantity\":1,\"stock\":65}]', 'waiting', NULL, NULL, '2026-11-24 19:17:56'),
('ORD-20261124201757-590', 2, 47.38, 2.68, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":36},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":29},{\"product_id\":5,\"product_name\":\"Tiramisu Cake\",\"price\":15,\"quantity\":1,\"stock\":46},{\"product_id\":4,\"product_name\":\"Sirap Bandung\",\"price\":3,\"quantity\":1,\"stock\":65},{\"product_id\":1,\"product_name\":\"Nasi Lemak Special\",\"price\":8.5,\"quantity\":1,\"stock\":31}]', 'waiting', NULL, NULL, '2026-11-24 19:17:57'),
('ORD-20261124201757-679', 2, 56.39, 3.19, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":36},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":29},{\"product_id\":5,\"product_name\":\"Tiramisu Cake\",\"price\":15,\"quantity\":1,\"stock\":46},{\"product_id\":4,\"product_name\":\"Sirap Bandung\",\"price\":3,\"quantity\":1,\"stock\":65},{\"product_id\":1,\"product_name\":\"Nasi Lemak Special\",\"price\":8.5,\"quantity\":2,\"stock\":31}]', 'waiting', NULL, NULL, '2026-11-24 19:17:57'),
('ORD-20261124201758-725', 2, 59.78, 3.38, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":36},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":2,\"stock\":29},{\"product_id\":5,\"product_name\":\"Tiramisu Cake\",\"price\":15,\"quantity\":1,\"stock\":46},{\"product_id\":4,\"product_name\":\"Sirap Bandung\",\"price\":3,\"quantity\":1,\"stock\":65},{\"product_id\":1,\"product_name\":\"Nasi Lemak Special\",\"price\":8.5,\"quantity\":2,\"stock\":31}]', 'waiting', NULL, NULL, '2026-11-24 19:17:58'),
('ORD-20261124201759-235', 2, 75.68, 4.28, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":36},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":2,\"stock\":29},{\"product_id\":5,\"product_name\":\"Tiramisu Cake\",\"price\":15,\"quantity\":2,\"stock\":46},{\"product_id\":4,\"product_name\":\"Sirap Bandung\",\"price\":3,\"quantity\":1,\"stock\":65},{\"product_id\":1,\"product_name\":\"Nasi Lemak Special\",\"price\":8.5,\"quantity\":2,\"stock\":31}]', 'waiting', NULL, NULL, '2026-11-24 19:17:59'),
('ORD-20261124201800-709', 2, 78.86, 4.46, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":36},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":2,\"stock\":29},{\"product_id\":5,\"product_name\":\"Tiramisu Cake\",\"price\":15,\"quantity\":2,\"stock\":46},{\"product_id\":4,\"product_name\":\"Sirap Bandung\",\"price\":3,\"quantity\":2,\"stock\":65},{\"product_id\":1,\"product_name\":\"Nasi Lemak Special\",\"price\":8.5,\"quantity\":2,\"stock\":31}]', 'waiting', NULL, NULL, '2026-11-24 19:18:00'),
('ORD-20261124201801-240', 2, 110.66, 6.26, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":3,\"stock\":36},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":2,\"stock\":29},{\"product_id\":5,\"product_name\":\"Tiramisu Cake\",\"price\":15,\"quantity\":2,\"stock\":46},{\"product_id\":4,\"product_name\":\"Sirap Bandung\",\"price\":3,\"quantity\":2,\"stock\":65},{\"product_id\":1,\"product_name\":\"Nasi Lemak Special\",\"price\":8.5,\"quantity\":2,\"stock\":31}]', 'paid', 71, '8c7fe005-1137-4553-9ab4-2fde2301b87a', '2026-11-24 19:18:01'),
('ORD-20261124201801-368', 2, 94.76, 5.36, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":2,\"stock\":36},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":2,\"stock\":29},{\"product_id\":5,\"product_name\":\"Tiramisu Cake\",\"price\":15,\"quantity\":2,\"stock\":46},{\"product_id\":4,\"product_name\":\"Sirap Bandung\",\"price\":3,\"quantity\":2,\"stock\":65},{\"product_id\":1,\"product_name\":\"Nasi Lemak Special\",\"price\":8.5,\"quantity\":2,\"stock\":31}]', 'waiting', NULL, NULL, '2026-11-24 19:18:01'),
('ORD-20261124201903-298', 1, 19.29, 1.09, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":33},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":27}]', 'waiting', NULL, NULL, '2026-11-24 19:19:03'),
('ORD-20261124201903-786', 1, 15.90, 0.90, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":33}]', 'waiting', NULL, NULL, '2026-11-24 19:19:03'),
('ORD-20261124201911-894', 1, 19.29, 1.09, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":33},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":27}]', 'paid', 72, '84e4006a-f8a2-4155-aa45-fc9f7a8e57ce', '2026-11-24 19:19:11'),
('ORD-20261124202123-359', 1, 15.90, 0.90, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":32}]', 'waiting', NULL, NULL, '2026-11-24 19:21:23'),
('ORD-20261124202124-647', 1, 28.30, 1.60, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":32},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":26},{\"product_id\":1,\"product_name\":\"Nasi Lemak Special\",\"price\":8.5,\"quantity\":1,\"stock\":29}]', 'waiting', NULL, NULL, '2026-11-24 19:21:24'),
('ORD-20261124202124-829', 1, 19.29, 1.09, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":32},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":26}]', 'waiting', NULL, NULL, '2026-11-24 19:21:24'),
('ORD-20261124202125-876', 1, 31.48, 1.78, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":32},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":26},{\"product_id\":1,\"product_name\":\"Nasi Lemak Special\",\"price\":8.5,\"quantity\":1,\"stock\":29},{\"product_id\":4,\"product_name\":\"Sirap Bandung\",\"price\":3,\"quantity\":1,\"stock\":63}]', 'waiting', NULL, NULL, '2026-11-24 19:21:25'),
('ORD-20261124202126-101', 1, 47.38, 2.68, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":32},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":26},{\"product_id\":1,\"product_name\":\"Nasi Lemak Special\",\"price\":8.5,\"quantity\":1,\"stock\":29},{\"product_id\":4,\"product_name\":\"Sirap Bandung\",\"price\":3,\"quantity\":1,\"stock\":63},{\"product_id\":5,\"product_name\":\"Tiramisu Cake\",\"price\":15,\"quantity\":1,\"stock\":44}]', 'waiting', NULL, NULL, '2026-11-24 19:21:26'),
('ORD-20261124202128-504', 1, 95.08, 5.38, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":32},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":26},{\"product_id\":1,\"product_name\":\"Nasi Lemak Special\",\"price\":8.5,\"quantity\":1,\"stock\":29},{\"product_id\":4,\"product_name\":\"Sirap Bandung\",\"price\":3,\"quantity\":1,\"stock\":63},{\"product_id\":5,\"product_name\":\"Tiramisu Cake\",\"price\":15,\"quantity\":4,\"stock\":44}]', 'paid', 73, 'a820198e-fafd-40b1-9af0-46211f69b72f', '2026-11-24 19:21:28'),
('ORD-20261124202128-546', 1, 63.28, 3.58, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":32},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":26},{\"product_id\":1,\"product_name\":\"Nasi Lemak Special\",\"price\":8.5,\"quantity\":1,\"stock\":29},{\"product_id\":4,\"product_name\":\"Sirap Bandung\",\"price\":3,\"quantity\":1,\"stock\":63},{\"product_id\":5,\"product_name\":\"Tiramisu Cake\",\"price\":15,\"quantity\":2,\"stock\":44}]', 'waiting', NULL, NULL, '2026-11-24 19:21:28'),
('ORD-20261124202128-652', 1, 79.18, 4.48, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":32},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":26},{\"product_id\":1,\"product_name\":\"Nasi Lemak Special\",\"price\":8.5,\"quantity\":1,\"stock\":29},{\"product_id\":4,\"product_name\":\"Sirap Bandung\",\"price\":3,\"quantity\":1,\"stock\":63},{\"product_id\":5,\"product_name\":\"Tiramisu Cake\",\"price\":15,\"quantity\":3,\"stock\":44}]', 'waiting', NULL, NULL, '2026-11-24 19:21:28'),
('ORD-20261124202205-723', 1, 3.39, 0.19, '[{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":25}]', 'waiting', NULL, NULL, '2026-11-24 19:22:05'),
('ORD-20261124202206-641', 1, 12.40, 0.70, '[{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":25},{\"product_id\":1,\"product_name\":\"Nasi Lemak Special\",\"price\":8.5,\"quantity\":1,\"stock\":28}]', 'waiting', NULL, NULL, '2026-11-24 19:22:06'),
('ORD-20261124202211-179', 1, 15.90, 0.90, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":31}]', 'waiting', NULL, NULL, '2026-11-24 19:22:11'),
('ORD-20261124202212-254', 1, 19.29, 1.09, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":31},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":24}]', 'waiting', NULL, NULL, '2026-11-24 19:22:12'),
('ORD-20261124202212-511', 1, 26.08, 1.48, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":31},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":3,\"stock\":24}]', 'waiting', NULL, NULL, '2026-11-24 19:22:12'),
('ORD-20261124202212-513', 1, 22.68, 1.28, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":31},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":2,\"stock\":24}]', 'waiting', NULL, NULL, '2026-11-24 19:22:12'),
('ORD-20261124202213-113', 1, 29.47, 1.67, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":31},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":4,\"stock\":24}]', 'waiting', NULL, NULL, '2026-11-24 19:22:13'),
('ORD-20261124202213-713', 1, 32.86, 1.86, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":1,\"stock\":31},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":5,\"stock\":24}]', 'waiting', NULL, NULL, '2026-11-24 19:22:13'),
('ORD-20261124202215-944', 1, 48.76, 2.76, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":2,\"stock\":31},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":5,\"stock\":24}]', 'waiting', NULL, NULL, '2026-11-24 19:22:15'),
('ORD-20261124202217-759', 1, 57.77, 3.27, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":2,\"stock\":31},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":5,\"stock\":24},{\"product_id\":1,\"product_name\":\"Nasi Lemak Special\",\"price\":8.5,\"quantity\":1,\"stock\":27}]', 'waiting', NULL, NULL, '2026-11-24 19:22:17'),
('ORD-20261124202219-885', 1, 73.67, 4.17, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":2,\"stock\":31},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":5,\"stock\":24},{\"product_id\":1,\"product_name\":\"Nasi Lemak Special\",\"price\":8.5,\"quantity\":1,\"stock\":27},{\"product_id\":5,\"product_name\":\"Tiramisu Cake\",\"price\":15,\"quantity\":1,\"stock\":40}]', 'waiting', NULL, NULL, '2026-11-24 19:22:19'),
('ORD-20261124202220-510', 1, 89.57, 5.07, '[{\"product_id\":3,\"product_name\":\"Chicken Chop Set\",\"price\":15,\"quantity\":2,\"stock\":31},{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":5,\"stock\":24},{\"product_id\":1,\"product_name\":\"Nasi Lemak Special\",\"price\":8.5,\"quantity\":1,\"stock\":27},{\"product_id\":5,\"product_name\":\"Tiramisu Cake\",\"price\":15,\"quantity\":2,\"stock\":40}]', 'paid', 75, '1cfe0527-b324-45d0-b05b-47989aafa950', '2026-11-24 19:22:20'),
('ORD-20261124203010-157', 1, 3.39, 0.19, '[{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":19}]', 'waiting', NULL, NULL, '2026-11-24 19:30:10'),
('ORD-20261124203010-177', 1, 12.40, 0.70, '[{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":19},{\"product_id\":1,\"product_name\":\"Nasi Lemak Special\",\"price\":8.5,\"quantity\":1,\"stock\":26}]', 'waiting', NULL, NULL, '2026-11-24 19:30:10'),
('ORD-20261124203012-798', 1, 28.30, 1.60, '[{\"product_id\":2,\"product_name\":\"Teh Tarik Ais\",\"price\":3.2,\"quantity\":1,\"stock\":19},{\"product_id\":1,\"product_name\":\"Nasi Lemak Special\",\"price\":8.5,\"quantity\":1,\"stock\":26},{\"product_id\":5,\"product_name\":\"Tiramisu Cake\",\"price\":15,\"quantity\":1,\"stock\":38}]', 'paid', 76, 'a63e37d1-4293-4de3-943b-639403aed239', '2026-11-24 19:30:12');

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
(1, 'Nasi Lemak Special', 'Mains', 8.50, 25, 'available', 'assets/default-food.jpeg'),
(2, 'Teh Tarik Ais', 'Beverages', 3.20, 18, 'available', 'assets/default-food.jpeg'),
(3, 'Chicken Chop Set', 'Mains', 15.00, 29, 'available', 'assets/default-food.jpeg'),
(4, 'Sirap Bandung', 'Beverages', 3.00, 62, 'available', 'assets/default-food.jpeg'),
(5, 'Tiramisu Cake', 'Desserts', 15.00, 37, 'available', 'assets/prod_1795457209_626.jpeg');

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
  `receipt_ref` varchar(50) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `sst_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','card','duitnow_qr') NOT NULL,
  `buyer_tin` varchar(50) DEFAULT 'EI00000000010',
  `buyer_name` varchar(150) DEFAULT 'Walk-in Customer (General Public)',
  `invoice_status` enum('pending','valid') DEFAULT 'pending',
  `validated_at` datetime DEFAULT NULL,
  `lhdn_uuid` varchar(36) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `receipt_ref`, `user_id`, `total_amount`, `sst_amount`, `payment_method`, `buyer_tin`, `buyer_name`, `invoice_status`, `validated_at`, `lhdn_uuid`, `created_at`) VALUES
(20, '#INV-1020', 2, 18.20, 0.00, 'cash', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, 'e773b772-9a75-4a23-a3a9-f39162ff6919', '2026-11-20 18:58:17'),
(21, '#INV-1021', 2, 18.20, 0.00, 'cash', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, '83e8897e-69f1-455c-96c1-b20483fa6e7c', '2026-11-20 18:58:22'),
(22, '#INV-1022', 2, 18.20, 0.00, 'cash', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, 'ff8e98a6-39ed-4720-b2ba-fb1740d1993e', '2026-11-20 19:50:04'),
(23, '#INV-1023', 2, 18.20, 0.00, 'cash', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, '0c312cb2-25ef-484a-ac54-b1a7b3f03565', '2026-11-20 19:54:52'),
(24, '#INV-1024', 2, 18.20, 0.00, 'cash', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, '5f8aeb76-e4c7-45a4-ac11-f695b65befd4', '2026-11-20 19:54:55'),
(25, '#INV-1025', 2, 18.20, 0.00, 'cash', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, '5d95aea9-7dc3-4966-85b2-b2dfe4cd6717', '2026-11-20 19:55:01'),
(26, '#INV-1026', 2, 18.20, 0.00, 'cash', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, 'a6443fd4-8d3e-4d2d-b229-493d948bf1e2', '2026-11-20 20:01:00'),
(27, '#INV-1027', 2, 19.29, 1.09, 'cash', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, '19057928-b4fe-4e87-8c38-e226da5037ff', '2026-11-20 20:23:23'),
(28, '#INV-1028', 2, 19.29, 1.09, 'cash', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, '0395228f-ff5f-4995-81ff-9fa66685b7fd', '2026-11-20 20:29:23'),
(29, '#INV-1029', 2, 19.29, 1.09, 'cash', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, '0b9afcde-4210-440b-b7f9-be625ae68972', '2026-11-20 20:30:08'),
(30, '#INV-1030', 2, 19.29, 1.09, 'cash', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, '7b9bd62b-07bb-4cc0-95cf-7b893cc56519', '2026-11-20 20:33:35'),
(31, '#INV-1031', 2, 19.29, 1.09, 'cash', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, '901afe4d-3f63-44df-abfe-be0b637e342c', '2026-11-20 20:33:42'),
(32, '#INV-1032', 2, 19.29, 1.09, 'cash', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, '8ddc48a5-32a0-4552-9ef5-76c4936bc504', '2026-11-20 20:33:44'),
(33, '#INV-1033', 2, 19.29, 1.09, 'cash', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, '4679d2b8-bdce-45e9-8583-8ce2742b8511', '2026-11-20 20:34:22'),
(34, '#INV-1034', 2, 19.29, 1.09, 'cash', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, 'c9dc4f70-6f6f-4378-8eab-ee7f2a4c0fc6', '2026-11-20 20:34:30'),
(35, '#INV-1035', 2, 19.29, 1.09, 'cash', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, '67308a02-1907-41d2-9ff2-e466153140c2', '2026-11-20 20:34:43'),
(36, '#INV-1036', 2, 19.29, 1.09, 'cash', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, '8214e078-3a13-4b1c-bb99-2e9bcbafc2f3', '2026-11-20 20:35:38'),
(37, '#INV-1037', 2, 19.29, 1.09, 'cash', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, 'eded8f12-1c32-490f-80ad-cbf350b18490', '2026-11-20 20:36:15'),
(38, '#INV-1038', 2, 19.29, 1.09, 'cash', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, '5fdce538-322a-452d-895c-38eb6f49f826', '2026-11-20 20:36:31'),
(39, '#INV-1039', 2, 19.29, 1.09, 'cash', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, '49910999-aaf1-4df3-a0dc-ad9300b31047', '2026-11-20 20:37:21'),
(40, '#INV-1040', 2, 19.29, 1.09, 'cash', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, '0fb9d4d0-827c-4078-957a-d5f0f77e10c7', '2026-11-20 20:40:25'),
(41, '#INV-1041', 2, 19.29, 1.09, 'cash', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, '37c321f8-c304-4fef-ba94-a22a4d35fea3', '2026-11-20 20:40:48'),
(42, '#INV-1042', 2, 19.29, 1.09, 'cash', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, 'bd7cbc6d-382d-40ff-9d0c-876708350af9', '2026-11-20 20:41:04'),
(43, '#INV-1043', 2, 19.29, 1.09, 'cash', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, 'e8047d58-ffb2-4ed0-9fc7-bbc7d4d0500c', '2026-11-20 20:43:51'),
(44, '#INV-1044', 1, 24.59, 1.39, 'cash', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, 'e1881cb3-231a-4765-b7ae-7b7523c1f82b', '2026-11-21 13:43:54'),
(45, '#INV-1045', 1, 24.59, 1.39, 'cash', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, '2973b243-a7fd-48f9-9b37-3f303097bbd8', '2026-11-21 13:50:45'),
(46, '#INV-1046', 1, 24.59, 1.39, 'cash', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, '6caed09a-311b-486b-9f46-b8678172d3a9', '2026-11-21 15:09:27'),
(47, '#INV-1047', 1, 24.59, 1.39, 'cash', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, '442f08cd-c958-4a0f-8675-c75db0114112', '2026-11-21 15:10:04'),
(48, '#INV-1048', 1, 24.59, 1.39, 'cash', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, 'ca2a276c-2619-4980-9034-a8cbf946e565', '2026-11-21 15:16:11'),
(49, '#INV-1049', 1, 24.59, 1.39, 'cash', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, '9fdeb06c-60ed-4e67-b7f8-6b78d00316b6', '2026-11-21 15:16:15'),
(50, '#INV-1050', 2, 6.57, 0.37, 'duitnow_qr', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, '3d1f5b10-463e-42ce-a8ae-134af6870205', '2026-11-21 15:38:51'),
(51, '#INV-1051', 2, 6.57, 0.37, 'duitnow_qr', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, '6df8e60b-6cb4-46a8-a183-699721c5fc07', '2026-11-21 15:55:01'),
(52, '#INV-1052', 2, 6.57, 0.37, 'duitnow_qr', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, 'a9af5d2a-e30d-4c8f-a056-de8299102b61', '2026-11-21 15:55:05'),
(53, '#INV-1053', 1, 12.72, 0.72, 'duitnow_qr', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, 'e9e2c0d1-77f9-4d5f-99b7-1d7a41066841', '2026-11-21 16:52:57'),
(54, '#INV-1054', 1, 9.01, 0.51, 'duitnow_qr', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, '20c5cd16-c153-4ff0-be2a-5ce6a906961b', '2026-11-21 16:53:16'),
(55, '#INV-1055', 1, 10.18, 0.58, 'cash', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, '377b57c3-4e7d-459b-a985-a8b98bdaf195', '2026-11-21 16:53:55'),
(56, '#INV-1056', 2, 54.17, 3.07, 'duitnow_qr', 'EI00000000010', 'Walk-in Customer (General Public)', 'valid', NULL, 'b9f979b0-0739-485d-af3d-ac1dd4b4d3df', '2026-11-23 18:09:56'),
(73, '#INV-1073', 1, 95.08, 5.38, 'duitnow_qr', 'EI00000000010', 'Walk-in Customer (General Public)', 'pending', NULL, 'a820198e-fafd-40b1-9af0-46211f69b72f', '2026-11-24 19:21:41'),
(74, '#INV-1074', 1, 12.40, 0.70, 'cash', 'EI00000000010', 'Walk-in Customer (General Public)', 'pending', NULL, NULL, '2026-11-24 19:22:09'),
(75, '#INV-1075', 1, 89.57, 5.07, 'duitnow_qr', 'EI00000000010', 'Walk-in Customer (General Public)', 'pending', NULL, '1cfe0527-b324-45d0-b05b-47989aafa950', '2026-11-24 19:22:34'),
(76, '#INV-1076', 1, 28.30, 1.60, 'duitnow_qr', 'EI00000000010', 'Walk-in Customer (General Public)', 'pending', NULL, 'a63e37d1-4293-4de3-943b-639403aed239', '2026-11-24 19:30:46');

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
(110, 55, 2, 3, 9.60),
(111, 56, 5, 1, 15.00),
(112, 56, 3, 1, 15.00),
(113, 56, 1, 1, 8.50),
(114, 56, 4, 1, 3.00),
(115, 56, 2, 3, 9.60),
(159, 73, 3, 1, 15.00),
(160, 73, 2, 1, 3.20),
(161, 73, 1, 1, 8.50),
(162, 73, 4, 1, 3.00),
(163, 73, 5, 4, 60.00),
(164, 74, 2, 1, 3.20),
(165, 74, 1, 1, 8.50),
(166, 75, 3, 2, 30.00),
(167, 75, 2, 5, 16.00),
(168, 75, 1, 1, 8.50),
(169, 75, 5, 2, 30.00),
(170, 76, 2, 1, 3.20),
(171, 76, 1, 1, 8.50),
(172, 76, 5, 1, 15.00);

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
-- Indexes for table `pending_qr_payments`
--
ALTER TABLE `pending_qr_payments`
  ADD PRIMARY KEY (`order_id`);

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
  MODIFY `ingredient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `transaction_items`
--
ALTER TABLE `transaction_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=173;

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
