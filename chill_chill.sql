-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 18, 2026 at 10:25 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `chill_chill`
--

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `cart_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `variant_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `slug`, `image`, `created_at`, `updated_at`) VALUES
(1, 'Cà phê Phin', 'ca-phe-phin', 'https://images.unsplash.com/photo-1544787210-282744e79c1d?w=400', '2026-03-22 03:05:01', NULL),
(2, 'Trà Trái Cây', 'tra-trai-cay', 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=400', '2026-03-22 03:05:01', NULL),
(3, 'Đá Xay', 'da-xay', 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=400', '2026-03-22 03:05:01', NULL),
(4, 'Bánh Ngọt', 'banh-ngot', 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=400', '2026-03-22 03:05:01', NULL),
(6, 'Topping', 'topping', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `categories_post`
--

CREATE TABLE `categories_post` (
  `categories_post_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2026_03_22_090430_create_shipping_address_table', 1),
(6, '2026_03_22_090431_create_categories_table', 1),
(7, '2026_03_22_090431_create_products_table', 1),
(8, '2026_03_22_090432_create_sizes_table', 1),
(9, '2026_03_22_090432_create_toppings_table', 1),
(10, '2026_03_22_090433_create_vouchers_table', 1),
(11, '2026_03_22_090434_create_payment_methods_table', 1),
(12, '2026_03_22_090434_create_user_vouchers_table', 1),
(13, '2026_03_22_090436_create_categories_post_table', 1),
(14, '2026_03_22_090440_create_product_variants_table', 1),
(15, '2026_03_22_090441_create_carts_table', 1),
(16, '2026_03_22_090442_create_orders_table', 1),
(17, '2026_03_22_090443_create_order_items_table', 1),
(18, '2026_03_22_090444_create_order_item_toppings_table', 1),
(19, '2026_03_22_090952_create_posts_table', 1),
(20, '2026_03_30_060910_add_image_to_toppings_table', 2),
(21, '2026_03_30_062205_create_product_topping_table', 3),
(22, '2026_04_08_042430_add_address_to_users_table', 4),
(23, '2026_04_08_042610_add_avatar_to_users_table', 5),
(24, '2026_04_08_060239_create_orders_table', 6);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_address` text COLLATE utf8mb4_unicode_ci,
  `order_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'delivery',
  `table_number` int DEFAULT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `total_amount` decimal(15,2) NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `items` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `customer_name`, `customer_phone`, `shipping_address`, `order_type`, `table_number`, `payment_method`, `total_amount`, `status`, `items`, `created_at`, `updated_at`) VALUES
(1, 3, 'hihi', '0385792442', 'Phường Tân Định, Quận 1, TP. Hồ Chí Minh', 'delivery', NULL, 'cash', '36000.00', 'pending', '{\"fbea9d05c20b72fe4f85d401d0623a6f\": {\"name\": \"Món Ngon Chill Chill 1\", \"image\": \"https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_6.png\", \"price\": \"36000.00\", \"quantity\": 1, \"toppings\": [], \"size_name\": \"Size S\", \"product_id\": 1, \"variant_id\": 1, \"topping_total\": 0}}', '2026-04-07 23:14:51', '2026-04-07 23:14:51'),
(2, 3, 'hihi', '', 'Phường Tân Định, Quận 1, TP. Hồ Chí Minh', 'dine_in', 30, 'cash', '36000.00', 'pending', '{\"fbea9d05c20b72fe4f85d401d0623a6f\": {\"name\": \"Món Ngon Chill Chill 1\", \"image\": \"https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_6.png\", \"price\": \"36000.00\", \"quantity\": 1, \"toppings\": [], \"size_name\": \"Size S\", \"product_id\": 1, \"variant_id\": 1, \"topping_total\": 0}}', '2026-04-08 00:13:14', '2026-04-08 00:13:14'),
(3, 3, 'hihi', '', '1222, Phường 5, Quận 11, TP. Hồ Chí Minh', 'dine_in', 20, 'cash', '36000.00', 'pending', '{\"fbea9d05c20b72fe4f85d401d0623a6f\": {\"name\": \"Món Ngon Chill Chill 1\", \"image\": \"https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_6.png\", \"price\": \"36000.00\", \"quantity\": 1, \"toppings\": [], \"size_name\": \"Size S\", \"product_id\": 1, \"variant_id\": 1, \"topping_total\": 0}}', '2026-04-08 00:17:33', '2026-04-08 00:17:33');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `payment_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `post_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `categories_post_id` bigint UNSIGNED NOT NULL,
  `auth_id` bigint UNSIGNED NOT NULL,
  `images` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `name`, `slug`, `description`, `status`, `image_url`, `created_at`, `updated_at`) VALUES
(1, 3, 'Món Ngon Chill Chill 1', 'mon-ngon-chill-chill-1-177417390180', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_6.png', '2026-03-22 03:05:01', '2026-03-30 00:24:32'),
(2, 3, 'Món Ngon Chill Chill 2', 'mon-ngon-chill-chill-2-177417390126', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_5.png', '2026-03-22 03:05:01', NULL),
(3, 4, 'Món Ngon Chill Chill 3', 'mon-ngon-chill-chill-3-177417390141', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_4.png', '2026-03-22 03:05:01', NULL),
(4, 4, 'Món Ngon Chill Chill 4', 'mon-ngon-chill-chill-4-177417390155', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_5.png', '2026-03-22 03:05:01', NULL),
(5, 3, 'Món Ngon Chill Chill 5', 'mon-ngon-chill-chill-5-177417390126', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_7.png', '2026-03-22 03:05:01', NULL),
(6, 4, 'Món Ngon Chill Chill 6', 'mon-ngon-chill-chill-6-177417390111', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_7.png', '2026-03-22 03:05:01', NULL),
(7, 3, 'Món Ngon Chill Chill 7', 'mon-ngon-chill-chill-7-177417390174', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_4.png', '2026-03-22 03:05:01', NULL),
(8, 3, 'Món Ngon Chill Chill 8', 'mon-ngon-chill-chill-8-177417390171', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_7.png', '2026-03-22 03:05:01', NULL),
(9, 4, 'Món Ngon Chill Chill 9', 'mon-ngon-chill-chill-9-177417390124', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_5.png', '2026-03-22 03:05:01', NULL),
(10, 4, 'Món Ngon Chill Chill 10', 'mon-ngon-chill-chill-10-177417390187', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_6.png', '2026-03-22 03:05:01', NULL),
(11, 2, 'Món Ngon Chill Chill 11', 'mon-ngon-chill-chill-11-177417390168', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_5.png', '2026-03-22 03:05:01', NULL),
(12, 1, 'Món Ngon Chill Chill 12', 'mon-ngon-chill-chill-12-177417390147', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_5.png', '2026-03-22 03:05:01', NULL),
(13, 2, 'Món Ngon Chill Chill 13', 'mon-ngon-chill-chill-13-177417390132', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_6.png', '2026-03-22 03:05:01', NULL),
(14, 3, 'Món Ngon Chill Chill 14', 'mon-ngon-chill-chill-14-177417390142', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_5.png', '2026-03-22 03:05:01', NULL),
(15, 2, 'Món Ngon Chill Chill 15', 'mon-ngon-chill-chill-15-177417390132', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_5.png', '2026-03-22 03:05:01', NULL),
(16, 2, 'Món Ngon Chill Chill 16', 'mon-ngon-chill-chill-16-177417390151', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_4.png', '2026-03-22 03:05:01', NULL),
(17, 3, 'Món Ngon Chill Chill 17', 'mon-ngon-chill-chill-17-177417390162', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_4.png', '2026-03-22 03:05:01', NULL),
(18, 2, 'Món Ngon Chill Chill 18', 'mon-ngon-chill-chill-18-177417390145', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_4.png', '2026-03-22 03:05:01', NULL),
(19, 1, 'Món Ngon Chill Chill 19', 'mon-ngon-chill-chill-19-177417390112', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_6.png', '2026-03-22 03:05:01', NULL),
(20, 1, 'Món Ngon Chill Chill 20', 'mon-ngon-chill-chill-20-177417390112', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_6.png', '2026-03-22 03:05:01', NULL),
(21, 3, 'Món Ngon Chill Chill 21', 'mon-ngon-chill-chill-21-177417390129', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_6.png', '2026-03-22 03:05:01', NULL),
(22, 1, 'Món Ngon Chill Chill 22', 'mon-ngon-chill-chill-22-177417390163', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_4.png', '2026-03-22 03:05:01', NULL),
(23, 3, 'Món Ngon Chill Chill 23', 'mon-ngon-chill-chill-23-177417390143', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_6.png', '2026-03-22 03:05:01', NULL),
(24, 3, 'Món Ngon Chill Chill 24', 'mon-ngon-chill-chill-24-177417390134', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_7.png', '2026-03-22 03:05:01', NULL),
(25, 4, 'Món Ngon Chill Chill 25', 'mon-ngon-chill-chill-25-177417390118', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_4.png', '2026-03-22 03:05:01', NULL),
(26, 2, 'Món Ngon Chill Chill 26', 'mon-ngon-chill-chill-26-177417390154', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_5.png', '2026-03-22 03:05:01', NULL),
(27, 4, 'Món Ngon Chill Chill 27', 'mon-ngon-chill-chill-27-177417390138', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_4.png', '2026-03-22 03:05:01', NULL),
(28, 2, 'Món Ngon Chill Chill 28', 'mon-ngon-chill-chill-28-177417390165', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_5.png', '2026-03-22 03:05:01', NULL),
(29, 2, 'Món Ngon Chill Chill 29', 'mon-ngon-chill-chill-29-177417390161', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_7.png', '2026-03-22 03:05:01', NULL),
(30, 3, 'Món Ngon Chill Chill 30', 'mon-ngon-chill-chill-30-177417390114', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_6.png', '2026-03-22 03:05:01', NULL),
(31, 2, 'Món Ngon Chill Chill 31', 'mon-ngon-chill-chill-31-177417390123', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_7.png', '2026-03-22 03:05:01', NULL),
(32, 3, 'Món Ngon Chill Chill 32', 'mon-ngon-chill-chill-32-177417390174', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_6.png', '2026-03-22 03:05:01', NULL),
(33, 1, 'Món Ngon Chill Chill 33', 'mon-ngon-chill-chill-33-177417390159', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_7.png', '2026-03-22 03:05:01', NULL),
(34, 2, 'Món Ngon Chill Chill 34', 'mon-ngon-chill-chill-34-177417390191', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_6.png', '2026-03-22 03:05:01', NULL),
(35, 4, 'Món Ngon Chill Chill 35', 'mon-ngon-chill-chill-35-177417390130', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_4.png', '2026-03-22 03:05:01', NULL),
(36, 1, 'Món Ngon Chill Chill 36', 'mon-ngon-chill-chill-36-177417390142', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_7.png', '2026-03-22 03:05:01', NULL),
(37, 4, 'Món Ngon Chill Chill 37', 'mon-ngon-chill-chill-37-177417390179', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_6.png', '2026-03-22 03:05:01', NULL),
(38, 3, 'Món Ngon Chill Chill 38', 'mon-ngon-chill-chill-38-177417390169', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_6.png', '2026-03-22 03:05:01', NULL),
(39, 3, 'Món Ngon Chill Chill 39', 'mon-ngon-chill-chill-39-177417390196', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_7.png', '2026-03-22 03:05:01', NULL),
(40, 1, 'Món Ngon Chill Chill 40', 'mon-ngon-chill-chill-40-177417390174', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_6.png', '2026-03-22 03:05:01', NULL),
(41, 4, 'Món Ngon Chill Chill 41', 'mon-ngon-chill-chill-41-177417390199', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_5.png', '2026-03-22 03:05:01', NULL),
(42, 3, 'Món Ngon Chill Chill 42', 'mon-ngon-chill-chill-42-177417390126', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_7.png', '2026-03-22 03:05:01', NULL),
(43, 1, 'Món Ngon Chill Chill 43', 'mon-ngon-chill-chill-43-177417390170', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_5.png', '2026-03-22 03:05:01', NULL),
(44, 2, 'Món Ngon Chill Chill 44', 'mon-ngon-chill-chill-44-177417390163', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_5.png', '2026-03-22 03:05:01', NULL),
(45, 4, 'Món Ngon Chill Chill 45', 'mon-ngon-chill-chill-45-177417390155', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_7.png', '2026-03-22 03:05:01', NULL),
(46, 1, 'Món Ngon Chill Chill 46', 'mon-ngon-chill-chill-46-177417390163', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_6.png', '2026-03-22 03:05:01', NULL),
(47, 1, 'Món Ngon Chill Chill 47', 'mon-ngon-chill-chill-47-177417390133', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_6.png', '2026-03-22 03:05:01', NULL),
(48, 6, 'thạch', 'mon-ngon-chill-chill-48-177417390172', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, 'https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_6.png', '2026-03-22 03:05:01', '2026-04-02 06:43:19'),
(49, 4, 'Chân Châu', 'mon-ngon-chill-chill-49-177417390113', 'Hương vị tuyệt hảo, được pha chế từ những nguyên liệu tươi ngon nhất. Phù hợp cho mọi khoảnh khắc trong ngày.', 1, '/images/c1.webp', '2026-03-22 03:05:01', '2026-04-02 06:26:28'),
(51, 2, 'G63', 'g63-1774417366', 'hihi', 1, '/images/c1.webp', '2026-03-24 22:42:46', '2026-03-30 00:26:43'),
(52, 1, 'hhhh', 'hhhh-1774855269', NULL, 1, NULL, '2026-03-30 00:21:09', '2026-03-30 00:21:09'),
(53, 1, 'hhhh', 'hhhh-1774855368', NULL, 1, NULL, '2026-03-30 00:22:48', '2026-03-30 00:22:48'),
(54, 2, 'Chân châu', 'cbr-1000rr-1774855383', NULL, 1, NULL, '2026-03-30 00:23:03', '2026-04-02 06:25:53');

-- --------------------------------------------------------

--
-- Table structure for table `product_topping`
--

CREATE TABLE `product_topping` (
  `product_id` bigint UNSIGNED NOT NULL,
  `topping_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_topping`
--

INSERT INTO `product_topping` (`product_id`, `topping_id`) VALUES
(1, 1),
(51, 1),
(54, 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `variant_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `size_id` bigint UNSIGNED NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`variant_id`, `product_id`, `size_id`, `price`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '36000.00', NULL, NULL),
(2, 1, 2, '50000.00', NULL, NULL),
(3, 2, 1, '35000.00', NULL, NULL),
(4, 2, 2, '52000.00', NULL, NULL),
(5, 3, 1, '35000.00', NULL, NULL),
(6, 3, 2, '62000.00', NULL, NULL),
(7, 4, 1, '35000.00', NULL, NULL),
(8, 4, 2, '62000.00', NULL, NULL),
(9, 5, 1, '43000.00', NULL, NULL),
(10, 5, 2, '62000.00', NULL, NULL),
(11, 6, 1, '39000.00', NULL, NULL),
(12, 6, 2, '64000.00', NULL, NULL),
(13, 7, 1, '35000.00', NULL, NULL),
(14, 7, 2, '61000.00', NULL, NULL),
(15, 8, 1, '42000.00', NULL, NULL),
(16, 8, 2, '54000.00', NULL, NULL),
(17, 9, 1, '42000.00', NULL, NULL),
(18, 9, 2, '51000.00', NULL, NULL),
(19, 10, 1, '40000.00', NULL, NULL),
(20, 10, 2, '56000.00', NULL, NULL),
(21, 11, 1, '45000.00', NULL, NULL),
(22, 11, 2, '52000.00', NULL, NULL),
(23, 12, 1, '35000.00', NULL, NULL),
(24, 12, 2, '60000.00', NULL, NULL),
(25, 13, 1, '44000.00', NULL, NULL),
(26, 13, 2, '62000.00', NULL, NULL),
(27, 14, 1, '33000.00', NULL, NULL),
(28, 14, 2, '57000.00', NULL, NULL),
(29, 15, 1, '34000.00', NULL, NULL),
(30, 15, 2, '59000.00', NULL, NULL),
(31, 16, 1, '36000.00', NULL, NULL),
(32, 16, 2, '64000.00', NULL, NULL),
(33, 17, 1, '34000.00', NULL, NULL),
(34, 17, 2, '63000.00', NULL, NULL),
(35, 18, 1, '31000.00', NULL, NULL),
(36, 18, 2, '54000.00', NULL, NULL),
(37, 19, 1, '45000.00', NULL, NULL),
(38, 19, 2, '65000.00', NULL, NULL),
(39, 20, 1, '41000.00', NULL, NULL),
(40, 20, 2, '54000.00', NULL, NULL),
(41, 21, 1, '41000.00', NULL, NULL),
(42, 21, 2, '51000.00', NULL, NULL),
(43, 22, 1, '44000.00', NULL, NULL),
(44, 22, 2, '54000.00', NULL, NULL),
(45, 23, 1, '38000.00', NULL, NULL),
(46, 23, 2, '56000.00', NULL, NULL),
(47, 24, 1, '30000.00', NULL, NULL),
(48, 24, 2, '54000.00', NULL, NULL),
(49, 25, 1, '40000.00', NULL, NULL),
(50, 25, 2, '56000.00', NULL, NULL),
(51, 26, 1, '32000.00', NULL, NULL),
(52, 26, 2, '55000.00', NULL, NULL),
(53, 27, 1, '39000.00', NULL, NULL),
(54, 27, 2, '56000.00', NULL, NULL),
(55, 28, 1, '35000.00', NULL, NULL),
(56, 28, 2, '58000.00', NULL, NULL),
(57, 29, 1, '32000.00', NULL, NULL),
(58, 29, 2, '58000.00', NULL, NULL),
(59, 30, 1, '45000.00', NULL, NULL),
(60, 30, 2, '59000.00', NULL, NULL),
(61, 31, 1, '45000.00', NULL, NULL),
(62, 31, 2, '55000.00', NULL, NULL),
(63, 32, 1, '32000.00', NULL, NULL),
(64, 32, 2, '53000.00', NULL, NULL),
(65, 33, 1, '37000.00', NULL, NULL),
(66, 33, 2, '64000.00', NULL, NULL),
(67, 34, 1, '44000.00', NULL, NULL),
(68, 34, 2, '63000.00', NULL, NULL),
(69, 35, 1, '37000.00', NULL, NULL),
(70, 35, 2, '61000.00', NULL, NULL),
(71, 36, 1, '41000.00', NULL, NULL),
(72, 36, 2, '55000.00', NULL, NULL),
(73, 37, 1, '42000.00', NULL, NULL),
(74, 37, 2, '60000.00', NULL, NULL),
(75, 38, 1, '42000.00', NULL, NULL),
(76, 38, 2, '58000.00', NULL, NULL),
(77, 39, 1, '38000.00', NULL, NULL),
(78, 39, 2, '61000.00', NULL, NULL),
(79, 40, 1, '41000.00', NULL, NULL),
(80, 40, 2, '62000.00', NULL, NULL),
(81, 41, 1, '33000.00', NULL, NULL),
(82, 41, 2, '52000.00', NULL, NULL),
(83, 42, 1, '35000.00', NULL, NULL),
(84, 42, 2, '54000.00', NULL, NULL),
(85, 43, 1, '34000.00', NULL, NULL),
(86, 43, 2, '52000.00', NULL, NULL),
(87, 44, 1, '35000.00', NULL, NULL),
(88, 44, 2, '64000.00', NULL, NULL),
(89, 45, 1, '32000.00', NULL, NULL),
(90, 45, 2, '62000.00', NULL, NULL),
(91, 46, 1, '40000.00', NULL, NULL),
(92, 46, 2, '54000.00', NULL, NULL),
(93, 47, 1, '40000.00', NULL, NULL),
(94, 47, 2, '59000.00', NULL, NULL),
(95, 48, 1, '43000.00', NULL, NULL),
(96, 48, 2, '64000.00', NULL, NULL),
(97, 49, 1, '31000.00', NULL, NULL),
(98, 49, 2, '52000.00', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `shipping_address`
--

CREATE TABLE `shipping_address` (
  `address_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address_line` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sizes`
--

CREATE TABLE `sizes` (
  `size_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sizes`
--

INSERT INTO `sizes` (`size_id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Size S', '2026-03-22 03:05:01', NULL),
(2, 'Size M', '2026-03-22 03:05:01', NULL),
(3, 'Size L', '2026-03-22 03:05:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `toppings`
--

CREATE TABLE `toppings` (
  `topping_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `toppings`
--

INSERT INTO `toppings` (`topping_id`, `name`, `price`, `image`, `status`, `created_at`, `updated_at`) VALUES
(1, 'ducaty', '1280.00', NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `point` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `address`, `avatar`, `password`, `phone`, `role`, `point`, `created_at`, `updated_at`) VALUES
(1, 'User_0385792442', NULL, NULL, NULL, '$2y$12$tfntTfQ1XZpRnR4zM/jw.uiy4yj4I3ljSj.1BhkCdrIapgrzLEq86', '0385792442', 'admin', 0, '2026-03-22 03:16:06', '2026-03-22 03:26:03'),
(2, 'haibaconbo', NULL, NULL, NULL, '$2y$12$gzhJTd7Na0sPKjvNeC/DKOcZ7Kd3mwKLiZ.Wu1vqaCnY0uCCCi3na', NULL, 'user', 0, '2026-03-26 06:06:35', '2026-03-26 06:06:35'),
(3, 'hihi', 'nguyenthanhtrungkien2305@gmail.com', '[\"Phường Tân Định, Quận 1, TP. Hồ Chí Minh\",\"1222, Phường 15, Quận 11, TP. Hồ Chí Minh\",\"1222, Phường 5, Quận 11, TP. Hồ Chí Minh\"]', 'uploads/avatars/1775622475_download.jpg', '$2y$12$if5YnusIk82OK8vBAip9Ae70gn6bQeNUhQVu7DcWVnT.ng/9wCnlu', NULL, 'user', 0, '2026-04-07 21:14:07', '2026-04-07 22:12:01'),
(4, 'huhuhu', NULL, NULL, NULL, '$2y$12$Lwauv.MK/WQkMw9yekEC.O62IPnxaQe3GVsnVZKry.9YB8HGiWPjG', NULL, 'user', 0, '2026-04-08 00:11:07', '2026-04-08 00:11:07');

-- --------------------------------------------------------

--
-- Table structure for table `user_vouchers`
--

CREATE TABLE `user_vouchers` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `voucher_id` bigint UNSIGNED NOT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT '0',
  `save_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vouchers`
--

CREATE TABLE `vouchers` (
  `voucher_id` bigint UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `usage_limit` int DEFAULT NULL,
  `used_count` int NOT NULL DEFAULT '0',
  `min_order` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `carts_user_id_foreign` (`user_id`),
  ADD KEY `carts_variant_id_foreign` (`variant_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`);

--
-- Indexes for table `categories_post`
--
ALTER TABLE `categories_post`
  ADD PRIMARY KEY (`categories_post_id`),
  ADD UNIQUE KEY `categories_post_slug_unique` (`slug`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`post_id`),
  ADD UNIQUE KEY `posts_slug_unique` (`slug`),
  ADD KEY `posts_categories_post_id_foreign` (`categories_post_id`),
  ADD KEY `posts_auth_id_foreign` (`auth_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD UNIQUE KEY `products_slug_unique` (`slug`),
  ADD KEY `products_category_id_foreign` (`category_id`);

--
-- Indexes for table `product_topping`
--
ALTER TABLE `product_topping`
  ADD PRIMARY KEY (`product_id`,`topping_id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`variant_id`),
  ADD KEY `product_variants_product_id_foreign` (`product_id`),
  ADD KEY `product_variants_size_id_foreign` (`size_id`);

--
-- Indexes for table `shipping_address`
--
ALTER TABLE `shipping_address`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `shipping_address_user_id_foreign` (`user_id`);

--
-- Indexes for table `sizes`
--
ALTER TABLE `sizes`
  ADD PRIMARY KEY (`size_id`);

--
-- Indexes for table `toppings`
--
ALTER TABLE `toppings`
  ADD PRIMARY KEY (`topping_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `users_name_unique` (`name`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_vouchers`
--
ALTER TABLE `user_vouchers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_vouchers_user_id_foreign` (`user_id`),
  ADD KEY `user_vouchers_voucher_id_foreign` (`voucher_id`);

--
-- Indexes for table `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`voucher_id`),
  ADD UNIQUE KEY `vouchers_code_unique` (`code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `cart_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `categories_post`
--
ALTER TABLE `categories_post`
  MODIFY `categories_post_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `payment_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `post_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `variant_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `shipping_address`
--
ALTER TABLE `shipping_address`
  MODIFY `address_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sizes`
--
ALTER TABLE `sizes`
  MODIFY `size_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `toppings`
--
ALTER TABLE `toppings`
  MODIFY `topping_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_vouchers`
--
ALTER TABLE `user_vouchers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `voucher_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carts_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_auth_id_foreign` FOREIGN KEY (`auth_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `posts_categories_post_id_foreign` FOREIGN KEY (`categories_post_id`) REFERENCES `categories_post` (`categories_post_id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_variants_size_id_foreign` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`size_id`) ON DELETE CASCADE;

--
-- Constraints for table `shipping_address`
--
ALTER TABLE `shipping_address`
  ADD CONSTRAINT `shipping_address_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_vouchers`
--
ALTER TABLE `user_vouchers`
  ADD CONSTRAINT `user_vouchers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_vouchers_voucher_id_foreign` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`voucher_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
