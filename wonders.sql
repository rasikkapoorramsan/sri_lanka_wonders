-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 16, 2025 at 10:55 PM
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
-- Database: `wonders`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`) VALUES
(33, 21, 4, 8),
(38, 22, 12, 1),
(46, 24, 4, 4),
(47, 24, 3, 2),
(48, 24, 12, 1),
(53, 25, 2, 1),
(60, 26, 3, 1),
(61, 26, 4, 1),
(62, 26, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `shipping_address` text DEFAULT NULL,
  `whatsapp_number` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','completed','canceled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total`, `shipping_address`, `whatsapp_number`, `created_at`, `status`) VALUES
(13, 20, 20.00, 'bibile medagama', '+94785607092', '2025-09-13 22:52:35', ''),
(14, 20, 6.00, 'colombo', '+94785607092', '2025-09-13 23:02:08', ''),
(15, 20, 6.00, 'ghfjgvukj', '+94785607092', '2025-09-13 23:16:23', 'pending'),
(16, 20, 5.00, 'monaragala ,bibile ,medagama', '+94785607092', '2025-09-13 23:23:36', 'pending'),
(17, 21, 5.00, '10th mile post medagama ,bibie', '+94785607092', '2025-09-15 00:27:50', 'pending'),
(18, 22, 11.00, 'colombo ', '+94785607092', '2025-09-15 02:09:05', 'canceled'),
(19, 22, 6.00, 'monaragala', '+94785607092', '2025-09-15 02:25:55', 'canceled'),
(20, 22, 0.00, 'anuradhapura', '+94785607092', '2025-09-15 02:34:42', 'canceled'),
(21, 23, 0.00, 'puthala,', '+94785607092', '2025-09-15 03:02:48', 'canceled'),
(22, 24, 0.00, 'colombo moratuva', '+94785607092', '2025-09-15 21:09:50', ''),
(23, 24, 0.00, 'colombo', '+94785607092', '2025-09-16 18:22:00', 'pending'),
(24, 25, 0.00, 'monaragala', '+94785825446', '2025-09-16 18:31:05', 'pending'),
(25, 25, 0.00, 'monragala', '+94785825446', '2025-09-16 18:32:11', 'pending'),
(26, 25, 0.00, 'monaragala', '+94785825446', '2025-09-16 18:37:35', 'pending'),
(27, 26, 0.00, 'bibile', '+94785825446', '2025-09-16 18:43:18', 'pending'),
(28, 26, 0.00, 'mahiyangana', '+94785825446', '2025-09-16 18:48:39', 'pending'),
(29, 26, 0.00, 'maathara', '+94785825446', '2025-09-16 18:49:44', 'pending'),
(30, 26, 0.00, 'gaali', '+94785825446', '2025-09-16 18:55:02', 'pending'),
(31, 26, 0.00, 'medagama', '+94785825446', '2025-09-16 18:56:35', 'pending'),
(32, 27, 0.00, 'veligama', '+94785825446', '2025-09-16 19:04:48', 'canceled'),
(33, 27, 6.00, 'putthlam', '+94785825446', '2025-09-16 19:11:11', 'canceled'),
(34, 27, 9.50, 'kanulwela', '+94785825446', '2025-09-16 19:14:07', 'canceled'),
(35, 27, 5.00, 'drerwyuytuyttr', '+94785607092', '2025-09-16 19:17:38', 'canceled'),
(36, 27, 10.00, 'bibile', '+94785825446', '2025-09-16 19:18:37', 'canceled'),
(37, 28, 0.00, 'tamil nadu ', '+94785825446', '2025-09-16 19:25:24', 'canceled'),
(38, 28, 0.00, 'colombo', '+94785825446', '2025-09-16 19:35:26', 'pending'),
(39, 29, 0.00, 'colombo', '+94785825446', '2025-09-16 19:47:40', ''),
(40, 30, 0.00, 'velimada', '+94785825446', '2025-09-16 20:37:11', 'pending');

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

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(15, 13, 3, 4, 5.00),
(16, 14, 4, 1, 6.00),
(17, 15, 4, 1, 6.00),
(18, 16, 3, 1, 5.00),
(19, 17, 3, 1, 5.00),
(20, 18, 4, 1, 6.00),
(21, 18, 12, 1, 5.00),
(22, 19, 4, 1, 6.00),
(23, 20, 4, 1, 6.00),
(24, 21, 4, 1, 6.00),
(25, 22, 3, 1, 5.00),
(26, 22, 4, 1, 6.00),
(27, 23, 3, 1, 5.00),
(28, 24, 3, 1, 5.00),
(29, 25, 4, 2, 6.00),
(30, 25, 3, 1, 5.00),
(31, 26, 4, 1, 6.00),
(32, 27, 3, 1, 5.00),
(33, 28, 2, 1, 4.50),
(34, 29, 3, 1, 5.00),
(35, 30, 4, 1, 6.00),
(36, 31, 3, 1, 5.00),
(37, 31, 2, 1, 4.50),
(38, 32, 3, 1, 5.00),
(39, 32, 4, 1, 6.00),
(40, 32, 12, 2, 5.00),
(41, 33, 4, 1, 6.00),
(42, 34, 3, 1, 5.00),
(43, 34, 2, 1, 4.50),
(44, 35, 12, 1, 5.00),
(45, 36, 3, 1, 5.00),
(46, 36, 12, 1, 5.00),
(47, 37, 2, 10, 4.50),
(48, 37, 3, 1, 5.00),
(49, 37, 4, 1, 6.00),
(50, 38, 2, 1, 4.50),
(51, 39, 2, 4, 4.50),
(52, 40, 3, 1, 5.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`) VALUES
(2, 'Hoppers (Appa)', 'Crispy, bowl-shaped pancakes fermented from rice flour and coconut milk. Often topped with a fried egg and served with spicy sambol for breakfast.', 4.50, 'https://via.placeholder.com/400x200?text=Hoppers'),
(3, 'Kiribath (Milk Rice)', 'Traditional rice cooked in thick coconut milk, often shaped into diamonds and eaten on special occasions with lunu miris (spicy onion relish).', 5.00, 'https://via.placeholder.com/400x200?text=Kiribath'),
(4, 'Dhal Curry (Parippu)', 'A staple lentil curry made with red lentils, coconut milk, turmeric, and tempered spices. Mild and comforting, pairs perfectly with rice.', 6.00, 'https://via.placeholder.com/400x200?text=Dhal+Curry'),
(12, 'Spices', 'Spices\r\nCultural Significance: Sri Lanka is historically known as \"The Spice Island,\" and its spices are integral to its cuisine and traditional medicine.\r\n\r\nExport Items:\r\n\r\nCeylon Cinnamon: Considered the finest and \"true\" cinnamon in the world.\r\n\r\nCardamom: A highly valued spice used in curries, sweets, and beverages.\r\n\r\nCloves, Pepper, and Nutmeg: Essential spices that add depth and flavor to many dishes.\r\n\r\nExport Form: Whole, ground, or in the form of essential oils and oleoresins.', 5.00, 'C:\\xampp\\htdocs\\uni_example\\all image\\bottom-view-bowls-with-spices-round-platter-turmeric-salt-black-pepper-red-pepper-powder-cut-vegetables-white-surface.jpg'),
(15, 'thosa', 'u67i8iuyi87tiyu', 5.00, 'https://via.placeholder.com/400x200?text=Fish+Ambul+Thiyal'),
(16, 'levariyaa', 'haoi;fg[rhl][t\r\ny;h\\]\r\nty', 10.00, 'C:\\xampp\\htdocs\\uni_example\\download.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'ramsan', 'ramsan@gmail.com', '$2y$10$eYKfuLSX140ZEyWlWqa1JOBsb1rQbI0rg07iiQ9uSWti6spffP44a', '2025-09-13 03:47:15'),
(3, 'ramsan rasik', 'ramsan1@gmail.com', '$2y$10$grb/YtP9HFDauuLhzysOJOERW7uKqAswmRotXn.ZpH/R5kaLFFpFW', '2025-09-13 03:49:24'),
(7, 'ramsanrasik', 'ramsan5@gmail.com', '$2y$10$V6aWtvwplDr6YHN/7kOz7eM2kdz/3wQ4tYcgoW3/Kx7wNYRFBYYAu', '2025-09-13 03:51:19'),
(11, 'rkm ramsan', 'ramsankhan@gmail.com', '$2y$10$WFvzeylbPFHuwrDVoYhfruukDcTYaEVuxIvwJmq5bxLb83PuGFroK', '2025-09-13 03:53:12'),
(12, 'isamil deen', 'ismaildeen@gmail.com', '$2y$10$Y.P6A.NgyLKLjD9qEGQ3zesOolknsOfM376/j.Wue8wUdwoQpTKte', '2025-09-13 04:27:21'),
(13, 'ismail deen', 'ismaildeen@gamil.com', '$2y$10$/Xvc6HzP78JzrPovqRxDiOyULoospaX5PTtFTHT6cKepaAluHYVIC', '2025-09-13 04:28:42'),
(14, 'muhammadu ismail', 'ismailmuhammad@gmail.com', '$2y$10$azZFwmSGDhlvbSznjOHweufcBPj2sLwGZkgujXldRPpcOSDYqvNRW', '2025-09-13 04:31:31'),
(15, 'ismil', 'nawfar@gamil.com', '$2y$10$Us2lMFLAc4M0UMwMRW/1puPkTEx26OrHRfXOsZP1/MDp0NheCD1fK', '2025-09-13 17:46:22'),
(18, 'rasidh', 'rasidh@gamil.com', '$2y$10$9VMbrgQhRDrWr8njoXLiiuyW8AhZP4wHZgfMOyb17EQ..MR0We4su', '2025-09-13 18:19:38'),
(20, 'r.k.m ramsan', 'rkmramsan@gmail.com', '$2y$10$JRj5.AUi47AHdOQfhcM4IeNVGLpoXHDfsi.UJoncR9KELLDLJlrU2', '2025-09-13 22:51:14'),
(21, 'r.k.m ramsana', 'ramsana@gmail.com', '$2y$10$.Xz.rptKhbgS/TRwZxc0gOzH7EwIerSCfLC2QwEKitiPiAZp2B682', '2025-09-15 00:24:48'),
(22, 'ramsanrk', 'ramsanrk@gmail.com', '$2y$10$SNaNY9/RqtwjYcZABeGQCOB33mjR8T1PEPVUJni0shhXCBtI//x7a', '2025-09-15 02:07:01'),
(23, 'mahaad', 'mahaad@gmail.com', '$2y$10$YK7WSp0sboduKiTKUCAk8eUcFAa8fk6FNkrT.D9QWUTTQeV9zJJD2', '2025-09-15 03:02:02'),
(24, 'fathima shiyama', 'shiyama@gmail.com', '$2y$10$valNNhlqFxqyV9oQn0SbOOvHLV4VpBbJVF5lPkIAQNu3bLLG6vBG.', '2025-09-15 21:05:02'),
(25, 'rasik kapoor ramsan', 'muhammad@gmail.com', '$2y$10$nBlPqnhpElk1.GFI7CgLCuKoWK9wWu4NdxQL6zLaf8uWg6SZ9eZ8W', '2025-09-16 18:28:58'),
(26, 'rasik', 'rasik@gmail.com', '$2y$10$s8ClKIDlQUBWGkUvqZSGvOG4YteV1SF5rPNDaj6Sz6vvMuhAO1ynG', '2025-09-16 18:41:16'),
(27, 'ramshiya', 'ramshiya@gmail.com', '$2y$10$kWPiclw4qWpSR.SXx4Q2QuBU5wW4/buYl979p8dQAFaghifFblcE.', '2025-09-16 19:03:30'),
(28, 'vijay', 'vijay@gmail.com', '$2y$10$0hLYpjjZk2AHeER5hULCXeCotqSLIXGLxHw.LcV/aaI66Lr9jxu56', '2025-09-16 19:23:51'),
(29, 'shazny', 'shazny@gmail.com', '$2y$10$y45.9CivU6RfmgJ6pHrUkuZBqrjVlw7GtqHG8ZNGL7z39Z91zO1dy', '2025-09-16 19:46:42'),
(30, 'aslam', 'aslam@gmail.com', '$2y$10$3IF0YYc0wtiyqL7BaSnRB.3031OtMQRa/y9hypA2IdVtZCxWJ4M6a', '2025-09-16 20:36:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
