-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 11, 2025 at 11:48 AM
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
-- Database: `crud_operation`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` int(100) NOT NULL,
  `quantity` int(100) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `name`, `price`, `quantity`, `image`) VALUES
(19, 10, 'The World of Art', 50, 1, '1741033264_the_world.jpg'),
(20, 11, 'The World of Art', 50, 1, '1741033264_the_world.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `number` varchar(12) NOT NULL,
  `message` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`id`, `user_id`, `name`, `email`, `number`, `message`) VALUES
(1, 101, 'Alice', 'alice@example.com', '1234567890', 'Hello!'),
(0, 12, 'boring_girls_a_novel', 'sameer.bagde.cse@ghrce.raisoni.net', '982574024', 'ewrewr3rwefasdvdsvasawew4t');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `number` varchar(12) NOT NULL,
  `email` varchar(255) NOT NULL,
  `method` varchar(50) NOT NULL,
  `address` varchar(500) NOT NULL,
  `total_products` varchar(1000) NOT NULL,
  `total_price` varchar(100) NOT NULL,
  `placed_on` varchar(50) NOT NULL,
  `payment_status` varchar(30) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `name`, `number`, `email`, `method`, `address`, `total_products`, `total_price`, `placed_on`, `payment_status`) VALUES
(10, 4, 'ergfhvbn', '456765432456', 'bagdesameer92@gmail.com', 'credit card', 'flat no. 345, 45, 3456edfg, 4tergfb - 54857', 'The Happy Lemon (1) ', '30', '04-Mar-2025', 'pending'),
(12, 4, 'sddgfg', '456787654', 'honk@gmail.com', 'credit card', 'flat no. 34234, edfc, rfvbgf, 23e4rfgvr - 2345', 'The Happy Lemon (2) ', '60', '06-Mar-2025', 'completed'),
(15, 4, 'boring_girls_a_novel', '53454353', 'email@gmail.com', 'paypal', 'flat no. 4543543, 4354354, nagput, 4twrgfdv - 3435435', 'The World of Art (5) ', '250', '06-Mar-2025', 'completed'),
(16, 4, 'boring_girls_a_novel', '34324324', 'bagdesameer92@gmail.com', 'paypal', 'flat no. 34324, 3324dfsdfef, dsfdsfwe, 3rwesfsdfq3r23 - 34324', 'The World of Art (3) ', '150', '10-Mar-2025', 'pending'),
(17, 4, 'sdfwerer3r3', '234234324', 'sameer.bagde.cse@ghrce.raisoni.net', 'paytm', 'flat no. 34324, 3r23432rs, rwf23r32, wf23r23re - 23434', 'The World of Art (3) ', '150', '10-Mar-2025', 'pending'),
(18, 12, 'boring_girls_a_novel', '342435435435', 'sameer.bagde.cse@ghrce.raisoni.net', 'paypal', 'flat no. 3232432, 3wewadsadf, 3243rfsf, 3r4wfsfqwe - 34324', 'The World of Art (1) ', '50', '10-Mar-2025', 'pending'),
(19, 12, 'rwedsf', '34324', 'sameer.bagde.cse@ghrce.raisoni.net', 'paypal', 'flat no. 2323e3, 34324wefs, 34324wefs, 23rsfsdfdsf - 234324', 'The World of Art (1) ', '50', '10-Mar-2025', 'pending'),
(20, 12, '4ertsgdfdsg', '345435345', 'sameer.bagde.cse@ghrce.raisoni.net', 'paytm', 'flat no. 33234, 3r5wfdsfds, 423rsdfsdf, 23rwefsdf - 234234', 'The Happy Lemon (1) ', '30', '10-Mar-2025', 'pending'),
(21, 12, 'boring_girls_a_novel', '32434234', 'sameer.bagde.cse@ghrce.raisoni.net', 'paytm', 'flat no. 0, wr324234324, 23efsf, india - 34234', 'The World of Art (1) ', '50', '10-Mar-2025', 'pending'),
(22, 12, 'boring_girls_a_novel', '343245424254', 'sameer.bagde.cse@ghrce.raisoni.net', 'paytm', 'flat no. 23432423, 3wefdsfwe3, fdsvdxfv, dfsdfwr - 23434', 'The World of Art (1) ', '50', '10-Mar-2025', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` varchar(100) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `image`) VALUES
(6, 'The Happy Lemon', '30', '1740984972_the_happy_lemon.jpg'),
(7, 'The World of Art', '50', '1741033264_the_world.jpg'),
(8, 'bash_and_lucy-2', '35', '1741102799_bash_and_lucy-2.jpg'),
(9, 'boring_girls_a_novel', '65', '1741102815_boring_girls_a_novel.jpg'),
(10, 'clever_lands', '45', '1741102894_clever_lands.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `first_name`, `last_name`, `email`, `password`, `user_type`) VALUES
(4, 'Sameer', 'Bagde', 'bagdesameer92@gmail.com', 'hello', 'user'),
(6, 'tony', 'gamers', 'email@gmail.com', 'hello', 'admin'),
(7, 'tony', 'gamers', 'olduser@email.com', 'hello', 'user'),
(12, 'Sameer', 'gamers', 'sameer.bagde.cse@ghrce.raisoni.net', 'hello', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
