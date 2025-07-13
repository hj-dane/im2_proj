-- phpMyAdmin SQL Dump
-- version 5.2.1deb1+deb12u1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 10, 2025 at 01:44 PM
-- Server version: 10.11.11-MariaDB-0+deb12u1
-- PHP Version: 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `s11820346_im2`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `category_name` varchar(255) DEFAULT NULL,
  `category_description` varchar(255) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `category_name`, `category_description`, `product_id`) VALUES
(1, 'Clothing', 'This includes jerseys, shorts/bibshorts, shirts and windbreakers.', NULL),
(2, 'Accessories', 'This includes socks, caps, gloves and pouches.', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(250) DEFAULT NULL,
  `contact_number` int(11) DEFAULT NULL,
  `date_registered` date DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_type`
--

CREATE TABLE `delivery_type` (
  `id` int(11) NOT NULL,
  `delivery_type` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `delivery_type`
--

INSERT INTO `delivery_type` (`id`, `delivery_type`) VALUES
(1, 'Self-Pick up'),
(2, 'Self-Arranged');

-- --------------------------------------------------------

--
-- Table structure for table `payment_method`
--

CREATE TABLE `payment_method` (
  `id` int(11) NOT NULL,
  `payment_method` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_method`
--

INSERT INTO `payment_method` (`id`, `payment_method`) VALUES
(1, 'Cash'),
(2, 'Gcash'),
(3, 'Online Transfer');

-- --------------------------------------------------------

--
-- Table structure for table `product_inventory`
--

CREATE TABLE `product_inventory` (
  `id` int(11) NOT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `size` varchar(255) DEFAULT NULL,
  `unit_price` float DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `product_description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_inventory`
--

INSERT INTO `product_inventory` (`id`, `product_name`, `is_active`, `color`, `size`, `unit_price`, `category_id`, `quantity`, `product_description`) VALUES
(51, 'Cycling Jersey', NULL, 'black', 'small', 850, 1, 45, 'High-performance jersey crafted with breathable mesh and quick-dry fabric'),
(52, 'Cycling Jersey', NULL, 'white', 'small', 850, 1, 40, 'High-performance jersey crafted with breathable mesh and quick-dry fabric'),
(53, 'Cycling Jersey', NULL, 'black', 'medium', 850, 1, 35, 'High-performance jersey crafted with breathable mesh and quick-dry fabric'),
(54, 'Cycling Jersey', NULL, 'white', 'medium', 850, 1, 50, 'High-performance jersey crafted with breathable mesh and quick-dry fabric'),
(55, 'Cycling Jersey', NULL, 'black', 'large', 850, 1, 49, 'High-performance jersey crafted with breathable mesh and quick-dry fabric'),
(56, 'Cycling Jersey', NULL, 'white', 'large', 850, 1, 50, 'High-performance jersey crafted with breathable mesh and quick-dry fabric'),
(57, 'Cycling Bibshorts', NULL, 'black', 'small', 1499.5, 1, 52, 'Durable bibshorts made for endurance cycling with firm muscle support.'),
(58, 'Cycling Bibshorts', NULL, 'white', 'small', 1499.5, 1, 53, 'Durable bibshorts made for endurance cycling with firm muscle support.'),
(59, 'Cycling Bibshorts', NULL, 'black', 'medium', 1499.5, 1, 55, 'Durable bibshorts made for endurance cycling with firm muscle support.'),
(60, 'Cycling Bibshorts', NULL, 'white', 'medium', 1499.5, 1, 57, 'Durable bibshorts made for endurance cycling with firm muscle support.'),
(61, 'Cycling Bibshorts', NULL, 'black', 'large', 1499.5, 1, 59, 'Durable bibshorts made for endurance cycling with firm muscle support.'),
(62, 'Cycling Bibshorts', NULL, 'white', 'large', 1499.5, 1, 61, 'Durable bibshorts made for endurance cycling with firm muscle support.'),
(63, 'Cycling Shorts', NULL, 'black', 'small', 800, 1, 45, 'All-around cycling shorts with soft padding and snug elastic waistband.'),
(64, 'Cycling Shorts', NULL, 'white', 'small', 800, 1, 40, 'All-around cycling shorts with soft padding and snug elastic waistband.'),
(65, 'Cycling Shorts', NULL, 'black', 'medium', 800, 1, 35, 'All-around cycling shorts with soft padding and snug elastic waistband.'),
(66, 'Cycling Shorts', NULL, 'white', 'medium', 800, 1, 53, 'All-around cycling shorts with soft padding and snug elastic waistband.'),
(67, 'Cycling Shorts', NULL, 'black', 'large', 800, 1, 55, 'All-around cycling shorts with soft padding and snug elastic waistband.'),
(68, 'Cycling Shorts', NULL, 'white', 'large', 800, 1, 57, 'All-around cycling shorts with soft padding and snug elastic waistband.'),
(69, 'Cycling Gloves', NULL, 'black', 'small', 300, 2, 20, 'Half-finger gloves with gel-padded palms for shock absorption.'),
(70, 'Cycling Gloves', NULL, 'white', 'small', 300, 2, 25, 'Half-finger gloves with gel-padded palms for shock absorption.'),
(71, 'Cycling Gloves', NULL, 'black', 'medium', 300, 2, 20, 'Half-finger gloves with gel-padded palms for shock absorption.'),
(72, 'Cycling Gloves', NULL, 'white', 'medium', 300, 2, 22, 'Half-finger gloves with gel-padded palms for shock absorption.'),
(73, 'Cycling Gloves', NULL, 'black', 'large', 300, 2, 26, 'Half-finger gloves with gel-padded palms for shock absorption.'),
(74, 'Cycling Gloves', NULL, 'white', 'large', 300, 2, 28, 'Half-finger gloves with gel-padded palms for shock absorption.'),
(75, 'Socks', NULL, 'black', NULL, 150, 2, 30, 'Stretch-fit performance socks with reinforced toe and heel padding.'),
(76, 'Socks', NULL, 'white', NULL, 150, 2, 35, 'Stretch-fit performance socks with reinforced toe and heel padding.'),
(77, 'Cycling Cap', NULL, 'black', NULL, 250, 2, 25, 'Lightweight cap with visor and breathable fabric, fits under helmet.'),
(78, 'Cycling Cap', NULL, 'white', NULL, 250, 2, 20, 'Lightweight cap with visor and breathable fabric, fits under helmet.'),
(79, 'Trucker Cap', NULL, 'black', NULL, 300, 2, 22, 'Casual trucker cap with curved brim and moisture-wicking inner band.'),
(80, 'Trucker Cap', NULL, 'white', NULL, 300, 2, 26, 'Casual trucker cap with curved brim and moisture-wicking inner band.'),
(81, 'Polo Shirt with Pockets', NULL, 'black', 'small', 900, 1, 52, 'Quick-dry polo with contrast collar and button placket.'),
(82, 'Polo Shirt with Pockets', NULL, 'white', 'small', 900, 1, 53, 'Quick-dry polo with contrast collar and button placket.'),
(83, 'Polo Shirt with Pockets', NULL, 'black', 'medium', 900, 1, 35, 'Quick-dry polo with contrast collar and button placket.'),
(84, 'Polo Shirt with Pockets', NULL, 'white', 'medium', 900, 1, 57, 'Quick-dry polo with contrast collar and button placket.'),
(85, 'Polo Shirt with Pockets', NULL, 'black', 'large', 900, 1, 59, 'Quick-dry polo with contrast collar and button placket.'),
(86, 'Polo Shirt with Pockets', NULL, 'white', 'large', 900, 1, 48, 'Quick-dry polo with contrast collar and button placket.'),
(87, 'Drifit Shirt', NULL, 'black', 'small', 450, 1, 53, 'Training-fit dri-fit shirt with ergonomic seams and comfort fit.'),
(88, 'Drifit Shirt', NULL, 'white', 'small', 450, 1, 54, 'Training-fit dri-fit shirt with ergonomic seams and comfort fit.'),
(89, 'Drifit Shirt', NULL, 'black', 'medium', 450, 1, 54, 'Training-fit dri-fit shirt with ergonomic seams and comfort fit.'),
(90, 'Drifit Shirt', NULL, 'white', 'medium', 450, 1, 55, 'Training-fit dri-fit shirt with ergonomic seams and comfort fit.'),
(91, 'Drifit Shirt', NULL, 'black', 'large', 450, 1, 55, 'Training-fit dri-fit shirt with ergonomic seams and comfort fit.'),
(92, 'Drifit Shirt', NULL, 'white', 'large', 450, 1, 56, 'Training-fit dri-fit shirt with ergonomic seams and comfort fit.'),
(93, 'Windbreaker', NULL, 'black', 'small', 1299, 1, 31, 'Windproof jacket with ventilated panels and elastic cuffs.'),
(94, 'Windbreaker', NULL, 'white', 'small', 1299, 1, 25, 'Windproof jacket with ventilated panels and elastic cuffs.'),
(95, 'Windbreaker', NULL, 'black', 'medium', 1299, 1, 30, 'Windproof jacket with ventilated panels and elastic cuffs.'),
(96, 'Windbreaker', NULL, 'white', 'medium', 1299, 1, 22, 'Windproof jacket with ventilated panels and elastic cuffs.'),
(97, 'Windbreaker', NULL, 'black', 'large', 1299, 1, 26, 'Windproof jacket with ventilated panels and elastic cuffs.'),
(98, 'Windbreaker', NULL, 'white', 'large', 1299, 1, 28, 'Windproof jacket with ventilated panels and elastic cuffs.'),
(99, 'Cycling Pouch', NULL, 'black', NULL, 150, 2, 48, 'Compact cycling pouch for tools, snacks, or phone with adjustable strap.'),
(100, 'Cycling Pouch', NULL, 'white', NULL, 150, 2, 45, 'Compact cycling pouch for tools, snacks, or phone with adjustable strap.');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `comment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trans_details`
--

CREATE TABLE `trans_details` (
  `id` int(11) NOT NULL,
  `trans_header_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `qty_out` int(11) DEFAULT NULL,
  `qty_in` int(11) DEFAULT NULL,
  `price` float DEFAULT NULL,
  `amount` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trans_header`
--

CREATE TABLE `trans_header` (
  `id` int(11) NOT NULL,
  `trans_date` date DEFAULT NULL,
  `trans_type_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `total_order_amount` float DEFAULT NULL,
  `amount_paid` float DEFAULT NULL,
  `payment_method_id` int(11) DEFAULT NULL,
  `delivery_type_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trans_type`
--

CREATE TABLE `trans_type` (
  `id` int(11) NOT NULL,
  `trans_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_login`
--

CREATE TABLE `user_login` (
  `id` int(11) NOT NULL,
  `user_name` varchar(250) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `date_registered` date DEFAULT NULL,
  `user_type_id` int(11) DEFAULT NULL,
  `locked_until` date DEFAULT NULL,
  `is_locked` tinyint(1) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `login_attempts` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_type`
--

CREATE TABLE `user_type` (
  `id` int(11) NOT NULL,
  `user_type` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_type`
--

INSERT INTO `user_type` (`id`, `user_type`) VALUES
(1, 'Customer'),
(2, 'Admin/Seller');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_category_product_inventory` (`product_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `delivery_type`
--
ALTER TABLE `delivery_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_method`
--
ALTER TABLE `payment_method`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_inventory`
--
ALTER TABLE `product_inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trans_details`
--
ALTER TABLE `trans_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trans_header_id` (`trans_header_id`),
  ADD KEY `product_inventory_id` (`product_id`);

--
-- Indexes for table `trans_header`
--
ALTER TABLE `trans_header`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trans_type` (`trans_type_id`),
  ADD KEY `payment_method` (`payment_method_id`),
  ADD KEY `trans_customer_id` (`customer_id`),
  ADD KEY `delivery_type` (`delivery_type_id`);

--
-- Indexes for table `trans_type`
--
ALTER TABLE `trans_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_login`
--
ALTER TABLE `user_login`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_type` (`user_type_id`);

--
-- Indexes for table `user_type`
--
ALTER TABLE `user_type`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery_type`
--
ALTER TABLE `delivery_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payment_method`
--
ALTER TABLE `payment_method`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `product_inventory`
--
ALTER TABLE `product_inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trans_details`
--
ALTER TABLE `trans_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trans_header`
--
ALTER TABLE `trans_header`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trans_type`
--
ALTER TABLE `trans_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_type`
--
ALTER TABLE `user_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `category`
--
ALTER TABLE `category`
  ADD CONSTRAINT `fk_category_product_inventory` FOREIGN KEY (`product_id`) REFERENCES `product_inventory` (`id`);

--
-- Constraints for table `customer`
--
ALTER TABLE `customer`
  ADD CONSTRAINT `customer_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_login` (`id`);

--
-- Constraints for table `product_inventory`
--
ALTER TABLE `product_inventory`
  ADD CONSTRAINT `category_id` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews` FOREIGN KEY (`id`) REFERENCES `trans_details` (`id`);

--
-- Constraints for table `trans_details`
--
ALTER TABLE `trans_details`
  ADD CONSTRAINT `product_inventory_id` FOREIGN KEY (`product_id`) REFERENCES `product_inventory` (`id`),
  ADD CONSTRAINT `trans_header_id` FOREIGN KEY (`trans_header_id`) REFERENCES `trans_header` (`id`);

--
-- Constraints for table `trans_header`
--
ALTER TABLE `trans_header`
  ADD CONSTRAINT `delivery_type` FOREIGN KEY (`delivery_type_id`) REFERENCES `delivery_type` (`id`),
  ADD CONSTRAINT `payment_method` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_method` (`id`),
  ADD CONSTRAINT `trans_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`),
  ADD CONSTRAINT `trans_type` FOREIGN KEY (`trans_type_id`) REFERENCES `trans_type` (`id`);

--
-- Constraints for table `user_login`
--
ALTER TABLE `user_login`
  ADD CONSTRAINT `user_type` FOREIGN KEY (`user_type_id`) REFERENCES `user_type` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
