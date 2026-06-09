-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Cze 09, 2026 at 10:43 AM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `warehouse`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) NOT NULL,
  `entity` varchar(64) NOT NULL,
  `entity_id` bigint(20) DEFAULT NULL,
  `action` varchar(64) NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `performed_by` bigint(20) DEFAULT NULL,
  `performed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `entity`, `entity_id`, `action`, `payload`, `performed_by`, `performed_at`) VALUES
(1, 'orders', 2, 'created', '{\"order_number\":\"ORD-FBE9AFF7\",\"total_gross\":122.88}', 2, '2026-06-08 17:20:01'),
(2, 'orders', 2, 'status_changed', '{\"status\":\"NEW\"}', 1, '2026-06-08 17:25:31'),
(3, 'shipments', 2, 'updated', '{\"tracking\":\"3461342\"}', 1, '2026-06-08 17:25:51'),
(4, 'orders', 2, 'status_changed', '{\"status\":\"COMPLETED\"}', 1, '2026-06-08 17:25:57'),
(5, 'shipments', 2, 'updated', '{\"tracking\":\"3461342\"}', 1, '2026-06-08 17:26:02'),
(6, 'invoices', 1, 'created', '{\"invoice_number\":\"FV-2026-3EC651\"}', 1, '2026-06-08 17:26:24'),
(7, 'invoices', 1, 'status_changed', '{\"status\":\"PAID\"}', 1, '2026-06-08 17:26:28'),
(8, 'price_lists', 2, 'created', '{\"name\":\"Cennik Europejski\"}', 1, '2026-06-08 17:53:26'),
(9, 'orders', 3, 'created', '{\"order_number\":\"ORD-FD56C6BF\",\"total_gross\":12297.54}', 2, '2026-06-08 18:04:07'),
(10, 'products', 3, 'updated', '{\"sku\":\"FOOD-0001\",\"name\":\"Cukier\",\"description\":\"\",\"image_path\":null,\"category_id\":2,\"unit\":\"opak\",\"is_active\":1,\"type\":\"food\",\"warranty_months\":null,\"expiration_date\":\"2028-10-09\",\"image_changed\":false}', 1, '2026-06-08 23:56:09'),
(11, 'products', 2, 'updated', '{\"sku\":\"ELECTRONIC-0001\",\"name\":\"Laptop Lenovo LOQ\",\"description\":\"\",\"image_path\":null,\"category_id\":1,\"unit\":\"szt\",\"is_active\":0,\"type\":\"electronic\",\"warranty_months\":24,\"expiration_date\":null,\"image_changed\":false}', 1, '2026-06-08 23:56:22'),
(12, 'products', 19, 'updated', '{\"sku\":\"ELECT-0005\",\"name\":\"Klawiatura mechaniczna\",\"description\":\"Klawiatura mechaniczna RGB, przełączniki czerwone\",\"image_path\":\"assets\\/uploads\\/products\\/product_19_d5d893bc1ba3a2e1.png\",\"category_id\":1,\"unit\":\"szt\",\"is_active\":1,\"type\":\"electronic\",\"warranty_months\":24,\"expiration_date\":null,\"image_changed\":true}', 1, '2026-06-09 07:42:42'),
(13, 'orders', 5, 'created', '{\"order_number\":\"ORD-20260609-F008\",\"total_gross\":17158.5}', 2, '2026-06-09 07:53:03'),
(14, 'orders', 5, 'status_changed', '{\"status\":\"COMPLETED\"}', 1, '2026-06-09 07:54:32'),
(15, 'shipments', 5, 'updated', '{\"tracking\":\"9768654\"}', 1, '2026-06-09 07:54:41');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `csv_imports`
--

CREATE TABLE `csv_imports` (
  `id` bigint(20) NOT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `filename` varchar(255) NOT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'PENDING',
  `errors` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`errors`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `vat_number` varchar(32) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_email` varchar(128) DEFAULT NULL,
  `billing_terms` varchar(64) NOT NULL DEFAULT 'prepayment',
  `price_list_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `company_name`, `vat_number`, `address`, `contact_email`, `billing_terms`, `price_list_id`, `created_at`) VALUES
(1, 'test', '2222222', 'aa', 'example@example.com', 'prepayment', 1, '2026-06-08 08:19:06');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `inventory_items`
--

CREATE TABLE `inventory_items` (
  `id` bigint(20) NOT NULL,
  `product_id` bigint(20) NOT NULL,
  `warehouse_id` bigint(20) NOT NULL,
  `quantity_on_hand` decimal(18,3) NOT NULL DEFAULT 0.000,
  `quantity_reserved` decimal(18,3) NOT NULL DEFAULT 0.000,
  `min_stock` decimal(18,3) NOT NULL DEFAULT 0.000,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_items`
--

INSERT INTO `inventory_items` (`id`, `product_id`, `warehouse_id`, `quantity_on_hand`, `quantity_reserved`, `min_stock`, `updated_at`) VALUES
(1, 2, 1, 10.000, 2.000, 2.000, '2026-06-08 18:04:07'),
(2, 3, 1, 100.000, 10.000, 10.000, '2026-06-08 17:20:01'),
(3, 8, 1, 500.000, 0.000, 50.000, '2026-06-08 19:05:19'),
(4, 9, 1, 2000.000, 0.000, 200.000, '2026-06-08 19:05:19'),
(5, 10, 1, 60.000, 0.000, 8.000, '2026-06-08 19:05:19'),
(6, 11, 1, 90.000, 0.000, 12.000, '2026-06-08 19:05:19'),
(7, 16, 1, 18.000, 0.000, 3.000, '2026-06-08 19:05:19'),
(8, 17, 1, 45.000, 0.000, 8.000, '2026-06-08 19:05:19'),
(9, 18, 1, 32.000, 0.000, 5.000, '2026-06-08 19:05:19'),
(10, 19, 1, 55.000, 50.000, 8.000, '2026-06-09 07:53:03'),
(11, 4, 1, 240.000, 0.000, 40.000, '2026-06-08 19:05:19'),
(12, 5, 1, 80.000, 0.000, 15.000, '2026-06-08 19:05:19'),
(13, 6, 1, 120.000, 0.000, 20.000, '2026-06-08 19:05:19'),
(14, 7, 1, 160.000, 0.000, 25.000, '2026-06-08 19:05:19'),
(15, 12, 1, 35.000, 0.000, 5.000, '2026-06-08 19:05:19'),
(16, 13, 1, 48.000, 0.000, 6.000, '2026-06-08 19:05:19'),
(17, 14, 1, 72.000, 0.000, 10.000, '2026-06-08 19:05:19'),
(18, 15, 1, 110.000, 0.000, 15.000, '2026-06-08 19:05:19');

--
-- Wyzwalacze `inventory_items`
--
DELIMITER $$
CREATE TRIGGER `trg_inventory_updated_at` BEFORE UPDATE ON `inventory_items` FOR EACH ROW BEGIN
  SET NEW.updated_at = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint(20) NOT NULL,
  `order_id` bigint(20) NOT NULL,
  `invoice_number` varchar(64) NOT NULL,
  `issued_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `due_date` date DEFAULT NULL,
  `total_net` decimal(18,4) NOT NULL,
  `total_gross` decimal(18,4) NOT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'UNPAID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `order_id`, `invoice_number`, `issued_at`, `due_date`, `total_net`, `total_gross`, `status`) VALUES
(1, 2, 'FV-2026-3EC651', '2026-06-08 17:26:24', '2026-06-08', 99.9000, 122.8800, 'PAID');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) NOT NULL,
  `order_number` varchar(64) NOT NULL,
  `customer_id` bigint(20) NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'CREATED',
  `total_net` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `total_gross` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `customer_id`, `user_id`, `status`, `total_net`, `total_gross`, `created_at`, `updated_at`) VALUES
(1, 'ORD-6D4FA637', 1, NULL, 'NEW', 0.0000, 0.0000, '2026-06-08 08:21:27', '2026-06-08 08:21:27'),
(2, 'ORD-FBE9AFF7', 1, 2, 'COMPLETED', 99.9000, 122.8800, '2026-06-08 17:20:01', '2026-06-08 17:25:57'),
(3, 'ORD-FD56C6BF', 1, 2, 'COMPLETED', 9998.0000, 12297.5400, '2026-06-08 18:04:07', '2026-06-08 23:46:38'),
(5, 'ORD-20260609-F008', 1, 2, 'COMPLETED', 13950.0000, 17158.5000, '2026-06-09 07:53:03', '2026-06-09 07:54:32');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `order_lines`
--

CREATE TABLE `order_lines` (
  `id` bigint(20) NOT NULL,
  `order_id` bigint(20) NOT NULL,
  `product_id` bigint(20) NOT NULL,
  `warehouse_id` bigint(20) DEFAULT NULL,
  `quantity` decimal(18,3) NOT NULL,
  `unit_price` decimal(18,4) NOT NULL,
  `line_total` decimal(18,4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_lines`
--

INSERT INTO `order_lines` (`id`, `order_id`, `product_id`, `warehouse_id`, `quantity`, `unit_price`, `line_total`) VALUES
(1, 2, 3, 1, 10.000, 9.9900, 99.9000),
(2, 3, 2, 1, 2.000, 4999.0000, 9998.0000),
(3, 5, 19, 1, 50.000, 279.0000, 13950.0000);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `price_lists`
--

CREATE TABLE `price_lists` (
  `id` bigint(20) NOT NULL,
  `name` varchar(128) NOT NULL,
  `currency` varchar(8) NOT NULL DEFAULT 'PLN',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `price_lists`
--

INSERT INTO `price_lists` (`id`, `name`, `currency`, `created_at`) VALUES
(1, 'Cennik standardowy', 'PLN', '2026-06-08 16:39:21'),
(2, 'Cennik Europejski', 'EUR', '2026-06-08 17:53:26');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `products`
--

CREATE TABLE `products` (
  `id` bigint(20) NOT NULL,
  `sku` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `category_id` bigint(20) DEFAULT NULL,
  `unit` varchar(32) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `type` varchar(20) NOT NULL DEFAULT 'electronic',
  `warranty_months` int(11) DEFAULT NULL,
  `expiration_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `sku`, `name`, `description`, `image_path`, `category_id`, `unit`, `is_active`, `created_at`, `updated_at`, `type`, `warranty_months`, `expiration_date`) VALUES
(2, 'ELECTRONIC-0001', 'Laptop Lenovo LOQ', '', NULL, 1, 'szt', 0, '2026-06-07 23:57:18', '2026-06-08 23:56:22', 'electronic', 24, NULL),
(3, 'FOOD-0001', 'Cukier', '', NULL, 2, 'opak', 1, '2026-06-08 08:52:05', '2026-06-08 23:56:09', 'food', NULL, '2028-10-09'),
(4, 'FOOD-0002', 'Mleko UHT 3,2% 1L', 'Mleko pasteryzowane UHT, karton 1 litr', NULL, 2, 'szt', 1, '2026-06-08 19:05:19', '2026-06-08 19:05:19', 'food', NULL, '2026-09-06'),
(5, 'FOOD-0003', 'Chleb pszenny 500 g', 'Świeży chleb pszenny, opakowanie 500 g', NULL, 2, 'szt', 1, '2026-06-08 19:05:19', '2026-06-08 19:43:44', 'food', NULL, '2026-06-11'),
(6, 'FOOD-0004', 'Masło extra 200 g', 'Masło 82% tłuszczu, kostka 200 g', NULL, 2, 'szt', 1, '2026-06-08 19:05:19', '2026-06-08 19:43:58', 'food', NULL, '2026-07-08'),
(7, 'FOOD-0005', 'Jogurt naturalny 400 g', 'Jogurt naturalny, kubek 400 g', NULL, 2, 'szt', 1, '2026-06-08 19:05:19', '2026-06-08 19:05:19', 'food', NULL, '2026-06-22'),
(8, 'BUILD-0001', 'Cement portlandzki 25 kg', 'Cement CEM I 42,5 R, worek 25 kg', NULL, 3, 'szt', 1, '2026-06-08 19:05:19', '2026-06-08 19:05:19', 'building', NULL, NULL),
(9, 'BUILD-0002', 'Pustak ceramiczny 12x25x38 cm', 'Pustak ceramiczny pełny, format 12x25x38 cm', NULL, 3, 'szt', 1, '2026-06-08 19:05:19', '2026-06-08 19:44:32', 'building', NULL, NULL),
(10, 'BUILD-0003', 'Wełna mineralna 100 mm', 'Mata z wełny mineralnej, grubość 100 mm, 6 m2', NULL, 3, 'opak', 1, '2026-06-08 19:05:19', '2026-06-08 19:45:19', 'building', NULL, NULL),
(11, 'BUILD-0004', 'Grunt głęboko penetrujący 5 L', 'Grunt pod tynki i farby, kanister 5 litrów', NULL, 3, 'szt', 1, '2026-06-08 19:05:19', '2026-06-08 19:45:38', 'building', NULL, NULL),
(12, 'TOOL-0001', 'Wiertarka udarowa 800 W', 'Wiertarka udarowa z regulacją obrotów, 800 W', NULL, 4, 'szt', 1, '2026-06-08 19:05:19', '2026-06-08 19:45:53', 'tools', 24, NULL),
(13, 'TOOL-0002', 'Zestaw kluczy nasadowych 1/2\"', 'Zestaw 32 elementów, grzechotka 1/2 cala', NULL, 4, 'kpl', 1, '2026-06-08 19:05:19', '2026-06-08 19:46:07', 'tools', 12, NULL),
(14, 'TOOL-0003', 'Poziomica aluminiowa 60 cm', 'Poziomica 3 libelle, profil aluminiowy 60 cm', NULL, 4, 'szt', 1, '2026-06-08 19:05:19', '2026-06-08 19:05:19', 'tools', 24, NULL),
(15, 'TOOL-0004', 'Młotek ciesielski 500 g', 'Młotek ciesielski z trzonkiem fibrowym, 500 g', NULL, 4, 'szt', 1, '2026-06-08 19:05:19', '2026-06-08 19:46:38', 'tools', 12, NULL),
(16, 'ELECT-0002', 'Monitor 27\" QHD', 'Monitor IPS 27 cali, rozdzielczość 2560x1440 px', NULL, 1, 'szt', 1, '2026-06-08 19:05:19', '2026-06-08 19:47:05', 'electronic', 36, NULL),
(17, 'ELECT-0003', 'Słuchawki bezprzewodowe BT', 'Słuchawki nauszne Bluetooth 5.3 z ANC', NULL, 1, 'szt', 1, '2026-06-08 19:05:19', '2026-06-08 19:47:19', 'electronic', 24, NULL),
(18, 'ELECT-0004', 'Router Wi-Fi 6', 'Router dual-band AX3000, 4 porty Gigabit', NULL, 1, 'szt', 1, '2026-06-08 19:05:19', '2026-06-08 19:05:19', 'electronic', 24, NULL),
(19, 'ELECT-0005', 'Klawiatura mechaniczna', 'Klawiatura mechaniczna RGB, przełączniki czerwone', 'assets/uploads/products/product_19_d5d893bc1ba3a2e1.png', 1, 'szt', 1, '2026-06-08 19:05:19', '2026-06-09 07:42:42', 'electronic', 24, NULL);

--
-- Wyzwalacze `products`
--
DELIMITER $$
CREATE TRIGGER `trg_products_updated_at` BEFORE UPDATE ON `products` FOR EACH ROW BEGIN
  SET NEW.updated_at = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `product_categories`
--

CREATE TABLE `product_categories` (
  `id` bigint(20) NOT NULL,
  `name` varchar(128) NOT NULL,
  `parent_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`id`, `name`, `parent_id`) VALUES
(1, 'Elektronika', NULL),
(2, 'Żywność', NULL),
(3, 'Materiały budowlane', NULL),
(4, 'Narzędzia', NULL);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `product_prices`
--

CREATE TABLE `product_prices` (
  `id` bigint(20) NOT NULL,
  `product_id` bigint(20) NOT NULL,
  `price_list_id` bigint(20) NOT NULL,
  `price` decimal(18,4) NOT NULL,
  `min_quantity` decimal(18,3) NOT NULL DEFAULT 1.000,
  `valid_from` date DEFAULT NULL,
  `valid_to` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_prices`
--

INSERT INTO `product_prices` (`id`, `product_id`, `price_list_id`, `price`, `min_quantity`, `valid_from`, `valid_to`) VALUES
(1, 2, 1, 4999.0000, 1.000, NULL, NULL),
(2, 3, 1, 9.9900, 1.000, NULL, NULL),
(3, 8, 1, 29.9000, 1.000, NULL, NULL),
(4, 9, 1, 3.4500, 1.000, NULL, NULL),
(5, 10, 1, 89.0000, 1.000, NULL, NULL),
(6, 11, 1, 42.5000, 1.000, NULL, NULL),
(7, 16, 1, 1299.0000, 1.000, NULL, NULL),
(8, 17, 1, 349.0000, 1.000, NULL, NULL),
(9, 18, 1, 399.0000, 1.000, NULL, NULL),
(10, 19, 1, 279.0000, 1.000, NULL, NULL),
(11, 4, 1, 4.4900, 1.000, NULL, NULL),
(12, 5, 1, 5.9900, 1.000, NULL, NULL),
(13, 6, 1, 8.4900, 1.000, NULL, NULL),
(14, 7, 1, 3.7900, 1.000, NULL, NULL),
(15, 12, 1, 249.0000, 1.000, NULL, NULL),
(16, 13, 1, 189.0000, 1.000, NULL, NULL),
(17, 14, 1, 54.9000, 1.000, NULL, NULL),
(18, 15, 1, 39.9000, 1.000, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `reservations`
--

CREATE TABLE `reservations` (
  `id` bigint(20) NOT NULL,
  `order_id` bigint(20) DEFAULT NULL,
  `product_id` bigint(20) NOT NULL,
  `warehouse_id` bigint(20) NOT NULL,
  `quantity` decimal(18,3) NOT NULL,
  `reserved_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'ACTIVE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `order_id`, `product_id`, `warehouse_id`, `quantity`, `reserved_at`, `expires_at`, `status`) VALUES
(1, 5, 19, 1, 50.000, '2026-06-09 07:53:03', NULL, 'ACTIVE');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `roles`
--

CREATE TABLE `roles` (
  `id` smallint(6) NOT NULL,
  `name` varchar(64) NOT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `permissions`) VALUES
(1, 'admin', '{\"all\": true}'),
(2, 'customer', '{\"orders\": true, \"invoices\": true}');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `shipments`
--

CREATE TABLE `shipments` (
  `id` bigint(20) NOT NULL,
  `order_id` bigint(20) NOT NULL,
  `carrier` varchar(128) DEFAULT NULL,
  `tracking_number` varchar(128) DEFAULT NULL,
  `shipped_at` timestamp NULL DEFAULT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'PENDING'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipments`
--

INSERT INTO `shipments` (`id`, `order_id`, `carrier`, `tracking_number`, `shipped_at`, `status`) VALUES
(1, 2, 'DPD', '3461342', '2026-06-08 17:25:51', 'DELIVERED'),
(2, 5, 'DPD', '9768654', NULL, 'DELIVERED');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `username` varchar(128) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(128) NOT NULL,
  `role_id` smallint(6) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `customer_id`, `username`, `password_hash`, `email`, `role_id`, `is_active`, `created_at`) VALUES
(1, NULL, 'admin', '$2y$10$5c3EV38bbb.ubbFKJX0V7ebfG9FqAcvpq.6UaJ8qCObWb3VcFWa.C', 'admin@marketflow.pl', 1, 1, '2026-06-08 16:39:21'),
(2, 1, 'klient', '$2y$10$PdOoo2pLyyFvlrVC/EX1.OYtwsVrRTmsfro44Q7dTDY.dV1HyziXy', 'example@example.com', 2, 1, '2026-06-08 16:39:21'),
(3, NULL, 'JanKowalski', '$2y$10$N7CdWMV7rs0JQ/Jl5V3.IOg3sshu6.Tf02RXtqnLL1F2VfgeL0Zsm', 'test@test.pl', 2, 1, '2026-06-09 02:26:48');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `warehouses`
--

CREATE TABLE `warehouses` (
  `id` bigint(20) NOT NULL,
  `code` varchar(32) NOT NULL,
  `name` varchar(128) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `warehouses`
--

INSERT INTO `warehouses` (`id`, `code`, `name`, `address`, `created_at`) VALUES
(1, '2', 'azbest', 'a', '2026-06-08 08:14:38');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_auditlogs_user` (`performed_by`);

--
-- Indeksy dla tabeli `csv_imports`
--
ALTER TABLE `csv_imports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_csv_imports_customer` (`customer_id`);

--
-- Indeksy dla tabeli `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_customers_price_list` (`price_list_id`);

--
-- Indeksy dla tabeli `inventory_items`
--
ALTER TABLE `inventory_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_inventory_product_warehouse` (`product_id`,`warehouse_id`),
  ADD KEY `fk_inventory_warehouse` (`warehouse_id`),
  ADD KEY `idx_inventory_product_warehouse` (`product_id`,`warehouse_id`);

--
-- Indeksy dla tabeli `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD KEY `fk_invoices_order` (`order_id`);

--
-- Indeksy dla tabeli `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `fk_orders_user` (`user_id`),
  ADD KEY `idx_orders_customer` (`customer_id`);

--
-- Indeksy dla tabeli `order_lines`
--
ALTER TABLE `order_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orderlines_product` (`product_id`),
  ADD KEY `fk_orderlines_warehouse` (`warehouse_id`),
  ADD KEY `idx_order_lines_order` (`order_id`);

--
-- Indeksy dla tabeli `price_lists`
--
ALTER TABLE `price_lists`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `fk_products_category` (`category_id`),
  ADD KEY `idx_products_name` (`name`);

--
-- Indeksy dla tabeli `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `fk_product_categories_parent` (`parent_id`);

--
-- Indeksy dla tabeli `product_prices`
--
ALTER TABLE `product_prices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_price_product_list_minqty_validfrom` (`product_id`,`price_list_id`,`min_quantity`,`valid_from`),
  ADD KEY `fk_price_pricelist` (`price_list_id`);

--
-- Indeksy dla tabeli `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reservations_order` (`order_id`),
  ADD KEY `fk_reservations_warehouse` (`warehouse_id`),
  ADD KEY `idx_reservations_product` (`product_id`);

--
-- Indeksy dla tabeli `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indeksy dla tabeli `shipments`
--
ALTER TABLE `shipments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_shipments_order` (`order_id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_users_customer` (`customer_id`),
  ADD KEY `fk_users_role` (`role_id`);

--
-- Indeksy dla tabeli `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `csv_imports`
--
ALTER TABLE `csv_imports`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inventory_items`
--
ALTER TABLE `inventory_items`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_lines`
--
ALTER TABLE `order_lines`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `price_lists`
--
ALTER TABLE `price_lists`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `product_prices`
--
ALTER TABLE `product_prices`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `shipments`
--
ALTER TABLE `shipments`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_auditlogs_user` FOREIGN KEY (`performed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `csv_imports`
--
ALTER TABLE `csv_imports`
  ADD CONSTRAINT `fk_csvimports_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `fk_customers_price_list` FOREIGN KEY (`price_list_id`) REFERENCES `price_lists` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `inventory_items`
--
ALTER TABLE `inventory_items`
  ADD CONSTRAINT `fk_inventory_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `fk_inventory_warehouse` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`);

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `fk_invoices_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_lines`
--
ALTER TABLE `order_lines`
  ADD CONSTRAINT `fk_orderlines_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_orderlines_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `fk_orderlines_warehouse` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD CONSTRAINT `fk_product_categories_parent` FOREIGN KEY (`parent_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_prices`
--
ALTER TABLE `product_prices`
  ADD CONSTRAINT `fk_price_pricelist` FOREIGN KEY (`price_list_id`) REFERENCES `price_lists` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_price_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `fk_reservations_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reservations_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `fk_reservations_warehouse` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`);

--
-- Constraints for table `shipments`
--
ALTER TABLE `shipments`
  ADD CONSTRAINT `fk_shipments_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
