-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 14, 2026 at 09:42 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.28

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
-- Table structure for table `attendances`
--

CREATE TABLE `attendances` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `check_in` datetime DEFAULT NULL,
  `check_out` datetime DEFAULT NULL,
  `scheduled_end_time` datetime DEFAULT NULL,
  `checkout_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendances`
--

INSERT INTO `attendances` (`id`, `user_id`, `date`, `check_in`, `check_out`, `scheduled_end_time`, `checkout_note`, `created_at`, `updated_at`) VALUES
(22, 5, '2026-06-22', '2026-06-22 19:14:26', '2026-06-22 19:19:15', '2026-06-22 23:14:26', NULL, '2026-06-22 12:14:26', '2026-06-22 12:19:15'),
(23, 5, '2026-07-21', '2026-07-21 08:32:57', '2026-07-21 10:54:04', NULL, NULL, '2026-07-21 01:32:57', '2026-07-21 03:54:04'),
(24, 5, '2026-07-21', '2026-07-21 10:55:23', '2026-07-21 10:55:27', NULL, NULL, '2026-07-21 03:55:23', '2026-07-21 03:55:27'),
(25, 6, '2026-07-21', '2026-07-21 10:59:47', NULL, NULL, NULL, '2026-07-21 03:59:47', '2026-07-21 03:59:47');

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `banner_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `badge` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `button_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Xem ngay combo',
  `button_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '/combo',
  `button_secondary_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `button_secondary_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bg_gradient` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'from-espresso via-coral to-amber-600',
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'combo_banner',
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`banner_id`, `title`, `badge`, `description`, `button_text`, `button_link`, `button_secondary_text`, `button_secondary_link`, `image_url`, `bg_gradient`, `position`, `product_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'COMBO TIẾT KIỆM – UỐNG LÀ MÊ!', 'Combo Tiết Kiệm Độc Quyền', 'Chọn ngay combo đồ uống & bánh ngọt yêu thích với giá ưu đãi cực sốc lên đến 25%.', 'Xem ngay combo', '/combo', NULL, NULL, NULL, 'from-espresso via-coral to-amber-600', 'combo_banner', NULL, 1, '2026-08-10 16:03:04', '2026-08-10 16:03:04'),
(2, 'Thư giãn từng nét - Giao hòa cảm xúc', 'Thưởng thức hương vị chuẩn Gu', 'Nơi dừng chân lý tưởng cho những tách cà phê nguyên chất đậm đà và ly trà sữa ngọt ngào. Gọi món ngay để nhận ưu đãi giao tận nơi!', 'Khám phá Menu ngay', '/san-pham', 'Món bán chạy', '#best-sellers', NULL, 'from-espresso via-coral to-amber-600', 'home_hero', NULL, 1, '2026-08-10 16:11:50', '2026-08-10 16:11:50'),
(3, 'Giảm 20% toàn bộ đơn hàng!', 'ƯU ĐÃI THÁNG 8', 'Nhập mã CHILL20 khi thanh toán online.', 'Đổi mã ngay', '/cart', NULL, NULL, 'https://images.unsplash.com/photo-1559525839-b184a4d698c7?q=80&w=1000&auto=format&fit=crop', 'from-espresso via-coral to-amber-600', 'home_promo', NULL, 1, '2026-08-10 16:11:50', '2026-08-10 16:11:50');

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `cart_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `variant_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`cart_id`, `user_id`, `variant_id`, `quantity`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 5, 1, NULL, '2026-08-10 14:57:29', '2026-08-10 14:57:29'),
(2, 8, 5, 1, NULL, '2026-08-14 05:06:20', '2026-08-14 05:06:20');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `cart_item_id` bigint UNSIGNED NOT NULL,
  `cart_id` bigint UNSIGNED DEFAULT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `variant_id` bigint UNSIGNED DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `toppings` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ice_level` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '100',
  `sugar_level` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '100',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`cart_item_id`, `cart_id`, `product_id`, `variant_id`, `quantity`, `toppings`, `ice_level`, `sugar_level`, `created_at`, `updated_at`) VALUES
(23, 1, 54, 101, 1, '[]', '100', '100', '2026-08-12 03:33:27', '2026-08-12 03:33:27'),
(24, 1, 3, 5, 1, '[]', '100', '100', '2026-08-12 03:33:27', '2026-08-12 03:33:27'),
(25, 1, 4, 7, 3, '[]', '100', '100', '2026-08-12 03:33:27', '2026-08-12 03:33:27'),
(26, 1, 5, 9, 2, '[]', '100', '100', '2026-08-12 03:33:27', '2026-08-12 03:33:27');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `slug`, `image`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Cà phê Phin', 'ca-phe-phin', 'https://images.unsplash.com/photo-1544787210-282744e79c1d?w=400', 1, '2026-03-22 03:05:01', NULL),
(2, 'Trà Trái Cây', 'tra-trai-cay', 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=400', 1, '2026-03-22 03:05:01', NULL),
(3, 'Đá Xay', 'da-xay', 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=400', 1, '2026-03-22 03:05:01', NULL),
(4, 'Bánh Ngọt', 'banh-ngot', 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=400', 1, '2026-03-22 03:05:01', NULL),
(6, 'Topping', 'topping', NULL, 1, NULL, NULL),
(7, 'Trà sữa', 'tra-sua', NULL, 1, '2026-08-14 04:28:13', '2026-08-14 04:28:13'),
(8, 'Khác', 'khac', NULL, 1, '2026-08-14 04:30:29', '2026-08-14 04:30:29');

-- --------------------------------------------------------

--
-- Table structure for table `categories_post`
--

CREATE TABLE `categories_post` (
  `categories_post_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories_post`
--

INSERT INTO `categories_post` (`categories_post_id`, `name`, `slug`, `parent_id`, `created_at`, `updated_at`) VALUES
(1, 'Coffeeholic', 'coffeeholic', NULL, '2026-07-29 11:49:25', '2026-07-29 11:49:25'),
(2, 'Teaholic', 'teaholic', NULL, '2026-07-29 11:49:25', '2026-07-29 11:49:25'),
(3, 'Blog', 'blog', NULL, '2026-07-29 11:49:25', '2026-07-29 11:49:25');

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` bigint UNSIGNED NOT NULL,
  `chat_session_id` bigint UNSIGNED NOT NULL,
  `sender_type` enum('customer','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `chat_session_id`, `sender_type`, `message`, `read_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'customer', 'tôi có 200k cho tôi 3 sản phẩm phù hợp tôi thích uống cafe', NULL, '2026-07-28 03:37:33', '2026-07-28 03:37:33'),
(2, 1, 'admin', 'Dạ, hiện tại trợ lý ảo AI của Chill Chill Coffee đang tạm ngắt kết nối. Bạn vui lòng chờ nhân viên tư vấn trong giây lát nhé! ☕', '2026-07-28 03:37:33', '2026-07-28 03:37:33', '2026-07-28 03:37:33');

-- --------------------------------------------------------

--
-- Table structure for table `chat_sessions`
--

CREATE TABLE `chat_sessions` (
  `id` bigint UNSIGNED NOT NULL,
  `session_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `is_bot_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chat_sessions`
--

INSERT INTO `chat_sessions` (`id`, `session_token`, `user_id`, `status`, `is_bot_enabled`, `created_at`, `updated_at`) VALUES
(1, 'TIdCt8aEac9f5rxTBkhM6tuF0yPR20OC1ycDkAvH', 5, 'active', 1, '2026-07-28 03:36:58', '2026-07-28 03:36:58');

-- --------------------------------------------------------

--
-- Table structure for table `combos`
--

CREATE TABLE `combos` (
  `combo_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `original_price` int NOT NULL DEFAULT '0',
  `price` int NOT NULL DEFAULT '0',
  `image_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `combos`
--

INSERT INTO `combos` (`combo_id`, `name`, `slug`, `description`, `original_price`, `price`, `image_url`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Combo Bữa Sáng', 'combo-bua-sang-chill-chill', 'Bữa sáng năng lượng với bánh mì phô mai nướng ấm nóng, nhân chảy ngậy mướt kết hợp cùng cà phê đen đá đậm đà. Sự khởi đầu hoàn hảo giúp bạn bừng tỉnh và tràn đầy nhiệt huyết.', 37000, 35000, '/uploads/combos/1786692526_combo-bua-sang.png', 1, '2026-08-10 14:49:13', '2026-08-14 07:28:46'),
(2, 'Combo Bữa Sáng Ngọt gào', 'combo-bua-sang-ngot-gao', 'Sự kết hợp tinh tế giữa một phần Cheesecake việt quất chua ngọt, béo mịn và ly Chocolate đá xay đậm đà mát lạnh. Combo tuyệt vời cho những khoảnh khắc \"chill\" ngập tràn vị ngọt.', 60000, 45000, '/uploads/combos/1786692655_combo-bua-sang-ngot-gao.png', 1, '2026-08-14 07:30:55', '2026-08-14 07:30:55'),
(3, 'Combo Giải Nhiệt', 'combo-giai-nhiet', 'Sự kết hợp giải khát hoàn hảo giữa ly trà trái cây vàng ươm, mát lạnh sảng khoái và phần bánh Flan caramel mượt mà, béo ngậy. Lựa chọn lý tưởng cho một buổi chiều nhẹ nhàng, thư thái.', 55000, 50000, '/uploads/combos/1786693364_combo-giai-nhiet.png', 1, '2026-08-14 07:42:44', '2026-08-14 07:42:44'),
(4, 'Combo Buổi Hẹn', 'combo-buoi-hen', 'Trọn vẹn khoảnh khắc ngọt ngào dành cho hai người với 2 ly nước tùy chọn (trà trái cây mọng nước & cà phê latte béo mịn) đi kèm 2 phần bánh ngọt cao cấp Tiramisu cùng Cheesecake chanh dây chua ngọt thanh mát.', 120000, 100000, '/uploads/combos/1786693515_combo-buoi-hen.png', 1, '2026-08-14 07:45:15', '2026-08-14 07:45:15'),
(5, 'Combo Cặp Đôi', 'combo-cap-doi', 'Sự dung hòa tinh tế giữa vị chua ngọt ngào, rực rỡ từ trà dâu tươi mọng và nét béo ngậy, đắng nhẹ của ly Latte tầng thơm phức. Lựa chọn \"nhìn là mê\" cho các cặp đôi khi hẹn hò.', 45000, 40000, '/uploads/combos/1786693573_combo-cap-doi.png', 1, '2026-08-14 07:46:13', '2026-08-14 07:46:13'),
(6, 'Combo Của Tháng', 'combo-cua', 'Khởi đầu ngày mới tràn đầy năng lượng cùng chiếc bánh Croissant ngàn lớp giòn xốp thơm lừng bơ Pháp, thưởng thức kèm ly cà phê đá xay mát lạnh, ngọt ngào đậm vị.', 65000, 60000, '/uploads/combos/1786693650_combo-cua.png', 1, '2026-08-14 07:47:30', '2026-08-14 07:47:41'),
(7, 'Combo Hắt Bạch', 'combo-hat-bach', 'Món quà trọn vẹn cho tín đồ cà phê với sự tương phản đầy thú vị: một ly Cà phê đen đậm đà, tỉnh táo kết hợp cùng Cà phê Latte êm dịu, béo ngậy. Bộ đôi hoàn hảo để sẻ chia cùng cạ cứng.', 37000, 33000, '/uploads/combos/1786693730_combo-hat-bach.png', 1, '2026-08-14 07:48:50', '2026-08-14 07:48:50'),
(8, 'Combo Mùa Hè', 'combo-mua-he', 'Bộ sưu tập 4 ly trà trái cây tươi mát rực rỡ sắc màu, ngập tràn topping hoa quả tươi từ dưa lưới, dâu tây đến đào cam sả. Lựa chọn tuyệt đỉnh cho nhóm bạn cùng giải nhiệt và tận hưởng không khí mùa hè.', 110000, 99000, '/uploads/combos/1786693827_combo-mua-he.png', 1, '2026-08-14 07:50:27', '2026-08-14 07:50:27');

-- --------------------------------------------------------

--
-- Table structure for table `combo_items`
--

CREATE TABLE `combo_items` (
  `id` bigint UNSIGNED NOT NULL,
  `combo_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `combo_items`
--

INSERT INTO `combo_items` (`id`, `combo_id`, `product_id`, `quantity`, `created_at`, `updated_at`) VALUES
(5, 1, 24, 1, '2026-08-14 07:28:46', '2026-08-14 07:28:46'),
(6, 1, 63, 1, '2026-08-14 07:28:46', '2026-08-14 07:28:46'),
(7, 2, 69, 1, '2026-08-14 07:30:55', '2026-08-14 07:30:55'),
(8, 2, 68, 1, '2026-08-14 07:30:55', '2026-08-14 07:30:55'),
(9, 3, 16, 1, '2026-08-14 07:42:44', '2026-08-14 07:42:44'),
(10, 3, 74, 1, '2026-08-14 07:42:44', '2026-08-14 07:42:44'),
(11, 4, 31, 1, '2026-08-14 07:45:15', '2026-08-14 07:45:15'),
(12, 4, 54, 1, '2026-08-14 07:45:15', '2026-08-14 07:45:15'),
(13, 4, 75, 1, '2026-08-14 07:45:15', '2026-08-14 07:45:15'),
(14, 4, 67, 1, '2026-08-14 07:45:15', '2026-08-14 07:45:15'),
(15, 5, 5, 1, '2026-08-14 07:46:13', '2026-08-14 07:46:13'),
(16, 5, 54, 1, '2026-08-14 07:46:13', '2026-08-14 07:46:13'),
(19, 6, 65, 1, '2026-08-14 07:47:41', '2026-08-14 07:47:41'),
(20, 6, 70, 1, '2026-08-14 07:47:41', '2026-08-14 07:47:41'),
(21, 7, 24, 1, '2026-08-14 07:48:50', '2026-08-14 07:48:50'),
(22, 7, 54, 1, '2026-08-14 07:48:50', '2026-08-14 07:48:50'),
(23, 8, 27, 1, '2026-08-14 07:50:27', '2026-08-14 07:50:27'),
(24, 8, 12, 1, '2026-08-14 07:50:27', '2026-08-14 07:50:27'),
(25, 8, 31, 1, '2026-08-14 07:50:27', '2026-08-14 07:50:27'),
(26, 8, 71, 1, '2026-08-14 07:50:27', '2026-08-14 07:50:27');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedbacks`
--

CREATE TABLE `feedbacks` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reply_content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('unread','read','replied') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unread',
  `replied_by` bigint UNSIGNED DEFAULT NULL,
  `replied_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
(24, '2026_04_08_060239_create_orders_table', 6),
(25, '2026_05_30_080954_add_price_to_products_table', 7),
(26, '2026_05_30_095951_add_remember_token_to_users_table', 8),
(27, '2026_05_31_093640_add_voucher_to_orders_table', 9),
(28, '2026_06_06_121546_create_shift_management_tables', 10),
(29, '2026_06_06_210310_create_attendance_tables', 11),
(30, '2026_06_07_103909_create_feedbacks_table', 12),
(31, '2026_06_07_104128_create_chat_sessions_table', 12),
(32, '2026_06_07_104130_create_chat_messages_table', 12),
(33, '2026_06_07_135929_add_is_bot_enabled_to_chat_sessions_table', 12),
(34, '2026_06_15_180131_create_salary_payments_table', 12),
(35, '2026_07_21_065344_create_reviews_table', 13),
(36, '2026_07_21_074757_create_product_images_table', 14),
(37, '2026_07_29_120000_add_points_required_to_vouchers_table', 15),
(38, '2026_07_29_130000_add_type_and_user_to_vouchers_table', 15),
(39, '2026_07_03_143152_add_is_locked_to_users_table', 16),
(40, '2026_07_29_140000_create_cart_items_table', 17),
(41, '2026_08_10_000001_create_combos_table', 17),
(42, '2026_08_10_000002_create_banners_table', 18),
(43, '2026_08_10_000003_add_secondary_button_to_banners_table', 19),
(44, '2026_08_10_000004_add_product_id_to_banners_table', 20),
(45, '2026_08_10_000005_add_usage_per_user_to_vouchers_table', 21);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `shift_id` bigint UNSIGNED DEFAULT NULL,
  `voucher_id` bigint UNSIGNED DEFAULT NULL,
  `customer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `order_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'delivery',
  `table_number` int DEFAULT NULL,
  `payment_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `total_amount` decimal(15,2) NOT NULL,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `items` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `shift_id`, `voucher_id`, `customer_name`, `customer_phone`, `shipping_address`, `order_type`, `table_number`, `payment_method`, `total_amount`, `discount_amount`, `status`, `items`, `created_at`, `updated_at`) VALUES
(1, 3, NULL, NULL, 'hihi', '0385792442', 'Phường Tân Định, Quận 1, TP. Hồ Chí Minh', 'delivery', NULL, 'cash', 36000.00, 0.00, 'completed', '{\"fbea9d05c20b72fe4f85d401d0623a6f\": {\"name\": \"Món Ngon Chill Chill 1\", \"image\": \"https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_6.png\", \"price\": \"36000.00\", \"quantity\": 1, \"toppings\": [], \"size_name\": \"Size S\", \"product_id\": 1, \"variant_id\": 1, \"topping_total\": 0}}', '2026-04-07 23:14:51', '2026-05-30 02:33:59'),
(2, 3, NULL, NULL, 'hihi', '', 'Phường Tân Định, Quận 1, TP. Hồ Chí Minh', 'dine_in', 30, 'cash', 36000.00, 0.00, 'completed', '{\"fbea9d05c20b72fe4f85d401d0623a6f\": {\"name\": \"Món Ngon Chill Chill 1\", \"image\": \"https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_6.png\", \"price\": \"36000.00\", \"quantity\": 1, \"toppings\": [], \"size_name\": \"Size S\", \"product_id\": 1, \"variant_id\": 1, \"topping_total\": 0}}', '2026-04-08 00:13:14', '2026-05-30 02:34:02'),
(3, 3, NULL, NULL, 'hihi', '', '1222, Phường 5, Quận 11, TP. Hồ Chí Minh', 'dine_in', 20, 'cash', 36000.00, 0.00, 'completed', '{\"fbea9d05c20b72fe4f85d401d0623a6f\": {\"name\": \"Món Ngon Chill Chill 1\", \"image\": \"https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_6.png\", \"price\": \"36000.00\", \"quantity\": 1, \"toppings\": [], \"size_name\": \"Size S\", \"product_id\": 1, \"variant_id\": 1, \"topping_total\": 0}}', '2026-04-08 00:17:33', '2026-05-30 02:35:43'),
(4, NULL, NULL, NULL, 'Khách Vãng Lai', NULL, NULL, 'pos', NULL, 'cash', 251000.00, 0.00, 'completed', '[{\"name\": \"Món Ngon Chill Chill 1\", \"price\": 36000, \"quantity\": 1, \"toppings\": {\"48\": 5}, \"productId\": 1, \"cartItemId\": 1780133048478}]', '2026-05-30 02:24:13', '2026-05-30 02:35:47'),
(5, NULL, NULL, NULL, 'Khách Vãng Lai', NULL, NULL, 'pos', NULL, 'cash', 164000.00, 0.00, 'completed', '[{\"name\": \"Món Ngon Chill Chill 2\", \"price\": 35000, \"quantity\": 1, \"toppings\": {\"48\": 3}, \"productId\": 2, \"cartItemId\": 1780133089179}]', '2026-05-30 02:24:55', '2026-05-30 02:35:50'),
(6, NULL, NULL, NULL, 'Khách Vãng Lai', NULL, NULL, 'pos', NULL, 'cash', 207000.00, 0.00, 'completed', '[{\"name\": \"Món Ngon Chill Chill 2\", \"price\": 35000, \"quantity\": 1, \"toppings\": {\"48\": 4}, \"productId\": 2, \"cartItemId\": 1780133272806}]', '2026-05-30 02:27:58', '2026-05-30 02:35:53'),
(7, NULL, NULL, NULL, 'Khách Vãng Lai', NULL, NULL, 'pos', NULL, 'cash', 165000.00, 0.00, 'completed', '[{\"name\": \"Món Ngon Chill Chill 1\", \"price\": 36000, \"quantity\": 1, \"toppings\": {\"48\": 3}, \"productId\": 1, \"cartItemId\": 1780133685824}]', '2026-05-30 02:35:00', '2026-05-30 02:35:56'),
(8, NULL, NULL, NULL, 'ho', NULL, '12', 'pos', NULL, 'cash', 322000.00, 0.00, 'completed', '[{\"name\": \"Món Ngon Chill Chill 1\", \"price\": 36000, \"quantity\": 1, \"toppings\": {\"48\": 5}, \"productId\": 1, \"cartItemId\": 1780370080689}, {\"name\": \"Món Ngon Chill Chill 3\", \"price\": 35000, \"quantity\": 1, \"toppings\": [], \"productId\": 3, \"cartItemId\": 1780370083119}, {\"name\": \"Món Ngon Chill Chill 1\", \"price\": 36000, \"quantity\": 1, \"toppings\": [], \"productId\": 1, \"cartItemId\": 1780370087044}]', '2026-06-01 20:15:10', '2026-06-01 20:15:27'),
(9, NULL, NULL, NULL, 'Khách Vãng Lai', NULL, NULL, 'pos', NULL, 'cash', 35000.00, 0.00, 'completed', '[{\"name\": \"Món Ngon Chill Chill 3\", \"price\": 35000, \"quantity\": 1, \"toppings\": [], \"productId\": 3, \"cartItemId\": 1780370133114}]', '2026-06-01 20:15:36', '2026-06-01 20:27:03'),
(10, NULL, NULL, NULL, 'Khách Vãng Lai', NULL, NULL, 'pos', NULL, 'cash', 164000.00, 0.00, 'completed', '[{\"name\": \"Món Ngon Chill Chill 2\", \"price\": 35000, \"quantity\": 1, \"toppings\": {\"48\": 3}, \"productId\": 2, \"cartItemId\": 1780370235767}]', '2026-06-01 20:17:19', '2026-06-01 20:27:06'),
(11, NULL, NULL, NULL, 'hsah', NULL, 'dfa', 'pos', NULL, 'cash', 242000.00, 0.00, 'completed', '[{\"name\": \"Món Ngon Chill Chill 3\", \"price\": 35000, \"quantity\": 2, \"toppings\": {\"48\": 2}, \"productId\": 3, \"cartItemId\": 1780370785219}]', '2026-06-01 20:26:39', '2026-06-01 20:26:58'),
(12, NULL, NULL, NULL, 'Khách Vãng Lai', NULL, NULL, 'pos', NULL, 'cash', 156000.00, 0.00, 'completed', '[{\"name\": \"Món Ngon Chill Chill 2\", \"price\": 35000, \"quantity\": 2, \"toppings\": {\"48\": 1}, \"productId\": 2, \"cartItemId\": 1780370842006}]', '2026-06-01 20:27:29', '2026-06-01 20:42:58'),
(13, 1, NULL, NULL, 'User_0385792442', '0385792442', '1222, Phường 13, Quận 10, TP. Hồ Chí Minh', 'delivery', NULL, 'cash', 72560.00, 0.00, 'completed', '{\"6ec569eaf747ca132d9b8c2b53ec505e\": {\"name\": \"Món Ngon Chill Chill 2\", \"image\": \"https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_5.png\", \"price\": \"35000.00\", \"quantity\": 1, \"toppings\": {\"1\": {\"qty\": 2, \"name\": \"ducaty\", \"price\": \"1280.00\"}}, \"size_name\": \"Size S\", \"product_id\": 2, \"variant_id\": 3, \"topping_total\": 2560}, \"db8fdc886e726430092587c678be4bdf\": {\"name\": \"Món Ngon Chill Chill 2\", \"image\": \"https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_5.png\", \"price\": \"35000.00\", \"quantity\": 1, \"toppings\": [], \"size_name\": \"Size S\", \"product_id\": 2, \"variant_id\": 3, \"topping_total\": 0}}', '2026-06-01 20:39:43', '2026-06-01 20:56:59'),
(14, 5, NULL, NULL, 'huyho', '0385792442', '1222, Phường 15, Quận 10, TP. Hồ Chí Minh', 'delivery', NULL, 'cash', 38840.00, 0.00, 'completed', '{\"92b84e1de742f44abfe6acd78e5a7ba3\": {\"name\": \"Món Ngon Chill Chill 2\", \"image\": \"https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_5.png\", \"price\": \"35000.00\", \"quantity\": 1, \"toppings\": {\"1\": {\"qty\": 3, \"name\": \"ducaty\", \"price\": \"1280.00\"}}, \"size_name\": \"Size S\", \"product_id\": 2, \"variant_id\": 3, \"topping_total\": 3840}}', '2026-06-01 20:40:58', '2026-06-06 05:27:53'),
(15, NULL, NULL, NULL, 'Khách Vãng Lai', NULL, NULL, 'pos', NULL, 'cash', 78000.00, 0.00, 'completed', '[{\"name\": \"Món Ngon Chill Chill 2\", \"price\": 35000, \"quantity\": 1, \"toppings\": {\"48\": 1}, \"productId\": 2, \"cartItemId\": 1780372280422}]', '2026-06-01 20:51:34', '2026-06-06 14:30:22'),
(16, NULL, NULL, NULL, 'Khách Vãng Lai', NULL, NULL, 'pos', NULL, 'cash', 187000.00, 0.00, 'completed', '[{\"name\": \"Món Ngon Chill Chill 3\", \"price\": 35000, \"quantity\": 1, \"toppings\": {\"48\": 2, \"56\": 3}, \"productId\": 3, \"cartItemId\": 1780372339777}, {\"name\": \"Món Ngon Chill Chill 3\", \"price\": 35000, \"quantity\": 1, \"toppings\": [], \"productId\": 3, \"cartItemId\": 1780372347046}, {\"name\": \"Chân Châu\", \"price\": 31000, \"quantity\": 1, \"toppings\": [], \"productId\": 49, \"cartItemId\": 1780372368684}]', '2026-06-01 20:56:20', '2026-06-06 14:30:24'),
(17, NULL, 2, NULL, 'Khách Vãng Lai', NULL, NULL, 'pos', NULL, 'cash', 308000.00, 0.00, 'completed', '[{\"name\": \"Món Ngon Chill Chill 1\", \"price\": 36000, \"quantity\": 1, \"toppings\": [], \"productId\": 1, \"cartItemId\": 1780746369632}, {\"name\": \"Món Ngon Chill Chill 2\", \"price\": 35000, \"quantity\": 1, \"toppings\": [], \"productId\": 2, \"cartItemId\": 1780746371297}, {\"name\": \"Món Ngon Chill Chill 4\", \"price\": 35000, \"quantity\": 1, \"toppings\": [], \"productId\": 4, \"cartItemId\": 1780746372746}, {\"name\": \"Món Ngon Chill Chill 5\", \"price\": 43000, \"quantity\": 2, \"toppings\": [], \"productId\": 5, \"cartItemId\": 1780746373952}, {\"name\": \"Món Ngon Chill Chill 6\", \"price\": 39000, \"quantity\": 1, \"toppings\": [], \"productId\": 6, \"cartItemId\": 1780746377066}, {\"name\": \"Món Ngon Chill Chill 7\", \"price\": 35000, \"quantity\": 1, \"toppings\": [], \"productId\": 7, \"cartItemId\": 1780746379480}, {\"name\": \"Món Ngon Chill Chill 9\", \"price\": 42000, \"quantity\": 1, \"toppings\": [], \"productId\": 9, \"cartItemId\": 1780746381162}]', '2026-06-06 04:46:33', '2026-06-06 14:35:57'),
(18, NULL, 2, NULL, 'Khách Vãng Lai', NULL, NULL, 'pos', NULL, 'cash', 514000.00, 0.00, 'completed', '[{\"name\": \"Món Ngon Chill Chill 2\", \"price\": 35000, \"quantity\": 3, \"toppings\": [], \"productId\": 2, \"cartItemId\": 1780747217272}, {\"name\": \"Món Ngon Chill Chill 3\", \"price\": 35000, \"quantity\": 4, \"toppings\": [], \"productId\": 3, \"cartItemId\": 1780747221180}, {\"name\": \"Món Ngon Chill Chill 4\", \"price\": 35000, \"quantity\": 1, \"toppings\": [], \"productId\": 4, \"cartItemId\": 1780747225674}, {\"name\": \"Món Ngon Chill Chill 1\", \"price\": 36000, \"quantity\": 1, \"toppings\": {\"55\": 1}, \"productId\": 1, \"cartItemId\": 1780747230352}, {\"name\": \"Món Ngon Chill Chill 4\", \"price\": 35000, \"quantity\": 1, \"toppings\": {\"56\": 1}, \"productId\": 4, \"cartItemId\": 1780747232966}, {\"name\": \"Món Ngon Chill Chill 5\", \"price\": 43000, \"quantity\": 1, \"toppings\": [], \"productId\": 5, \"cartItemId\": 1780747235991}, {\"name\": \"Món Ngon Chill Chill 5\", \"price\": 43000, \"quantity\": 1, \"toppings\": {\"56\": 1}, \"productId\": 5, \"cartItemId\": 1780747238530}, {\"name\": \"Món Ngon Chill Chill 7\", \"price\": 35000, \"quantity\": 1, \"toppings\": {\"56\": 1}, \"productId\": 7, \"cartItemId\": 1780747241399}, {\"name\": \"Món Ngon Chill Chill 9\", \"price\": 42000, \"quantity\": 1, \"toppings\": {\"56\": 1}, \"productId\": 9, \"cartItemId\": 1780747244285}]', '2026-06-06 05:00:51', '2026-06-06 14:36:00'),
(19, NULL, 2, NULL, 'Khách Vãng Lai', NULL, NULL, 'pos', NULL, 'cash', 1025000.00, 0.00, 'completed', '[{\"name\": \"Món Ngon Chill Chill 1\", \"price\": 36000, \"quantity\": 1, \"toppings\": {\"48\": 23}, \"productId\": 1, \"cartItemId\": 1780756619592}]', '2026-06-06 14:37:02', '2026-06-06 14:37:16'),
(20, NULL, 2, NULL, 'Khách Vãng Lai', NULL, NULL, 'pos', NULL, 'cash', 809000.00, 0.00, 'completed', '[{\"name\": \"Món Ngon Chill Chill 2\", \"price\": 35000, \"quantity\": 1, \"toppings\": {\"48\": 18}, \"productId\": 2, \"cartItemId\": 1780757276825}]', '2026-06-06 14:48:00', '2026-06-06 14:48:13'),
(21, NULL, 2, NULL, 'Khách Vãng Lai', NULL, NULL, 'pos', NULL, 'cash', 345000.00, 0.00, 'completed', '[{\"name\": \"Món Ngon Chill Chill 13\", \"price\": 44000, \"quantity\": 1, \"toppings\": {\"48\": 7}, \"productId\": 13, \"cartItemId\": 1780757356892}]', '2026-06-06 14:49:20', '2026-06-06 14:49:31'),
(22, NULL, 3, NULL, 'Khách Vãng Lai', NULL, NULL, 'pos', NULL, 'cash', 165000.00, 0.00, 'completed', '[{\"name\": \"Món Ngon Chill Chill 1\", \"price\": 36000, \"quantity\": 1, \"toppings\": {\"48\": 3}, \"productId\": 1, \"cartItemId\": 1780975201414}]', '2026-06-09 03:20:16', '2026-06-09 03:20:29'),
(23, NULL, 3, NULL, 'Khách Vãng Lai', NULL, NULL, 'pos', NULL, 'cash', 441000.00, 0.00, 'completed', '[{\"name\": \"Món Ngon Chill Chill 2\", \"price\": 35000, \"quantity\": 1, \"toppings\": [], \"productId\": 2, \"cartItemId\": 1780975507963}, {\"name\": \"Món Ngon Chill Chill 3\", \"price\": 35000, \"quantity\": 1, \"toppings\": [], \"productId\": 3, \"cartItemId\": 1780975510303}, {\"name\": \"Món Ngon Chill Chill 4\", \"price\": 35000, \"quantity\": 1, \"toppings\": [], \"productId\": 4, \"cartItemId\": 1780975511461}, {\"name\": \"Món Ngon Chill Chill 3\", \"price\": 35000, \"quantity\": 1, \"toppings\": {\"48\": 5}, \"productId\": 3, \"cartItemId\": 1780975515518}, {\"name\": \"thạch\", \"price\": 43000, \"quantity\": 2, \"toppings\": [], \"productId\": 48, \"cartItemId\": 1780975517597}]', '2026-06-09 03:25:23', '2026-06-09 03:25:40'),
(24, NULL, 3, NULL, 'Khách Vãng Lai', NULL, NULL, 'pos', NULL, 'cash', 36000.00, 0.00, 'completed', '[{\"name\": \"Món Ngon Chill Chill 1\", \"price\": 36000, \"quantity\": 1, \"toppings\": [], \"productId\": 1, \"cartItemId\": 1780975725010}]', '2026-06-09 03:28:48', '2026-07-21 04:54:28'),
(25, 3, NULL, NULL, 'hihi', '0385792442', 'Phường Tân Định, Quận 1, TP. Hồ Chí Minh', 'delivery', NULL, 'cash', 36000.00, 0.00, 'canceled', '{\"fbea9d05c20b72fe4f85d401d0623a6f\": {\"name\": \"Món Ngon Chill Chill 1\", \"image\": \"https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_6.png\", \"price\": \"36000.00\", \"quantity\": 1, \"toppings\": [], \"size_name\": \"Size S\", \"product_id\": 1, \"variant_id\": 1, \"topping_total\": 0}}', '2026-07-20 23:27:48', '2026-07-20 23:28:53'),
(26, NULL, NULL, NULL, 'Khách Vãng Lai', NULL, NULL, 'pos', NULL, 'cash', 82000.00, 0.00, 'completed', '[{\"name\": \"Món Ngon Chill Chill 1\", \"price\": 36000, \"quantity\": 1, \"toppings\": {\"48\": 1}, \"ice_level\": \"0_full\", \"productId\": 1, \"cartItemId\": 1784606773065, \"sugar_level\": \"70\"}]', '2026-07-21 04:06:15', '2026-07-21 04:54:30'),
(27, 1, NULL, NULL, 'User_0385792442', '0385792442', '1222, Phường 13, Quận 10, TP. Hồ Chí Minh', 'delivery', NULL, 'cash', 137000.00, 0.00, 'completed', '{\"b68257ed6d4c8dc092c0ba9577699a6a\": {\"name\": \"Món Ngon Chill Chill 1\", \"image\": \"https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_6.png\", \"price\": \"50000.00\", \"quantity\": 1, \"toppings\": [], \"size_name\": \"Size M\", \"product_id\": 1, \"variant_id\": 2, \"topping_total\": 0}, \"bd5da04a142aa091d3ebc8683ffdfd1a\": {\"name\": \"Món Ngon Chill Chill 1\", \"image\": \"https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_6.png\", \"price\": \"36000.00\", \"quantity\": 1, \"toppings\": {\"48\": {\"qty\": 1, \"name\": \"thạch\", \"price\": 43000}, \"54\": {\"qty\": 1, \"name\": \"Chân châu\", \"price\": 5000}}, \"ice_level\": \"0_full\", \"size_name\": \"Size S\", \"product_id\": 1, \"variant_id\": 1, \"sugar_level\": \"50\", \"topping_total\": 51000}}', '2026-07-21 04:28:33', '2026-07-21 04:30:57'),
(28, 1, NULL, NULL, 'User_0385792442', '0385792442', '1222, Phường 13, Quận 10, TP. Hồ Chí Minh', 'delivery', NULL, 'cash', 113000.00, 0.00, 'completed', '{\"4e5af1cfe20efb39ef236f2adffb0908\": {\"name\": \"Món Ngon Chill Chill 1\", \"image\": \"https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_6.png\", \"price\": 36000, \"quantity\": 1, \"toppings\": [], \"ice_level\": \"100\", \"size_name\": \"Size S\", \"product_id\": 1, \"variant_id\": 1, \"sugar_level\": \"100\", \"topping_total\": 0}, \"76a3a0717261ac655f142dc527cfc567\": {\"name\": \"Món Ngon Chill Chill 7\", \"image\": \"https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_4.png\", \"price\": 35000, \"quantity\": 1, \"toppings\": [], \"ice_level\": \"100\", \"size_name\": \"Size S\", \"product_id\": 7, \"variant_id\": 13, \"sugar_level\": \"100\", \"topping_total\": 0}, \"dd5374a0f19dab329e7706689d71e6d4\": {\"name\": \"Món Ngon Chill Chill 8\", \"image\": \"https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_7.png\", \"price\": 42000, \"quantity\": 1, \"toppings\": [], \"ice_level\": \"100\", \"size_name\": \"Size S\", \"product_id\": 8, \"variant_id\": 15, \"sugar_level\": \"100\", \"topping_total\": 0}}', '2026-07-21 04:43:33', '2026-07-21 04:54:32'),
(29, 1, NULL, NULL, 'User_0385792442', '0385792442', '1222, Phường 13, Quận 10, TP. Hồ Chí Minh', 'delivery', NULL, 'cash', 308000.00, 0.00, 'completed', '{\"461f3f7aaacf8164c83b1ca2369e530d\": {\"name\": \"Món Ngon Chill Chill 12\", \"image\": \"https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_5.png\", \"price\": 35000, \"quantity\": 1, \"toppings\": [], \"ice_level\": \"100\", \"size_name\": \"Size S\", \"product_id\": 12, \"variant_id\": 23, \"sugar_level\": \"100\", \"topping_total\": 0}, \"4e5af1cfe20efb39ef236f2adffb0908\": {\"name\": \"Món Ngon Chill Chill 1\", \"image\": \"https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_6.png\", \"price\": 36000, \"quantity\": 1, \"toppings\": [], \"ice_level\": \"100\", \"size_name\": \"Size S\", \"product_id\": 1, \"variant_id\": 1, \"sugar_level\": \"100\", \"topping_total\": 0}, \"63db9321b5eb2981777723ac4aa9eba9\": {\"name\": \"Món Ngon Chill Chill 2\", \"image\": \"https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_5.png\", \"price\": 35000, \"quantity\": 1, \"toppings\": [], \"ice_level\": \"100\", \"size_name\": \"Size S\", \"product_id\": 2, \"variant_id\": 3, \"sugar_level\": \"100\", \"topping_total\": 0}, \"76a3a0717261ac655f142dc527cfc567\": {\"name\": \"Món Ngon Chill Chill 7\", \"image\": \"https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_4.png\", \"price\": 35000, \"quantity\": 1, \"toppings\": [], \"ice_level\": \"100\", \"size_name\": \"Size S\", \"product_id\": 7, \"variant_id\": 13, \"sugar_level\": \"100\", \"topping_total\": 0}, \"7e16010225a9f151bb093124cb9c41ea\": {\"name\": \"Món Ngon Chill Chill 33\", \"image\": \"https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_7.png\", \"price\": 37000, \"quantity\": 1, \"toppings\": [], \"ice_level\": \"100\", \"size_name\": \"Size S\", \"product_id\": 33, \"variant_id\": 65, \"sugar_level\": \"100\", \"topping_total\": 0}, \"a63a7fb1b0bedbd69c765d69aa757139\": {\"name\": \"Món Ngon Chill Chill 5\", \"image\": \"https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_7.png\", \"price\": 43000, \"quantity\": 1, \"toppings\": [], \"ice_level\": \"100\", \"size_name\": \"Size S\", \"product_id\": 5, \"variant_id\": 9, \"sugar_level\": \"100\", \"topping_total\": 0}, \"dd5374a0f19dab329e7706689d71e6d4\": {\"name\": \"Món Ngon Chill Chill 8\", \"image\": \"https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_7.png\", \"price\": 42000, \"quantity\": 1, \"toppings\": [], \"ice_level\": \"100\", \"size_name\": \"Size S\", \"product_id\": 8, \"variant_id\": 15, \"sugar_level\": \"100\", \"topping_total\": 0}, \"ef69e038f21a6f324a594044b095867c\": {\"name\": \"Món Ngon Chill Chill 19\", \"image\": \"https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_6.png\", \"price\": 45000, \"quantity\": 1, \"toppings\": [], \"ice_level\": \"100\", \"size_name\": \"Size S\", \"product_id\": 19, \"variant_id\": 37, \"sugar_level\": \"100\", \"topping_total\": 0}}', '2026-07-21 04:44:28', '2026-07-21 04:54:34'),
(30, NULL, NULL, NULL, 'Khách Vãng Lai', NULL, NULL, 'pos', NULL, 'cash', 208000.00, 0.00, 'completed', '[{\"name\": \"Món Ngon Chill Chill 1\", \"price\": 36000, \"quantity\": 1, \"toppings\": {\"48\": 4}, \"ice_level\": \"100\", \"productId\": 1, \"cartItemId\": 1784610654173, \"sugar_level\": \"100\"}]', '2026-07-21 05:10:57', '2026-07-21 05:20:46'),
(31, NULL, NULL, NULL, 'Khách Vãng Lai', NULL, NULL, 'pos', NULL, 'cash', 165000.00, 0.00, 'completed', '[{\"name\": \"Món Ngon Chill Chill 1\", \"price\": 36000, \"quantity\": 1, \"toppings\": {\"48\": 3}, \"ice_level\": \"100\", \"productId\": 1, \"cartItemId\": 1784610839968, \"sugar_level\": \"100\"}]', '2026-07-21 05:14:02', '2026-07-21 05:20:49'),
(32, NULL, 10, NULL, 'Khách Vãng Lai', NULL, NULL, 'pos', NULL, 'cash', 208000.00, 0.00, 'completed', '[{\"name\": \"Món Ngon Chill Chill 1\", \"price\": 36000, \"quantity\": 1, \"toppings\": {\"48\": 4}, \"ice_level\": \"100\", \"productId\": 1, \"cartItemId\": 1784611521861, \"sugar_level\": \"100\"}]', '2026-07-21 05:25:24', '2026-07-21 05:27:35'),
(33, NULL, 10, NULL, 'Khách Vãng Lai', NULL, NULL, 'pos', NULL, 'cash', 251000.00, 0.00, 'completed', '[{\"name\": \"Món Ngon Chill Chill 1\", \"price\": 36000, \"quantity\": 1, \"toppings\": {\"48\": 5}, \"ice_level\": \"100\", \"productId\": 1, \"cartItemId\": 1784611679517, \"sugar_level\": \"100\"}]', '2026-07-21 05:28:02', '2026-07-21 05:28:24'),
(34, NULL, 10, NULL, 'Khách Vãng Lai', NULL, NULL, 'pos', NULL, 'cash', 234000.00, 0.00, 'completed', '[{\"name\": \"Món Ngon Chill Chill 2\", \"price\": 35000, \"quantity\": 3, \"toppings\": {\"48\": 1}, \"ice_level\": \"100\", \"productId\": 2, \"cartItemId\": 1784611902314, \"sugar_level\": \"100\"}]', '2026-07-21 05:31:48', '2026-07-21 05:31:59'),
(35, NULL, 10, NULL, 'Khách Vãng Lai', NULL, NULL, 'pos', NULL, 'cash', 366000.00, 0.00, 'completed', '[{\"name\": \"Món Ngon Chill Chill 1\", \"price\": 36000, \"quantity\": 3, \"toppings\": {\"48\": 2}, \"ice_level\": \"100\", \"productId\": 1, \"cartItemId\": 1784612239375, \"sugar_level\": \"100\"}]', '2026-07-21 05:37:24', '2026-07-21 05:42:27'),
(36, NULL, NULL, NULL, 'Khách Vãng Lai', NULL, NULL, 'pos', NULL, 'cash', 210000.00, 0.00, 'cancelled', '[{\"name\": \"Món Ngon Chill Chill 3\", \"price\": 35000, \"quantity\": 6, \"toppings\": [], \"ice_level\": \"100\", \"productId\": 3, \"cartItemId\": 1784612574368, \"sugar_level\": \"100\"}]', '2026-07-21 05:43:09', '2026-07-22 05:57:33'),
(37, 1, NULL, NULL, 'User_0385792442', '0385792442', '1222, Phường 13, Quận 10, TP. Hồ Chí Minh', 'delivery', NULL, 'cash', 108000.00, 0.00, 'completed', '{\"87414d627eacb989da8ca038d9603c58\": {\"name\": \"Món Ngon Chill Chill 5\", \"image\": \"https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_7.png\", \"price\": 62000, \"quantity\": 1, \"toppings\": {\"48\": {\"qty\": 1, \"name\": \"thạch\", \"price\": 43000}}, \"ice_level\": \"0_full\", \"size_name\": \"Size M\", \"product_id\": 5, \"variant_id\": 10, \"sugar_level\": \"100\", \"topping_total\": 46000}}', '2026-07-22 05:59:10', '2026-07-22 05:59:53'),
(38, 1, NULL, NULL, 'User_0385792442', '0385792442', '1222, Phường 13, Quận 10, TP. Hồ Chí Minh', 'delivery', NULL, 'cash', 212000.00, 53000.00, 'completed', '{\"e8355d7c5cf4ee25428697b80dc6cf76\": {\"name\": \"Món Ngon Chill Chill 1\", \"image\": \"https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_6.png\", \"price\": 50000, \"quantity\": 1, \"toppings\": {\"48\": {\"qty\": 5, \"name\": \"thạch\", \"price\": 43000}}, \"ice_level\": \"100\", \"size_name\": \"Size M\", \"product_id\": 1, \"variant_id\": 2, \"sugar_level\": \"100\", \"topping_total\": 215000}}', '2026-07-22 06:03:19', '2026-08-01 03:50:45'),
(39, 1, NULL, NULL, 'User_0385792442', '0385792442', '1222, Phường 13, Quận 10, TP. Hồ Chí Minh', 'delivery', NULL, 'cash', 35000.00, 0.00, 'completed', '{\"63db9321b5eb2981777723ac4aa9eba9\": {\"name\": \"Món Ngon Chill Chill 2\", \"image\": \"https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_5.png\", \"price\": 35000, \"quantity\": 1, \"toppings\": [], \"ice_level\": \"100\", \"size_name\": \"Size S\", \"product_id\": 2, \"variant_id\": 3, \"sugar_level\": \"100\", \"topping_total\": 0}}', '2026-07-28 04:00:47', '2026-08-01 03:50:48'),
(40, 1, NULL, NULL, 'User_0385792442', '0385792442', '1222, Phường 13, Quận 10, TP. Hồ Chí Minh', 'delivery', NULL, 'cash', 94500.00, 10500.00, 'canceled', '{\"5c83604da39a79622ac29cc5dc207364\": {\"name\": \"Món Ngon Chill Chill 3\", \"image\": \"https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_4.png\", \"price\": 35000, \"quantity\": 3, \"toppings\": [], \"ice_level\": \"100\", \"size_name\": \"Size S\", \"product_id\": 3, \"variant_id\": 5, \"sugar_level\": \"100\", \"topping_total\": 0}}', '2026-08-01 03:34:30', '2026-08-01 03:35:46'),
(41, 1, NULL, NULL, 'User_0385792442', '0385792442', NULL, 'pos', NULL, 'cash', 78000.00, 0.00, 'completed', '[{\"name\": \"Món Ngon Chill Chill 3\", \"price\": 35000, \"quantity\": 1, \"toppings\": {\"48\": 1}, \"ice_level\": \"100\", \"productId\": 3, \"cartItemId\": 1785556190725, \"sugar_level\": \"100\"}]', '2026-08-01 03:50:27', '2026-08-01 03:50:50'),
(42, 1, NULL, 7, 'User_0385792442', '0385792442', '1222, Phường 13, Quận 10, TP. Hồ Chí Minh', 'delivery', NULL, 'cash', 55200.00, 13800.00, 'pending', '{\"464654966006336e1137d32337b64852\": {\"name\": \"Chân châu\", \"image\": null, \"price\": 5000, \"quantity\": 2, \"toppings\": [], \"ice_level\": \"100\", \"size_name\": \"Size S\", \"product_id\": 54, \"variant_id\": 101, \"sugar_level\": \"100\", \"topping_total\": 0}, \"4f58848a84c683df63445dd15052d595\": {\"name\": \"[COMBO] Combo Bữa Sáng Chill Chill\", \"image\": \"https://images.unsplash.com/photo-1541167760496-1628856ab772?q=80&w=600&auto=format&fit=crop\", \"price\": 59000, \"combo_id\": 1, \"is_combo\": true, \"quantity\": 1, \"toppings\": [], \"ice_level\": null, \"size_name\": \"Món Ngon Chill Chill 12 x1 + Món Ngon Chill Chill 19 x1\", \"product_id\": 0, \"variant_id\": 0, \"sugar_level\": null, \"topping_total\": 0}}', '2026-08-10 16:34:21', '2026-08-10 16:34:21'),
(43, 1, NULL, 7, 'User_0385792442', '0385792442', '1222, Phường 13, Quận 10, TP. Hồ Chí Minh', 'delivery', NULL, 'cash', 56000.00, 14000.00, 'pending', '{\"15a69edbcc680bf00509ca6aebda0f96\": {\"name\": \"Món Ngon Chill Chill 3\", \"image\": \"https://c.animaapp.com/mmlh5SvJe3Mo7/img/ai_4.png\", \"price\": 35000, \"quantity\": 2, \"toppings\": [], \"ice_level\": \"100\", \"size_name\": \"Size S\", \"product_id\": 3, \"variant_id\": 5, \"sugar_level\": \"100\", \"topping_total\": 0}}', '2026-08-10 16:37:20', '2026-08-10 16:37:20');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `payment_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
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
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
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
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `thumbnail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `categories_post_id` bigint UNSIGNED NOT NULL,
  `auth_id` bigint UNSIGNED NOT NULL,
  `images` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`post_id`, `title`, `slug`, `content`, `thumbnail`, `status`, `categories_post_id`, `auth_id`, `images`, `created_at`, `updated_at`) VALUES
(1, 'BÍ QUYẾT PHƯƠNG PHÁP RANG XAY CÀ PHÊ NGUYÊN BẢN TẠI CHILL CHILL', 'bi-quyet-phuong-phap-rang-xay-ca-phe-nguyen-ban-tai-chill-chill', 'Cà phê không chỉ là một thức uống quen thuộc mỗi buổi sáng, mà đối với Chill Chill, đó là cả một hành trình nghệ thuật gói trọn tâm huyết từ những đồi cà phê bạt ngàn vùng đất Tây Nguyên.\n\n1. Nguồn nguyên liệu được tuyển chọn khắt khe\nĐể tạo nên hương vị mộc mạc nhưng đậm đà khó quên, Chill Chill tuyển chọn từng hạt cà phê Robusta và Arabica đạt độ chín hoàn hảo. Những hạt cà phê chín mọng được hái thủ công, trải qua quá trình sơ chế nghiêm ngặt nhằm giữ nguyên hàm lượng hương vị tự nhiên nhất.\n\n2. Nghệ thuật rang xay chuẩn gu Việt\nBí quyết tạo nên điểm đặc trưng của cà phê Chill Chill nằm ở công nghệ rang xay hiện đại kết hợp với công thức gia truyền. Hạt cà phê được rang ở nhiệt độ thích hợp để dậy lên mùi thơm nồng nàn, chút đắng thanh hòa quyện cùng vị hậu ngọt sâu lắng. Dù thưởng thức Phin truyền thống hay Espresso hiện đại, bạn đều sẽ cảm nhận được sự tỉ mỉ trong từng ngụm cà phê.\n\n3. Đánh thức năng lượng cho ngày mới\nMột tách cà phê đậm đà tại Chill Chill không chỉ giúp bạn tỉnh táo làm việc mà còn là nguồn cảm hứng sáng tạo tuyệt vời. Hãy ghé thăm chi nhánh gần nhất hoặc đặt hàng ngay để trải nghiệm hương vị cà phê nguyên bản nhé!', '/images/caphe.png', 1, 1, 1, NULL, '2026-08-13 09:09:43', '2026-08-13 09:09:43'),
(2, 'BẠC XỈU SÀI GÒN - HƯƠNG VỊ KÝ ỨC ĐỌC LẠI TRONG TỪNG NGỤM CHILL', 'bac-xiu-sai-gon-huong-vi-ky-uc-doc-lai-trong-tung-ngum-chill', 'Nếu cà phê đen đá mang vị đắng nguyên bản cá tính, thì Bạc Xỉu lại là sự vỗ về dịu ngọt cho những ai yêu thích sự nhẹ nhàng nhưng vẫn muốn vương vấn chút hương vị cà phê đậm đà.\n\n1. Nguồn gốc của ly Bạc Xỉu thân thuộc\nBạc Xỉu (viết tắt từ \'Bạc tẩy xỉu phạ\') vốn bắt nguồn từ nét văn hóa ẩm thực giao thoa độc đáo của Sài Gòn xưa. Đối với nhiều người, Bạc Xỉu là món uống nhập môn đưa họ bước vào thế giới cà phê đầy say mê.\n\n2. Điểm đặc trưng tại Chill Chill Coffee & Tea\nTại Chill Chill, ly Bạc Xỉu được chăm chút với tỉ lệ vàng giữa lớp sữa đặc ngọt béo, sữa tươi thơm ngậy và cốt cà phê Robusta sánh mịn. Khi khuấy đều, màu nâu sóng sánh hiện lên đẹp mắt, vị ngọt béo ngậy hòa cùng hương thơm đắng dịu lan tỏa ngay từ ngụm đầu tiên.\n\n3. Thưởng thức trọn vẹn từng khoảnh khắc\nMột ly Bạc Xỉu mát lạnh cùng chiếc bánh ngọt vừa nướng chín tới sẽ là combo hoàn hảo cho buổi trò chuyện cùng bạn bè hay những giờ làm việc thăng hoa.', '/images/bacsiu.jpg', 1, 1, 1, NULL, '2026-08-12 09:09:43', '2026-08-12 09:09:43'),
(3, 'HÀNH TRÌNH TỪ ĐỒI TRÀ XANH MƯỚT ĐẾN LY TRÀ TRÁI CÂY THANH MÁT', 'hanh-trinh-tu-doi-tra-xanh-muot-den-ly-tra-trai-cay-thanh-mat', 'Bên cạnh cà phê nguyên chất, dòng Trà Trái Cây tại Chill Chill luôn là sự lựa chọn hàng đầu của giới trẻ nhờ hương vị thanh mát, tự nhiên và vô cùng giải nhiệt.\n\n1. Búp trà tươi ủ lạnh chuẩn vị\nChill Chill sử dụng những búp trà Oolong và Trà Xanh tươi nguyên bản được trồng trên các vùng đồi trà sương mù. Trà được ủ ở nhiệt độ tiêu chuẩn để chiết xuất trọn vẹn hương thơm thanh khiết, chát nhẹ hậu ngọt mà không hề bị đắng chát.\n\n2. Sự kết hợp bùng nổ cùng trái cây tươi\nMỗi ly trà là sự kết hợp ăn ý giữa nền trà đậm đà và các loại trái cây tươi ngon như Đào, Dâu tây, Cam sả, Vải hay Mãng cầu. Trái cây được chế biến tươi mỗi ngày, giữ trọn vị giòn ngọt và vitamin thiên nhiên.\n\n3. Thức uống tươi mát bảo vệ sức khỏe\nKhông chỉ mang lại cảm giác sảng khoái tức thì, trà trái cây Chill Chill còn chứa nhiều chất chống oxy hóa, hỗ trợ thanh lọc cơ thể và nạp lại nguồn năng lượng tươi trẻ cho bạn.', '/images/tra.png', 1, 2, 1, NULL, '2026-08-11 09:09:43', '2026-08-11 09:09:43'),
(4, 'THƯỞNG THỨC HỒNG TRÀ SỮA & TRÀ TRÁI CÂY NHIỆT ĐỚI ĐẶC BIỆT', 'thuong-thuc-hong-tra-sua-tra-trai-cay-nhiet-doi-dac-biet', 'Trà sữa và trà nhiệt đới luôn mang một sức hút đặc biệt đối với các tín đồ mê ẩm thực thức uống. Tại Chill Chill, chúng mình mang đến cho bạn danh mục trà đa dạng với hương vị cuốn hút độc đáo.\n\n1. Hồng Trà Sữa ngậy béo thơm lừng\nĐược pha chế từ nền Hồng Trà thượng hạng ủ đậm đà kết hợp cùng lớp kem sữa béo ngậy, ly Hồng Trà Sữa Chill Chill sở hữu vị ngọt thanh vừa phải, không gây ngấy. Thêm chút trân châu đen dẻo giòn hay trân châu hoàng kim là bạn đã có ngay ly trà sữa chuẩn gu.\n\n2. Trà Trái Cây Nhiệt Đới giải nhiệt cực đỉnh\nBản giao hưởng giữa vị chua ngọt hài hòa của Chanh vàng, Táo, Thơm (Dứa) và Dưa lưới đem lại cảm giác sảng khoái bất tận. Thức uống đầy màu sắc rực rỡ này chắc chắn sẽ làm bừng sáng góc chụp ảnh Check-in của bạn.\n\n3. Trải nghiệm dịch vụ giao hàng tận nơi\nDù ở văn phòng hay tại nhà, chỉ với vài thao tác đơn giản trên website, ly trà yêu thích của bạn sẽ được giao tới nhanh chóng, giữ trọn độ tươi ngon mát lạnh.', '/images/hongtrasua.png', 1, 2, 1, NULL, '2026-08-10 09:09:43', '2026-08-10 09:09:43'),
(5, 'THƯỞNG THỨC BÁNH NGỌT TƯƠI MỖI NGÀY CÙNG THỨC UỐNG CHILL CHILL', 'thuong-thuc-banh-ngot-tuoi-moi-ngay-cung-thuc-uong-chill-chill', 'Một buổi trà chiều hoàn hảo không thể thiếu sự hiện diện của những chiếc bánh ngọt thơm phức, mềm mịn được nướng mới mỗi ngày tại Chill Chill.\n\n1. Thực đơn bánh ngọt phong phú chuẩn vị Tiệm Bánh\nTừ Croissant bơ tỏi giòn rụm thơm lừng, Tiramisu cacao đậm đà mềm tan, đến Cheesecake chanh dây chua ngọt thanh mát... tất cả đều được chế biến từ nguồn nguyên liệu nhập khẩu cao cấp.\n\n2. Sự bắt cặp ăn ý cùng cà phê & trà\n- Tiramisu kết hợp cùng Cà phê Espresso: Sự hòa quyện bùng nổ giữa vị đắng nhẹ và béo ngậy.\n- Croissant kết hợp cùng Trà Trái Cây: Giúp cân bằng vị giác, tạo cảm giác nhẹ nhàng sảng khoái.\n\n3. Điểm hẹn trà chiều lý tưởng\nHãy tạm gác lại những xô xà công việc để tự thưởng cho mình một góc nhỏ yên bình, nhâm nhi tách trà thơm và thưởng thức món bánh yêu thích cùng Chill Chill bạn nhé!', '/images/banhngot.png', 1, 3, 1, NULL, '2026-08-09 09:09:43', '2026-08-09 09:09:43'),
(6, 'KHÁM PHÁ GÓI COMBO TIẾT KIỆM - UỐNG LÀ MÊ DÀNH CHO BẠN BÈ & CẶP ĐÔI', 'kham-pha-goi-combo-tiet-kiem-uong-la-me-danh-cho-ban-be-cap-doi', 'Thấu hiểu nhu cầu thưởng thức đa dạng cùng bạn bè và người thương, Chill Chill giới thiệu dòng Gói Combo Tiết Kiệm với mức giá ưu đãi cực hấp dẫn lên tới 30%.\n\n1. Đa dạng các gói Combo hấp dẫn\n- Combo Buổi Hẹn Ngọt Ngào: Trọn bộ 2 ly nước tùy chọn kèm 1 phần bánh Tiramisu mềm mịn.\n- Combo Bữa Sáng Năng Lượng: Tách Cà phê Phin đậm đà kết hợp cùng bánh Croissant giòn rụm.\n- Combo Giải Nhiệt Mùa Hè: Nhóm 3-4 ly Trà trái cây nhiệt đới với mức giá siêu ưu đãi.\n\n2. Đặt hàng dễ dàng - Ưu đãi trao tay\nMọi gói Combo đều được cập nhật thường xuyên trên trang chủ và chuyên mục Combo của website. Khách hàng có thể dễ dàng thêm vào giỏ hàng và đặt giao tận nơi nhanh chóng.\n\n3. Đồng hành cùng mọi cuộc vui\nKhông chỉ giúp tiết kiệm chi phí, các gói Combo Chill Chill còn mang tới sự tiện lợi và niềm vui trọn vẹn trong mọi dịp tụ họp, sinh nhật hay làm việc nhóm.', '/images/combobuoihen.png', 1, 3, 1, NULL, '2026-08-08 09:09:43', '2026-08-08 09:09:43');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `image_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `name`, `slug`, `description`, `status`, `is_featured`, `image_url`, `created_at`, `updated_at`) VALUES
(3, 8, 'Nước ép thơm', 'mon-ngon-chill-chill-3-177417390141', 'Sự kết hợp bừng tỉnh giữa vị chua chua ngọt ngọt đặc trưng và hương thơm tươi mát từ dứa. Thức uống giàu vitamin C, lý tưởng để giải khát và hỗ trợ tiêu hóa.', 1, 0, '/storage/products/KerRnyfOfSTQY01z7OXm1V6kLzmMA1rriiMwhUTW.jpg', '2026-03-22 03:05:01', '2026-08-14 06:09:27'),
(4, 8, 'Soda chanh', 'mon-ngon-chill-chill-4-177417390155', 'Vị chanh tươi mát kết hợp với bọt gas lăn tăn, tạo nên một thức uống sảng khoái và tràn đầy năng lượng.', 1, 0, '/storage/products/S7xF2UdLiPei5wf5xTSsdZAlcZvX6lmjGLWlzWw3.jpg', '2026-03-22 03:05:01', '2026-08-14 06:14:21'),
(5, 8, 'Soda dâu', 'mon-ngon-chill-chill-5-177417390126', 'Hương vị dâu tây ngọt ngào và sảng khoái với bọt gas nhẹ nhàng, thích hợp để thưởng thức trong những ngày hè nắng nóng.', 1, 0, '/storage/products/jNwfy0J7M7ziHOgLWMIu8Lr5XV5ehH1ueffdNshw.jpg', '2026-03-22 03:05:01', '2026-08-14 06:15:14'),
(6, 8, 'Soda việt quốc', 'mon-ngon-chill-chill-6-177417390111', 'Sự kết hợp độc đáo của việt quất tươi và soda, mang đến một thức uống sảng khoái và tràn đầy năng lượng với màu sắc bắt mắt.', 1, 0, '/storage/products/DZ4bbqx75Lb6IRcGm5p9psFSPCwj6q6MHlvFA6CW.jpg', '2026-03-22 03:05:01', '2026-08-14 06:16:07'),
(7, 8, 'Cam vắt', 'mon-ngon-chill-chill-7-177417390174', 'Thức uống giải nhiệt quốc dân từ những quả cam tươi mọng nước, dồi dào Vitamin C. Vị chua ngọt tự nhiên kết hợp cùng đá lạnh mang lại cảm giác sảng khoái, tràn đầy năng lượng tức thì.', 1, 0, '/storage/products/f4vRTgCKkEKZp77zVCrrzHykA03t7iWJNeR5BbhX.jpg', '2026-03-22 03:05:01', '2026-08-14 06:37:55'),
(8, 7, 'Hồng trà sửa', 'mon-ngon-chill-chill-8-177417390171', 'Hương cốt trà đen đậm đà, thơm lừng kết hợp hài hòa cùng vị sữa béo ngậy thanh dịu. Dòng trà sữa truyền thống quốc dân mang đến trải nghiệm ngọt ngào, êm dịu khó cưỡng.', 1, 0, '/storage/products/xlnbtCANOZjXiRBNVFWKtuOck4gN0axUq9YvbQeS.png', '2026-03-22 03:05:01', '2026-08-14 04:49:31'),
(9, 2, 'Trà vãi', 'mon-ngon-chill-chill-9-177417390124', 'Hương trà thơm ngát hòa quyện cùng vị ngọt thanh quyến rũ đặc trưng của vải chín. Đi kèm những quả vải mọng nước, trắng ngần, mang lại trải nghiệm giải khát cực kỳ tinh tế.', 1, 0, '/storage/products/amD7ScCXbHzvatcADc7Bh8Po9qucVgqgUwCeLXul.jpg', '2026-03-22 03:05:01', '2026-08-14 06:37:31'),
(11, 3, 'Sinh tố dâu', 'mon-ngon-chill-chill-11-177417390168', 'Thức uống trái cây tươi mới với vị chua ngọt tự nhiên của dâu tây, là lựa chọn tuyệt vời để giải nhiệt và bổ sung vitamin cho cơ thể.', 1, 0, '/storage/products/zyhgtPetMIJ0Udmw2JFMv4v2X3yOv1IbXwaSpyxp.jpg', '2026-03-22 03:05:01', '2026-08-14 06:38:20'),
(12, 2, 'Trà đào cam xả', 'mon-ngon-chill-chill-12-177417390147', 'Thức uống \"quốc dân\" với hương sả thơm nồng, vị cam chua nhẹ hài hòa cùng trà đào thanh ngọt. Đi kèm những lát đào giòn sần sật và cam tươi giải nhiệt tuyệt vời.', 1, 0, '/storage/products/tijYrmi8xv24fROH65qwMQt0H4VNHuX5ih1UdU6v.jpg', '2026-03-22 03:05:01', '2026-08-14 04:54:44'),
(13, 8, 'Nước ép Cà rốt', 'mon-ngon-chill-chill-13-177417390132', 'Thức uống thanh mát từ cà rốt tươi nguyên chất, giàu vitamin A và dưỡng chất tốt cho sức khỏe. Vị ngọt tự nhiên giúp giải nhiệt và làm đẹp da hiệu quả.', 1, 0, '/storage/products/TXgACzxdL8OWC5rugcL1jpvpmUE4zu4ekxhFviQu.jpg', '2026-03-22 03:05:01', '2026-08-14 06:06:23'),
(16, 2, 'Trà nhiệt đới', 'mon-ngon-chill-chill-16-177417390151', 'Một hỗn hợp tươi mới của các loại trái cây như cam và đào. Thức uống này không chỉ ngon miệng mà còn giàu vitamin và hương vị thơm ngon.', 1, 0, '/storage/products/Ysf2U91TSjADeNli199rCJGKmKGlRV11kJqqGsNg.jpg', '2026-03-22 03:05:01', '2026-08-14 06:20:37'),
(17, 2, 'Trà xoài', 'mon-ngon-chill-chill-17-177417390162', 'Vị trà thanh nhẹ kết hợp hoàn hảo cùng hương vị xoài chín vàng ngọt ngào, thơm lừng. Bổ sung các khối xoài tươi mọng nước mang đến cảm giác sảng khoái từng ngụm.', 1, 0, '/storage/products/9To9ExcnZYGT3IhjzWvVxWvx1EYMSp6fRPBPhwih.jpg', '2026-03-22 03:05:01', '2026-08-14 06:22:13'),
(18, 2, 'Trà thanh long', 'mon-ngon-chill-chill-18-177417390145', 'Sắc hồng tím quyến rũ tự nhiên từ thanh long đỏ kết hợp cùng nền trà thanh dịu. Thức uống mát lạnh ngập tràn những miếng thanh long tươi giòn giòn, thanh ngọt dễ chịu.', 1, 0, '/storage/products/RYlwC7qwmMlCKLPoMGToYuJaT5kzRWfGGYpj9uT6.jpg', '2026-03-22 03:05:01', '2026-08-14 06:23:00'),
(23, 8, 'Matcha latte', 'mon-ngon-chill-chill-23-177417390143', 'Thức uống thanh mát với hương matcha đậm đà, hòa quyện với vị béo của sữa tươi, lý tưởng cho những người yêu thích hương vị nguyên bản của trà xanh.', 1, 0, '/storage/products/eOs1HClw0zbh0EhTMrGbJZwtjvZjYGAqaDsLZyka.jpg', '2026-03-22 03:05:01', '2026-08-14 06:33:57'),
(24, 3, 'Cà phê đen', 'mon-ngon-chill-chill-24-177417390134', 'Hương vị cà phê đậm đà, chuẩn vị truyền thống kết hợp cùng đá lạnh sảng khoái. Lựa chọn hoàn hảo giúp bạn bừng tỉnh năng lượng và tập trung cho ngày mới.', 1, 0, '/storage/products/jhfxuV4yoauE1aTvZMG4Rk7lJ4vaj1U0yG6fLQZi.jpg', '2026-03-22 03:05:01', '2026-08-14 06:39:28'),
(27, 4, 'Trà chanh vàng', 'mon-ngon-chill-chill-27-177417390138', 'Thức uống giải nhiệt cổ điển với vị chua ngọt cân bằng, sảng khoái. Trà chanh đá là lựa chọn lý tưởng cho những ngày hè nắng nóng.', 1, 0, '/images/trachanhvang.jpg', '2026-03-22 03:05:01', '2026-08-14 06:35:15'),
(31, 2, 'Trà dâu', 'mon-ngon-chill-chill-31-177417390123', 'Hương vị dâu tây ngọt ngào và tươi mới trong từng ngụm trà. Đây là thức uống yêu thích của nhiều người, đặc biệt là vào mùa dâu.', 1, 0, '/storage/products/fj4VMx4UfbdpQJ2nq6d86nK0pDoceHF5LHYASMdo.jpg', '2026-03-22 03:05:01', '2026-08-14 06:17:09'),
(45, 8, 'Nước ép táo', 'mon-ngon-chill-chill-45-177417390155', 'Dòng nước ép vàng trong thanh khiết từ những quả táo tươi chọn lọc, mọng nước. Vị chua ngọt dịu nhẹ, tự nhiên giúp thanh lọc cơ thể và sạc lại năng lượng nhẹ nhàng.', 1, 0, '/storage/products/4dq5Z0oggMBkxJgsnUY7SfCVglk1YMFO0cSIpS9p.jpg', '2026-03-22 03:05:01', '2026-08-14 06:08:35'),
(46, 8, 'Nước ép dưa hấu', 'mon-ngon-chill-chill-46-177417390163', 'Thức uống giải nhiệt ngọt ngào với sắc đỏ bắt mắt, giữ trọn vị mọng nước tự nhiên của dưa hấu tươi. Mang lại cảm giác mát lạnh, sảng khoái tức thì cho những ngày nắng nóng.', 1, 0, '/storage/products/w3t1JSiC2Wvxk7LHiOej8gTvF7LPIP4a2zJxCPgm.jpg', '2026-03-22 03:05:01', '2026-08-14 06:07:36'),
(47, 7, 'Lục trà sữa', 'mon-ngon-chill-chill-47-177417390133', 'Dòng trà thanh khiết với hương thơm hoa nhài tự nhiên nhẹ nhàng và vị chát dịu hậu ngọt. Lựa chọn tuyệt vời giúp thanh lọc cơ thể, mang lại cảm giác thư thái và tỉnh táo.', 1, 0, '/storage/products/jD1FUGVo6B3eWFR0BDrW496UVaHTpPmmM4YDE1E1.jpg', '2026-03-22 03:05:01', '2026-08-14 06:11:17'),
(48, 3, 'Matcha đá xay', 'mon-ngon-chill-chill-48-177417390172', 'Sự kết hợp hoàn hảo giữa bột matcha nguyên chất và sữa tươi được xay nhuyễn với đá, tạo nên một thức uống béo ngậy và thơm lừng hương matcha.', 1, 0, '/storage/products/0MdbYXIsnbLwiPko2rxtJPRdmTEyRrOnQe32oE79.jpg', '2026-03-22 03:05:01', '2026-08-14 06:13:10'),
(54, 1, 'Bạc sỉu', 'cbr-1000rr-1774855383', 'Sự kết hợp hoàn hảo giữa vị đậm đà của cà phê  và vị béo ngậy của sữa tươi. Thức uống này mang lại cảm giác tỉnh táo và sảng khoái tức thì.', 1, 1, '/storage/products/qoS4WYFlZ973y0ikgQyDsbzAcDpt3L08KWfJJiIB.jpg', '2026-03-30 00:23:03', '2026-08-14 04:27:16'),
(62, 4, 'Bánh bông lan trứng muối', 'banh-bong-lan-trung-muoi-1786683884', 'Cốt bánh bông lan mềm mịn, xốp nhẹ kết hợp cùng lớp sốt bơ trứng béo ngậy. Phủ trên bề mặt là chà bông đậm đà và trứng muối bùi bùi, tạo nên sự cân bằng hoàn hảo giữa vị ngọt và mặn.', 1, 0, '/storage/products/AjsdyqNbeQi8z63iyjQHmR60j5G8gcxbICobeHXn.jpg', '2026-08-14 05:04:44', '2026-08-14 07:14:50'),
(63, 4, 'Bánh mì phô mai', 'banh-mi-pho-mai-1786686751', 'Món bánh mì nướng nóng hổi với lớp vỏ ngoài giòn nhẹ, ôm trọn nhân phô mai chảy béo ngậy, ngậy mướt bên trong. Lựa chọn tuyệt vời cho những ai mê đắm hương vị thơm bùi, đậm đà.', 1, 0, '/storage/products/rusJzmXDHBog0G2OIouD9eBHBvde7P5yJF31aVIw.jpg', '2026-08-14 05:52:31', '2026-08-14 07:14:40'),
(64, 4, 'Bánh su kem', 'banh-su-kem-1786686803', 'Những chiếc bánh su nhỏ nhắn với vỏ xốp giòn lăn lăn đường bột và sốt socola nhẹ. Bên trong đong đầy nhân kem tươi mát lạnh, béo mịn, tan ngay trong miệng khi thưởng thức.', 1, 0, '/storage/products/1KsHP03SsYnWnoIbbJHZh6ciTb74gw4GlxciLOGT.jpg', '2026-08-14 05:53:23', '2026-08-14 07:14:32'),
(65, 3, 'Caramel đá xay', 'caramel-da-xay-1786686966', 'Sự hòa quyện béo ngậy giữa cà phê, sữa và sốt caramel thơm lừng được xay mịn cùng đá. Thức uống ngọt ngào, mát lạnh đốn gục mọi tín đồ mê đồ ngọt.', 1, 0, '/storage/products/70fmG3qYPwr9AwSSKN5SKk4KQ2HchZgaVlBmDQZA.jpg', '2026-08-14 05:56:06', '2026-08-14 05:56:06'),
(66, 6, 'Chân châu đen', 'chan-chau-den-1786687018', 'Những viên trân châu dẻo dai, thơm bùi ngập trong sốt đường đen ngọt ngào. Topping \"quốc dân\" hoàn hảo để nâng tầm hương vị cho mọi món trà sữa hay cà phê.', 1, 0, '/storage/products/4rkPJHHikkOJb8MGtvObqDICLMQrfIhGt02o3WcI.jpg', '2026-08-14 05:56:58', '2026-08-14 07:14:23'),
(67, 4, 'Cheesecake chanh dây', 'cheesecake-chanh-day-1786687068', 'Bánh phô mai nướng béo ngậy kết hợp cùng lớp mút chanh dây chua ngọt thanh mát trên bề mặt. Hương vị tươi mới, hòa quyện tuyệt vời không gây cảm giác ngấy.', 1, 0, '/storage/products/RJvXzfvbI0OOCDODaVmQPzfAkyps2lhBWX8sikOK.jpg', '2026-08-14 05:57:48', '2026-08-14 07:14:13'),
(68, 4, 'Cheesecake việt quốc', 'cheesecake-viet-quoc-1786687113', 'Lớp kem phô mai mềm mịn nằm trên đế bánh giòn xốp, phủ tràn mứt việt quất chua ngọt cùng quả tươi thanh dịu. Món tráng miệng tinh tế và sang trọng.', 1, 0, '/storage/products/VWIHCTOsRxElnDqEB1tThLP7hkG6U1eY90J6LjV2.jpg', '2026-08-14 05:58:33', '2026-08-14 07:13:45'),
(69, 3, 'Chocolate đá xay', 'chocolate-da-xay-1786687214', 'Vị đắng nhẹ kết hợp hoàn hảo cùng độ ngọt béo đặc trưng của ca-cao nguyên chất xay đá. Mang đến trải nghiệm đậm vị, sảng khoái cực kỳ đã miệng.', 1, 0, '/storage/products/RvXMuuJs0PUut8qsupkLXNPkJvZVp4nvreU5zZSm.jpg', '2026-08-14 06:00:14', '2026-08-14 06:01:09'),
(70, 4, 'Croissan bơ', 'croissan-bo-1786687329', 'Bánh sừng bò ngàn lớp vàng ươm, vỏ ngoài giòn rụm cùng ruột bánh xốp mềm thơm lừng hương bơ Pháp. Món bánh ăn kèm cà phê lý tưởng cho buổi sáng nhẹ nhàng.', 1, 0, '/storage/products/VF0CtQoI18y0dFedhtyySQERGm6TGncYEeW2qjxs.jpg', '2026-08-14 06:02:09', '2026-08-14 07:13:34'),
(71, 2, 'Trà dưa lưới', 'tra-dua-luoi-1786688310', 'Một sự lựa chọn đơn giản nhưng không kém phần hấp dẫn với vị ngọt dịu của dưa. Thức uống này thích hợp để thưởng thức bất cứ lúc nào trong ngày.', 1, 0, '/storage/products/HCYh2IlmcSXtoTebPyHoyOjRygQzvO5JZOdHCMgS.jpg', '2026-08-14 06:18:30', '2026-08-14 06:18:30'),
(72, 4, 'Croissan chocolatte', 'croissan-chocolatte-1786691755', 'Lớp vỏ bánh ngàn lớp giòn tan phủ bột ca-cao quyến rũ, ôm trọn nhân socola sánh mịn bên trong. Sự kết hợp béo ngậy và đắng nhẹ đầy cuốn hút.', 1, 0, '/storage/products/aCdxTfTzLW3RhKH52xcRA7mh9qc3liXQU5lln84b.jpg', '2026-08-14 07:15:55', '2026-08-14 07:15:55'),
(73, 4, 'panna cotta', 'panna-cotta-1786691837', 'Món tráng miệng mềm mịn, béo ngậy với lớp mứt mâm xôi chua ngọt và trái mâm xôi tươi, chinh phục mọi thực khách bởi hương vị tinh tế.', 1, 0, '/storage/products/7XdloaFWVhAXv900ZqzzyA3Xp7FoebvwbqFgWs90.jpg', '2026-08-14 07:17:17', '2026-08-14 07:18:54'),
(74, 4, 'pudding trứng', 'pudding-trung-1786691919', 'Món tráng miệng cổ điển với lớp caramel ngọt ngào, mềm mịn và béo ngậy, mang đến cảm giác ngọt ngào và ấm áp.', 1, 0, '/storage/products/KNRYMuhS1y8l5lz2LtJPl5JeDQ6tm5r8rZRNYdlE.jpg', '2026-08-14 07:18:39', '2026-08-14 07:18:39'),
(75, 4, 'Tiramisu', 'tiramisu-1786692012', 'Món bánh Ý truyền thống với sự kết hợp hoàn hảo giữa lớp kem Mascarpone béo mịn và cốt bánh thấm đượm hương cà phê, phủ bột cacao đắng nhẹ đầy tinh tế.', 1, 0, '/storage/products/9K312QRjpj6JKIIly0fHkJChMEatXaOqGkGb1zqG.jpg', '2026-08-14 07:20:12', '2026-08-14 07:20:12'),
(76, 6, 'Thạch cà phê', 'thach-ca-phe-1786692069', 'Những viên thạch cà phê dai giòn, đậm vị đắng nhẹ đắm mình trong làn sữa tươi béo ngậy, ngọt ngào. Món tráng miệng thanh mát, kích thích giác quan vô cùng bắt miệng.', 1, 0, '/storage/products/e0zcmyes939ndih7HnmkxCDS66bfi2RiO3ygjAnM.jpg', '2026-08-14 07:21:09', '2026-08-14 07:21:09'),
(77, 6, 'Thạch dừa', 'thach-dua-1786692116', 'Thạch dừa trong suốt, giòn sần sật ăn kèm cơm dừa bào sợi thanh ngọt và nước dừa mát lành. Thức quà giải nhiệt tự nhiên, nhẹ nhàng cho những ngày oi ả.', 1, 0, '/storage/products/QHLPcyVlPtZ5VhzuBBSmty65yO985DNeRhrtBEM4.jpg', '2026-08-14 07:21:56', '2026-08-14 07:21:56'),
(78, 6, 'Trân châu hoàng kim', 'tran-chau-hoang-kim-1786692252', 'Những viên trân châu óng ánh sắc vàng, vừa dẻo dai vừa ngấm trọn vị mật ngọt dịu dàng. Lựa chọn hoàn hảo kết hợp cùng trà sữa béo ngậy để bùng nổ hương vị.', 1, 0, '/storage/products/Ak407YftUW0wpbYrDX9ZczUJ7xLwWPGAYvPAIA0b.jpg', '2026-08-14 07:24:12', '2026-08-14 07:24:12'),
(79, 6, 'Trân châu trắng', 'tran-chau-trang-1786692304', 'Những hạt trân châu ngọc trai mọng nước, giòn sần sật với vị ngọt thanh tự nhiên. Topping quốc dân giúp nâng tầm độ ngon mát cho mọi ly trà trái cây và trà sữa.', 1, 0, '/storage/products/3rplgJ1w7Z0ZBFH19lktk2b9YyFPlD3qrRZkN27j.jpg', '2026-08-14 07:25:04', '2026-08-14 07:25:04');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `image_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(5, 3, 1, 30000.00, NULL, NULL),
(6, 3, 2, 34000.00, NULL, NULL),
(7, 4, 1, 20000.00, NULL, NULL),
(8, 4, 2, 24000.00, NULL, NULL),
(9, 5, 1, 20000.00, NULL, NULL),
(10, 5, 2, 25000.00, NULL, NULL),
(11, 6, 1, 20000.00, NULL, NULL),
(12, 6, 2, 25000.00, NULL, NULL),
(13, 7, 1, 25000.00, NULL, NULL),
(14, 7, 2, 38000.00, NULL, NULL),
(15, 8, 1, 30000.00, NULL, NULL),
(16, 8, 2, 34000.00, NULL, NULL),
(17, 9, 1, 30000.00, NULL, NULL),
(18, 9, 2, 34000.00, NULL, NULL),
(21, 11, 1, 40000.00, NULL, NULL),
(22, 11, 2, 44000.00, NULL, NULL),
(23, 12, 1, 30000.00, NULL, NULL),
(24, 12, 2, 34000.00, NULL, NULL),
(25, 13, 1, 30000.00, NULL, NULL),
(26, 13, 2, 34000.00, NULL, NULL),
(31, 16, 1, 25000.00, NULL, NULL),
(32, 16, 2, 30000.00, NULL, NULL),
(33, 17, 1, 25000.00, NULL, NULL),
(34, 17, 2, 25000.00, NULL, NULL),
(35, 18, 1, 25000.00, NULL, NULL),
(36, 18, 2, 30000.00, NULL, NULL),
(45, 23, 1, 30000.00, NULL, NULL),
(46, 23, 2, 38000.00, NULL, NULL),
(47, 24, 1, 12000.00, NULL, NULL),
(48, 24, 2, 15000.00, NULL, NULL),
(53, 27, 1, 25000.00, NULL, NULL),
(54, 27, 2, 30000.00, NULL, NULL),
(61, 31, 1, 30000.00, NULL, NULL),
(62, 31, 2, 35000.00, NULL, NULL),
(89, 45, 1, 30000.00, NULL, NULL),
(90, 45, 2, 34000.00, NULL, NULL),
(91, 46, 1, 30000.00, NULL, NULL),
(92, 46, 2, 34000.00, NULL, NULL),
(93, 47, 1, 30000.00, NULL, NULL),
(94, 47, 2, 34000.00, NULL, NULL),
(95, 48, 1, 30000.00, NULL, NULL),
(96, 48, 2, 38000.00, NULL, NULL),
(101, 54, 1, 25000.00, NULL, NULL),
(105, 54, 2, 30000.00, NULL, NULL),
(106, 54, 3, 34000.00, NULL, NULL),
(107, 3, 3, 38000.00, NULL, NULL),
(108, 4, 3, 28000.00, NULL, NULL),
(109, 5, 3, 30000.00, NULL, NULL),
(110, 6, 3, 30000.00, NULL, NULL),
(111, 7, 3, 42000.00, NULL, NULL),
(112, 8, 3, 38000.00, NULL, NULL),
(113, 9, 3, 38000.00, NULL, NULL),
(114, 11, 3, 48000.00, NULL, NULL),
(115, 12, 3, 38000.00, NULL, NULL),
(118, 65, 1, 30000.00, NULL, NULL),
(119, 65, 2, 38000.00, NULL, NULL),
(120, 65, 3, 45000.00, NULL, NULL),
(124, 69, 1, 30000.00, NULL, NULL),
(125, 69, 2, 38000.00, NULL, NULL),
(126, 69, 3, 45000.00, NULL, NULL),
(129, 13, 3, 38000.00, NULL, NULL),
(130, 46, 3, 38000.00, NULL, NULL),
(131, 45, 3, 38000.00, NULL, NULL),
(132, 47, 3, 38000.00, NULL, NULL),
(133, 48, 3, 45000.00, NULL, NULL),
(134, 31, 3, 40000.00, NULL, NULL),
(135, 71, 1, 25000.00, NULL, NULL),
(136, 71, 2, 30000.00, NULL, NULL),
(137, 71, 3, 35000.00, NULL, NULL),
(138, 16, 3, 35000.00, NULL, NULL),
(139, 17, 3, 30000.00, NULL, NULL),
(140, 18, 3, 35000.00, NULL, NULL),
(141, 23, 3, 45000.00, NULL, NULL),
(142, 27, 3, 35000.00, NULL, NULL),
(143, 24, 3, 18000.00, NULL, NULL),
(145, 70, 4, 35000.00, NULL, NULL),
(146, 68, 4, 30000.00, NULL, NULL),
(147, 67, 4, 30000.00, NULL, NULL),
(148, 66, 4, 5000.00, NULL, NULL),
(149, 64, 4, 40000.00, NULL, NULL),
(150, 63, 4, 25000.00, NULL, NULL),
(151, 62, 4, 25000.00, NULL, NULL),
(152, 72, 4, 35000.00, NULL, NULL),
(153, 73, 4, 30000.00, NULL, NULL),
(154, 74, 4, 30000.00, NULL, NULL),
(155, 75, 4, 35000.00, NULL, NULL),
(156, 76, 4, 5000.00, NULL, NULL),
(157, 77, 4, 5000.00, NULL, NULL),
(158, 78, 4, 5000.00, NULL, NULL),
(159, 79, 4, 5000.00, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `combo_id` bigint UNSIGNED DEFAULT NULL,
  `order_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` int NOT NULL DEFAULT '5',
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `product_id`, `combo_id`, `order_id`, `rating`, `comment`, `image`, `created_at`, `updated_at`) VALUES
(1, 3, 1, NULL, '3', 1, 'hihi', NULL, '2026-07-21 00:07:46', '2026-07-21 00:07:46'),
(2, 3, 1, NULL, '2', 5, 'hấp dẫn', NULL, '2026-07-21 00:09:30', '2026-07-21 00:09:30'),
(3, 1, 1, NULL, '27', 5, 'Đồ uống hấp dẫn sẽ mua lại', NULL, '2026-07-21 04:31:35', '2026-07-21 04:31:35'),
(4, 1, 12, NULL, '29', 5, 'Tuyệt vời', NULL, '2026-07-21 04:54:56', '2026-07-21 04:54:56');

-- --------------------------------------------------------

--
-- Table structure for table `salary_payments`
--

CREATE TABLE `salary_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `month` int NOT NULL,
  `year` int NOT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('pending','paid') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `paid_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shifts`
--

INSERT INTO `shifts` (`id`, `name`, `date`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES
(1, 'Ca Ngày 06/06/2026', '2026-06-06', '06:00:00', '22:00:00', '2026-06-06 05:25:55', '2026-06-06 05:25:55'),
(2, 'Ca 6 (20:00 - 00:00)', '2026-06-06', '20:00:00', '23:59:59', '2026-06-06 14:35:57', '2026-06-06 14:35:57'),
(3, 'Ca 3 (08:00 - 12:00)', '2026-06-09', '08:00:00', '12:00:00', '2026-06-09 02:49:47', '2026-06-09 02:49:47'),
(4, 'Ca 6 (20:00 - 00:00)', '2026-06-14', '20:00:00', '23:59:59', '2026-06-14 15:17:34', '2026-06-14 15:17:34'),
(5, 'Ca 5 (16:00 - 20:00)', '2026-06-15', '16:00:00', '20:00:00', '2026-06-15 10:49:31', '2026-06-15 10:49:31'),
(6, 'Ca 6 (20:00 - 00:00)', '2026-06-15', '20:00:00', '23:59:59', '2026-06-15 15:18:39', '2026-06-15 15:18:39'),
(7, 'Ca 3 (08:00 - 12:00)', '2026-06-16', '08:00:00', '12:00:00', '2026-06-16 03:28:03', '2026-06-16 03:28:03'),
(8, 'Ca 5 (16:00 - 20:00)', '2026-06-22', '16:00:00', '20:00:00', '2026-06-22 12:05:39', '2026-06-22 12:05:39'),
(9, 'Ca 3 (08:00 - 12:00)', '2026-07-21', '08:00:00', '12:00:00', '2026-07-21 04:59:58', '2026-07-21 04:59:58'),
(10, 'Ca 4 (12:00 - 16:00)', '2026-07-21', '12:00:00', '16:00:00', '2026-07-21 05:00:04', '2026-07-21 05:00:04'),
(11, 'Ca 3 (08:00 - 12:00)', '2026-08-01', '08:00:00', '12:00:00', '2026-08-01 03:57:44', '2026-08-01 03:57:44'),
(12, 'Ca 4 (12:00 - 16:00)', '2026-08-04', '12:00:00', '16:00:00', '2026-08-04 05:01:06', '2026-08-04 05:01:06');

-- --------------------------------------------------------

--
-- Table structure for table `shift_registrations`
--

CREATE TABLE `shift_registrations` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `shift_date` date NOT NULL,
  `start_time` time NOT NULL,
  `duration` int NOT NULL,
  `status` enum('pending','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shift_registrations`
--

INSERT INTO `shift_registrations` (`id`, `user_id`, `shift_date`, `start_time`, `duration`, `status`, `created_at`, `updated_at`) VALUES
(1, 5, '2026-06-07', '11:11:00', 12, 'rejected', '2026-06-06 14:08:50', '2026-06-15 16:20:32'),
(2, 5, '2026-06-14', '06:00:00', 4, 'rejected', '2026-06-14 15:34:28', '2026-06-15 16:20:35'),
(3, 5, '2026-06-14', '14:00:00', 4, 'rejected', '2026-06-14 15:34:36', '2026-06-15 16:20:37'),
(4, 5, '2026-06-15', '06:00:00', 4, 'approved', '2026-06-15 16:21:30', '2026-06-15 16:21:50'),
(5, 5, '2026-06-15', '10:00:00', 4, 'approved', '2026-06-15 16:21:36', '2026-06-15 16:21:52'),
(6, 5, '2026-06-16', '10:00:00', 4, 'approved', '2026-06-16 03:30:21', '2026-06-16 03:31:07'),
(7, 5, '2026-06-16', '14:00:00', 4, 'approved', '2026-06-16 03:30:26', '2026-06-16 03:31:09'),
(8, 5, '2026-07-21', '06:00:00', 4, 'approved', '2026-07-21 01:31:59', '2026-07-21 01:32:50'),
(10, 5, '2026-07-21', '10:00:00', 4, 'approved', '2026-07-21 03:57:23', '2026-07-21 03:57:35'),
(11, 6, '2026-07-21', '10:00:00', 4, 'approved', '2026-07-21 03:59:34', '2026-07-21 03:59:44'),
(12, 5, '2026-07-29', '10:00:00', 4, 'approved', '2026-07-29 04:17:11', '2026-07-29 04:17:43');

-- --------------------------------------------------------

--
-- Table structure for table `shift_user`
--

CREATE TABLE `shift_user` (
  `id` bigint UNSIGNED NOT NULL,
  `shift_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shift_user`
--

INSERT INTO `shift_user` (`id`, `shift_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 2, 5, NULL, NULL),
(2, 3, 5, NULL, NULL),
(3, 3, 6, NULL, NULL),
(4, 4, 5, NULL, NULL),
(5, 5, 5, NULL, NULL),
(6, 6, 5, NULL, NULL),
(7, 7, 5, NULL, NULL),
(8, 8, 5, NULL, NULL),
(9, 9, 6, NULL, NULL),
(10, 10, 6, NULL, NULL),
(11, 11, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `shipping_address`
--

CREATE TABLE `shipping_address` (
  `address_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `full_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address_line` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sizes`
--

INSERT INTO `sizes` (`size_id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Size S', '2026-03-22 03:05:01', NULL),
(2, 'Size M', '2026-03-22 03:05:01', NULL),
(3, 'Size L', '2026-03-22 03:05:01', NULL),
(4, 'Mặc định', '2026-08-14 06:59:48', '2026-08-14 06:59:48');

-- --------------------------------------------------------

--
-- Table structure for table `toppings`
--

CREATE TABLE `toppings` (
  `topping_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `point` int NOT NULL DEFAULT '0',
  `is_locked` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `address`, `avatar`, `password`, `phone`, `role`, `point`, `is_locked`, `created_at`, `updated_at`, `remember_token`) VALUES
(1, 'User_0385792442', NULL, '[\"1222, Phường 13, Quận 10, TP. Hồ Chí Minh\"]', NULL, '$2y$12$tfntTfQ1XZpRnR4zM/jw.uiy4yj4I3ljSj.1BhkCdrIapgrzLEq86', '0385792442', 'admin', 5, 0, '2026-03-22 03:16:06', '2026-03-22 03:26:03', NULL),
(2, 'haibaconbo', NULL, NULL, NULL, '$2y$12$gzhJTd7Na0sPKjvNeC/DKOcZ7Kd3mwKLiZ.Wu1vqaCnY0uCCCi3na', NULL, 'user', 0, 0, '2026-03-26 06:06:35', '2026-03-26 06:06:35', NULL),
(3, 'hihi', 'nguyenthanhtrungkien2305@gmail.com', '[\"Phường Tân Định, Quận 1, TP. Hồ Chí Minh\",\"1222, Phường 15, Quận 11, TP. Hồ Chí Minh\",\"1222, Phường 5, Quận 11, TP. Hồ Chí Minh\"]', 'uploads/avatars/1775622475_download.jpg', '$2y$12$if5YnusIk82OK8vBAip9Ae70gn6bQeNUhQVu7DcWVnT.ng/9wCnlu', NULL, 'user', 0, 0, '2026-04-07 21:14:07', '2026-04-07 22:12:01', NULL),
(4, 'huhuhu', NULL, NULL, NULL, '$2y$12$Lwauv.MK/WQkMw9yekEC.O62IPnxaQe3GVsnVZKry.9YB8HGiWPjG', NULL, 'user', 0, 0, '2026-04-08 00:11:07', '2026-04-08 00:11:07', NULL),
(5, 'huyho', 'huyho@diemcongcoffee.com', '[\"1222, Phường 15, Quận 10, TP. Hồ Chí Minh\"]', NULL, '$2y$12$L9hUL2s2lNSuFwWe5Dv6fOCedFueTV/vccabQecLS2PPy/NDKhTHK', NULL, 'staff', 0, 0, '2026-05-30 02:52:39', '2026-05-30 02:52:39', 'Omymfsuph83Sy7qIVVFgquTFqXhM9vsdwfT9OH0ACfic0KtX53UnrDUpMKFP'),
(6, 'nhanvien1', NULL, NULL, NULL, '$2y$12$IyGSZGVS36A.XzgCu6ar6.XAsbq8sfJVv9lcV/IZM4DWNUNWFT4IG', '0888888888', 'staff', 0, 0, '2026-06-09 03:22:53', '2026-06-09 03:22:53', NULL),
(7, 'Quan', NULL, NULL, NULL, '$2y$12$AD.VuNPvD.QBXbLJvZE7fOcyv.h5KbTc7OiCeUENKNaRomGK9.s2m', '1234567890', 'user', 0, 0, '2026-06-09 03:40:16', '2026-08-12 04:16:10', NULL),
(8, 'User_123456', NULL, NULL, NULL, '$2y$12$aSehGmxzujDVbDyDadAatuz4rXstwmvagkC9gLILyyxhMeuTeHbuS', '123456', 'admin', 0, 0, '2026-08-12 04:15:33', '2026-08-12 04:16:03', NULL);

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

--
-- Dumping data for table `user_vouchers`
--

INSERT INTO `user_vouchers` (`id`, `user_id`, `voucher_id`, `is_used`, `save_at`, `created_at`, `updated_at`) VALUES
(5, 1, 7, 1, '2026-08-10 23:34:03', '2026-08-10 16:34:03', '2026-08-10 16:34:21'),
(6, 1, 7, 1, '2026-08-10 23:34:07', '2026-08-10 16:34:07', '2026-08-10 16:37:20');

-- --------------------------------------------------------

--
-- Table structure for table `vouchers`
--

CREATE TABLE `vouchers` (
  `voucher_id` bigint UNSIGNED NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `usage_limit` int DEFAULT NULL,
  `usage_per_user` int DEFAULT '1',
  `used_count` int NOT NULL DEFAULT '0',
  `min_order` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `points_required` int NOT NULL DEFAULT '10',
  `is_points_exchange` tinyint(1) NOT NULL DEFAULT '1',
  `assigned_user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vouchers`
--

INSERT INTO `vouchers` (`voucher_id`, `code`, `discount_type`, `discount_value`, `start_date`, `end_date`, `usage_limit`, `usage_per_user`, `used_count`, `min_order`, `created_at`, `updated_at`, `points_required`, `is_points_exchange`, `assigned_user_id`) VALUES
(7, '745810', 'percent', 20.00, '2026-08-10 23:33:00', '2026-12-31 23:33:00', 10000, 1, 2, 1000.00, '2026-08-10 16:33:51', '2026-08-10 16:33:51', 3, 1, NULL),
(8, 'SUMMER20', 'percent', 20.00, '2026-08-12 10:31:00', '2026-08-31 10:32:00', 9972, 1, 0, 199000.00, '2026-08-12 03:32:25', '2026-08-12 03:32:39', 10, 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendances_user_id_foreign` (`user_id`);

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`banner_id`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `carts_user_id_foreign` (`user_id`),
  ADD KEY `carts_variant_id_foreign` (`variant_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `cart_items_product_id_foreign` (`product_id`);

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
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_messages_chat_session_id_foreign` (`chat_session_id`);

--
-- Indexes for table `chat_sessions`
--
ALTER TABLE `chat_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chat_sessions_session_token_unique` (`session_token`),
  ADD KEY `chat_sessions_user_id_foreign` (`user_id`);

--
-- Indexes for table `combos`
--
ALTER TABLE `combos`
  ADD PRIMARY KEY (`combo_id`),
  ADD UNIQUE KEY `combos_slug_unique` (`slug`);

--
-- Indexes for table `combo_items`
--
ALTER TABLE `combo_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `combo_items_combo_id_foreign` (`combo_id`),
  ADD KEY `combo_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feedbacks_replied_by_foreign` (`replied_by`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `orders_voucher_id_foreign` (`voucher_id`),
  ADD KEY `orders_shift_id_foreign` (`shift_id`);

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
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `salary_payments`
--
ALTER TABLE `salary_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shift_registrations`
--
ALTER TABLE `shift_registrations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shift_registrations_user_id_foreign` (`user_id`);

--
-- Indexes for table `shift_user`
--
ALTER TABLE `shift_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shift_user_shift_id_foreign` (`shift_id`),
  ADD KEY `shift_user_user_id_foreign` (`user_id`);

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
  ADD UNIQUE KEY `vouchers_code_unique` (`code`),
  ADD KEY `vouchers_assigned_user_id_foreign` (`assigned_user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `banner_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `cart_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `cart_item_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `categories_post`
--
ALTER TABLE `categories_post`
  MODIFY `categories_post_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `chat_sessions`
--
ALTER TABLE `chat_sessions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `combos`
--
ALTER TABLE `combos`
  MODIFY `combo_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `combo_items`
--
ALTER TABLE `combo_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

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
  MODIFY `post_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `variant_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=160;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `salary_payments`
--
ALTER TABLE `salary_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `shift_registrations`
--
ALTER TABLE `shift_registrations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `shift_user`
--
ALTER TABLE `shift_user`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `shipping_address`
--
ALTER TABLE `shipping_address`
  MODIFY `address_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sizes`
--
ALTER TABLE `sizes`
  MODIFY `size_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `toppings`
--
ALTER TABLE `toppings`
  MODIFY `topping_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user_vouchers`
--
ALTER TABLE `user_vouchers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `voucher_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `attendances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carts_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_chat_session_id_foreign` FOREIGN KEY (`chat_session_id`) REFERENCES `chat_sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_sessions`
--
ALTER TABLE `chat_sessions`
  ADD CONSTRAINT `chat_sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `combo_items`
--
ALTER TABLE `combo_items`
  ADD CONSTRAINT `combo_items_combo_id_foreign` FOREIGN KEY (`combo_id`) REFERENCES `combos` (`combo_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `combo_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD CONSTRAINT `feedbacks_replied_by_foreign` FOREIGN KEY (`replied_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_voucher_id_foreign` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`voucher_id`) ON DELETE SET NULL;

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
-- Constraints for table `shift_registrations`
--
ALTER TABLE `shift_registrations`
  ADD CONSTRAINT `shift_registrations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `shift_user`
--
ALTER TABLE `shift_user`
  ADD CONSTRAINT `shift_user_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shift_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

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

--
-- Constraints for table `vouchers`
--
ALTER TABLE `vouchers`
  ADD CONSTRAINT `vouchers_assigned_user_id_foreign` FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
