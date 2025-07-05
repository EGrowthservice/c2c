-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost:3306
-- Thời gian đã tạo: Th5 24, 2025 lúc 05:30 AM
-- Phiên bản máy phục vụ: 8.0.30
-- Phiên bản PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `c2c_marketplace`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart`
--

CREATE TABLE `cart` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `created_at`) VALUES
(36, 2, 10, 1, '2025-05-23 06:40:27'),
(37, 1, 7, 1, '2025-05-24 04:23:36');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Điện tử', '2025-05-20 09:52:40', '2025-05-21 06:19:26'),
(2, 'Thời trang', '2025-05-20 09:52:40', '2025-05-20 09:52:40'),
(3, 'Đồ gia dụng', '2025-05-20 09:52:40', '2025-05-20 09:52:40'),
(4, 'Sách', '2025-05-20 09:52:40', '2025-05-20 09:52:40'),
(5, 'Khác', '2025-05-20 09:52:40', '2025-05-20 09:52:40'),
(6, 'Hieu truong', '2025-05-20 10:01:33', '2025-05-20 10:01:33');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `contacts`
--

CREATE TABLE `contacts` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `subject`, `message`, `created_at`, `updated_at`) VALUES
(1, 'ád ád', 'hieucb204@gmail.com', 'Mở lại dịch vụ free hosting đang tạm khóa', 'Mở lại dịch vụ free hosting đang tạm khóa', '2025-05-21 06:30:54', '2025-05-21 06:30:54'),
(2, 'ád ád', 'hieucb204@gmail.com', 'ád ád', 'ád ád', '2025-05-21 07:22:39', '2025-05-21 07:22:39'),
(3, 'hieucb204@gmail.com', 'hieucb204@gmail.com', 'hieucb204@gmail.com', 'hieucb204@gmail.com', '2025-05-21 09:14:25', '2025-05-21 09:14:25');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `favorites`
--

CREATE TABLE `favorites` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(6, 4, 5, '2025-05-20 14:18:43'),
(10, 1, 7, '2025-05-21 07:19:47');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `buyer_id` int NOT NULL,
  `seller_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled','pending_payment','confirmed') DEFAULT NULL,
  `tracking_number` varchar(50) DEFAULT NULL,
  `carrier` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `buyer_id`, `seller_id`, `product_id`, `quantity`, `total_price`, `status`, `tracking_number`, `carrier`, `created_at`, `updated_at`) VALUES
(105, 1, 2, 7, 2, 4400.00, 'pending', NULL, NULL, '2025-05-24 04:10:59', '2025-05-24 04:10:59'),
(106, 1, 2, 7, 1, 2200.00, 'pending', NULL, NULL, '2025-05-24 04:26:23', '2025-05-24 04:26:23'),
(107, 1, 2, 7, 1, 2200.00, 'pending', NULL, NULL, '2025-05-24 04:27:14', '2025-05-24 04:27:14'),
(108, 1, 2, 7, 1, 2200.00, 'pending', NULL, NULL, '2025-05-24 04:28:23', '2025-05-24 04:28:23'),
(109, 1, 2, 7, 1, 2200.00, 'pending', NULL, NULL, '2025-05-24 04:36:58', '2025-05-24 04:36:58'),
(110, 1, 2, 7, 1, 2200.00, 'pending', NULL, NULL, '2025-05-24 04:41:48', '2025-05-24 04:41:48'),
(111, 1, 2, 7, 1, 2200.00, 'pending', NULL, NULL, '2025-05-24 04:42:08', '2025-05-24 04:42:08'),
(112, 1, 2, 7, 1, 2200.00, 'confirmed', NULL, NULL, '2025-05-24 04:43:49', '2025-05-24 04:52:50'),
(113, 1, 2, 7, 1, 2200.00, 'pending_payment', NULL, NULL, '2025-05-24 04:53:57', '2025-05-24 04:53:58'),
(114, 1, 2, 7, 1, 2200.00, 'confirmed', NULL, NULL, '2025-05-24 05:01:16', '2025-05-24 05:07:38'),
(115, 1, 2, 7, 1, 2200.00, 'pending', NULL, NULL, '2025-05-24 05:12:07', '2025-05-24 05:12:07');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_detail`
--

CREATE TABLE `order_detail` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `pincode` varchar(10) NOT NULL,
  `state` varchar(100) NOT NULL,
  `town_city` varchar(100) NOT NULL,
  `house_no` varchar(255) NOT NULL,
  `road_name` varchar(255) NOT NULL,
  `landmark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `order_detail`
--

INSERT INTO `order_detail` (`id`, `order_id`, `fullname`, `phone`, `pincode`, `state`, `town_city`, `house_no`, `road_name`, `landmark`) VALUES
(102, 105, 'Hiếu Trương', '0941518881', '50217', 'Đà Nẵng', 'da nang', 'xc1231', 'Hiếu Trương', 'Hiếu Trương'),
(103, 106, 'Hiếu Trương', '0941518881', '50217', 'Đà Nẵng', 'da nang', 'xc1231', 'Hiếu Trương', 'Hiếu Trương'),
(104, 107, 'Hiếu Trương', '0941518881', '50217', 'Đà Nẵng', 'da nang', 'xc1231', 'Hiếu Trương', 'Hiếu Trương'),
(105, 108, 'Hiếu Trương', '0941518881', '50217', 'Đà Nẵng', 'da nang', 'xc1231', 'Hiếu Trương', 'Hiếu Trương'),
(106, 109, 'Hiếu Trương', '0941518881', '50217', 'Đà Nẵng', 'da nang', 'xc1231', 'Hiếu Trương', 'Hiếu Trương'),
(107, 110, 'Hiếu Trương', '0941518881', '50217', 'Đà Nẵng', 'da nang', 'xc1231', 'Hiếu Trương', 'Hiếu Trương'),
(108, 111, 'Hiếu Trương', '0941518881', '50217', 'Đà Nẵng', 'da nang', 'xc1231', '0941518881', '0941518881'),
(109, 112, 'Hiếu Trương', '0941518881', '50217', 'Đà Nẵng', 'da nang', 'xc1231', '0941518881', '0941518881'),
(110, 113, 'Hiếu Trương', '0941518881', '50217', 'Đà Nẵng', 'da nang', 'xc1231', 'Hiếu Trương', 'Hiếu Trương'),
(111, 114, 'Hiếu Trương', '0941518881', '50217', 'Đà Nẵng', 'da nang', 'xc1231', 'Hiếu Trương', 'Hiếu Trương'),
(112, 115, 'Hiếu Trương', '0941518881', '50217', 'Đà Nẵng', 'da nang', 'xc1231', 'Hiếu Trương', 'Hiếu Trương');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payment`
--

CREATE TABLE `payment` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `payment_method` enum('cod','payos') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','failed','cancelled') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `payment`
--

INSERT INTO `payment` (`id`, `order_id`, `payment_method`, `transaction_id`, `amount`, `status`, `created_at`) VALUES
(99, 105, 'cod', NULL, 4400.00, 'pending', '2025-05-24 04:10:59'),
(100, 106, 'payos', NULL, 2200.00, 'pending', '2025-05-24 04:26:23'),
(101, 107, 'payos', NULL, 2200.00, 'pending', '2025-05-24 04:27:14'),
(102, 108, 'payos', NULL, 2200.00, 'pending', '2025-05-24 04:28:23'),
(103, 109, 'payos', NULL, 2200.00, 'pending', '2025-05-24 04:36:58'),
(104, 110, 'payos', NULL, 2200.00, 'pending', '2025-05-24 04:41:48'),
(105, 111, 'payos', NULL, 2200.00, 'pending', '2025-05-24 04:42:08'),
(106, 112, 'payos', NULL, 2200.00, 'completed', '2025-05-24 04:43:49'),
(107, 113, 'payos', NULL, 2200.00, 'pending', '2025-05-24 04:53:57'),
(108, 114, 'payos', NULL, 2200.00, 'completed', '2025-05-24 05:01:16'),
(109, 115, 'payos', NULL, 2200.00, 'pending', '2025-05-24 05:12:07');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `image` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT '0',
  `views` int DEFAULT '0',
  `category_id` int NOT NULL,
  `seller_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `user_id`, `title`, `description`, `price`, `status`, `created_at`, `image`, `is_featured`, `views`, `category_id`, `seller_id`) VALUES
(5, 1, 'Sản phẩm mẫu 1', 'Mô tả sản phẩm mẫu 1', 1000000.00, 'approved', '2025-05-18 09:24:04', 'product-2-1.jpg', 1, 214, 1, 1),
(6, 1, 'Sản phẩm mẫu 2', 'Mô tả sản phẩm mẫu 2', 2000000.00, 'approved', '2025-05-18 09:24:04', 'product-2-1.jpg', 0, 206, 2, 1),
(7, 2, 'zxc1', 'zxc', 2000.00, 'approved', '2025-05-18 10:44:37', 'product-1-1.jpg', 0, 20, 1, 2),
(9, 1, 'xcxzc', 'xcxzc1', 2000.00, 'approved', '2025-05-19 05:10:27', 'product-1-3.jpg  ', 0, 1, 4, 1),
(10, 1, 'Tiêu đề ', 'Tiêu đề ', 31000.00, 'approved', '2025-05-20 09:06:45', '682c462599607_certificate-TC-2341.png', 0, 31, 5, 1),
(11, 1, 'zxc', 'zxc', 2000.00, 'approved', '2025-05-20 10:03:47', '682c53833842d_127.0.0.1_8000_admin_categories_add.png', 0, 13, 6, 1),
(12, 5, 'spmoi1', 'spmoi', 31000.00, 'approved', '2025-05-21 09:09:11', '682d9837cc036_product-16.jpg', 0, 10, 1, 5);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reports`
--

CREATE TABLE `reports` (
  `id` int NOT NULL,
  `reported_user_id` int NOT NULL,
  `reason` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `reports`
--

INSERT INTO `reports` (`id`, `reported_user_id`, `reason`, `created_at`) VALUES
(1, 2, 'no no ', '2025-05-20 06:04:21'),
(4, 1, 'zxc', '2025-05-20 14:23:37'),
(5, 2, 'xzc', '2025-05-21 07:19:54'),
(6, 5, 'sp k tot', '2025-05-21 09:13:30');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

CREATE TABLE `reviews` (
  `id` int NOT NULL,
  `product_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `comment` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Đang đổ dữ liệu cho bảng `reviews`
--

INSERT INTO `reviews` (`id`, `product_id`, `user_id`, `rating`, `comment`, `created_at`) VALUES
(1, 5, 1, 2, 'zxc', '2025-05-18 10:37:11');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `seller_ratings`
--

CREATE TABLE `seller_ratings` (
  `id` int NOT NULL,
  `seller_id` int NOT NULL,
  `buyer_id` int NOT NULL,
  `rating` tinyint NOT NULL,
  `comment` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Đang đổ dữ liệu cho bảng `seller_ratings`
--

INSERT INTO `seller_ratings` (`id`, `seller_id`, `buyer_id`, `rating`, `comment`, `created_at`) VALUES
(1, 1, 2, 5, 'Người bán giao hàng nhanh, hỗ trợ tốt!', '2025-05-18 11:19:36');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `transactions`
--

CREATE TABLE `transactions` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `status` enum('pending','completed','failed','pending_payment','cancelled') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `transaction_id` varchar(100) DEFAULT NULL,
  `order_code` bigint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `transactions`
--

INSERT INTO `transactions` (`id`, `order_id`, `amount`, `payment_method`, `status`, `created_at`, `transaction_id`, `order_code`) VALUES
(18, 105, 4400.00, 'cod', 'pending', '2025-05-24 04:10:59', NULL, NULL),
(19, 112, 2200.00, 'payos', 'pending', '2025-05-24 04:43:52', 'bd4d4bf9f9e7431e8913aca85a5d6bbf', 112),
(20, 112, 2200.00, 'payos', 'completed', '2025-05-24 04:52:50', NULL, 112),
(21, 113, 2200.00, 'payos', 'pending', '2025-05-24 04:53:58', '581590b539854805bc853073bb675541', 113),
(22, 114, 2200.00, 'payos', 'pending', '2025-05-24 05:01:18', 'bc2594cd2f4144cc9633a03cf42daf34', 114),
(23, 114, 2200.00, 'payos', 'completed', '2025-05-24 05:07:38', NULL, 114);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `average_rating` decimal(3,2) DEFAULT '0.00',
  `rating_count` int DEFAULT '0',
  `is_active` int NOT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`, `average_rating`, `rating_count`, `is_active`, `reset_token`, `reset_token_expires`) VALUES
(1, 'admin@gmail.com', 'admin@gmail.com', '$2y$10$.N1Zh9daK6MC1wT12BEGmecHCvD9eMOrHh17jUK9bx3yjj/lo.2ky', 'admin', '2025-05-18 09:23:52', 0.00, 0, 0, NULL, NULL),
(2, 'test@gmail.com', 'test@gmail.com', '$2y$10$V/hWgIL5VpZIcnpz6bR1HuM2v2IE9I/BjEo5i6Utmhnn9N2kKc1O2', 'user', '2025-05-18 10:37:24', 0.00, 0, 0, NULL, NULL),
(4, 'tes11t@gmail.com', 'tes11t@gmail.com', '$2y$10$HJiUXaTUQgaJnYBW4GcxUu272CvrsTTWi23lpUx8Ezs1unZ6Dj4Ue', 'user', '2025-05-20 10:12:19', 0.00, 0, 1, NULL, NULL),
(5, 'hieucv204@gmail.com', 'hieucv204@gmail.com', '$2y$10$8MMBML6Jx8Cgz7NXRFO78uS4NZyYBiNPRu1obaSmI60dUTSJDnD1.', 'user', '2025-05-21 06:09:30', 0.00, 0, 0, NULL, NULL),
(9, 'hieucv2004@gmail.com', 'hieucv2004@gmail.com', '$2y$10$o0vjEY27wWaE6ag4xQ/tj.8cwfZYVhpS9ceBlCEuFnKaaCvDfOfTS', 'user', '2025-05-21 08:46:46', 0.00, 0, 1, '6e7a7bf117b638bdf8a7b62bb0a7bee83cf2a4bfd0e5257121e4571ae3f2a637', '2025-05-21 16:53:59');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `buyer_id` (`buyer_id`),
  ADD KEY `seller_id` (`seller_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Chỉ mục cho bảng `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `products_ibfk_2` (`category_id`),
  ADD KEY `products_ibfk_3` (`seller_id`);

--
-- Chỉ mục cho bảng `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reported_user_id` (`reported_user_id`);

--
-- Chỉ mục cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `seller_ratings`
--
ALTER TABLE `seller_ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seller_id` (`seller_id`),
  ADD KEY `buyer_id` (`buyer_id`);

--
-- Chỉ mục cho bảng `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT cho bảng `order_detail`
--
ALTER TABLE `order_detail`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT cho bảng `payment`
--
ALTER TABLE `payment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `seller_ratings`
--
ALTER TABLE `seller_ratings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Ràng buộc đối với các bảng kết xuất
--

--
-- Ràng buộc cho bảng `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Ràng buộc cho bảng `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Ràng buộc cho bảng `order_detail`
--
ALTER TABLE `order_detail`
  ADD CONSTRAINT `order_detail_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Ràng buộc cho bảng `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `products_ibfk_3` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Ràng buộc cho bảng `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`reported_user_id`) REFERENCES `users` (`id`);

--
-- Ràng buộc cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ràng buộc cho bảng `seller_ratings`
--
ALTER TABLE `seller_ratings`
  ADD CONSTRAINT `seller_ratings_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `seller_ratings_ibfk_2` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`);

--
-- Ràng buộc cho bảng `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
