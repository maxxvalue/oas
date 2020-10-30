-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 17, 2020 at 03:14 AM
-- Server version: 10.1.44-MariaDB
-- PHP Version: 7.2.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Table structure for table `{prefix}_category`
--

CREATE TABLE `{prefix}_category` (
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `category_id` int(11) DEFAULT 0,
  `topic` varchar(150) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `{prefix}_category`
--

INSERT INTO `{prefix}_category` (`type`, `category_id`, `topic`) VALUES
('unit', 1, 'เส้น'),
('unit', 2, 'เรือน'),
('unit', 3, 'คู่'),
('unit', 4, 'เครื่อง'),
('unit', 5, 'ชุด'),
('unit', 6, 'ครั้ง'),
('unit', 7, 'กล่อง'),
('unit', 8, 'อัน'),
('unit', 9, 'ห่อ'),
('unit', 10, 'ลัง'),
('unit', 11, 'ชิ้น'),
('unit', 12, 'ไอเทม');

-- --------------------------------------------------------

--
-- Table structure for table `{prefix}_customer`
--

CREATE TABLE `{prefix}_customer` (
  `id` int(11) NOT NULL,
  `company` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `branch` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `idcard` varchar(13) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tax_id` varchar(13) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `provinceID` smallint(3) UNSIGNED NOT NULL,
  `province` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zipcode` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bank` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bank_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bank_no` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `discount` float NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Dumping data for table `{prefix}_customer`
--

INSERT INTO `{prefix}_customer` (`id`, `company`, `branch`, `name`, `idcard`, `tax_id`, `phone`, `fax`, `email`, `address`, `provinceID`, `province`, `zipcode`, `country`, `website`, `bank`, `bank_name`, `bank_no`, `discount`) VALUES
(1, 'ทดสอบ คู่ค้า', '', '', '', '', '03412345678', '', '', '123/45 อ.เมือง', 103, 'กาญจนบุรี', '71000', 'TH', '', '', '', '', '10.00'),
(2, 'ทดสอบ ลูกค้า', '', '', '', '', '03412456', '', '', '', 102, 'กรุงเทพมหานคร', '10000', 'TH', '', NULL, NULL, NULL, '0.00');

-- --------------------------------------------------------

--
-- Table structure for table `{prefix}_language`
--

CREATE TABLE `{prefix}_language` (
  `id` int(11) NOT NULL,
  `key` text COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `owner` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `js` tinyint(1) NOT NULL,
  `th` text COLLATE utf8_unicode_ci,
  `en` text COLLATE utf8_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `{prefix}_number`
--

CREATE TABLE `{prefix}_number` (
  `id` int(11) NOT NULL,
  `product_no` int(11) DEFAULT 0,
  `IN_NO` int(11) DEFAULT 0,
  `OUT_NO` int(11) DEFAULT 0,
  `PO_NO` int(11) DEFAULT 0,
  `QUO_NO` int(11) DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `{prefix}_number`
--

INSERT INTO `{prefix}_number` (`id`, `product_no`, `IN_NO`, `OUT_NO`, `PO_NO`, `QUO_NO`) VALUES
(1, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `{prefix}_orders`
--

CREATE TABLE `{prefix}_orders` (
  `id` int(11) NOT NULL,
  `order_no` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `customer_id` int(11) UNSIGNED NOT NULL,
  `order_date` date NOT NULL,
  `member_id` int(11) UNSIGNED NOT NULL,
  `discount` float NOT NULL,
  `vat` float NOT NULL,
  `tax` float NOT NULL,
  `total` float NOT NULL,
  `status` enum('IN','OUT','PO','QUO') COLLATE utf8_unicode_ci NOT NULL,
  `paid` float NOT NULL,
  `discount_percent` float NOT NULL,
  `tax_status` float NOT NULL,
  `vat_status` tinyint(1) NOT NULL,
  `order` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `payment_method` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment` text COLLATE utf8_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `{prefix}_inventory`
--

CREATE TABLE `{prefix}_inventory` (
  `id` int(11) NOT NULL,
  `topic` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `product_no` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `create_date` datetime NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `price` float NOT NULL,
  `cost` float NOT NULL,
  `vat` float NOT NULL,
  `count_stock` tinyint(1) NOT NULL DEFAULT 1,
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `unit` int(11) NOT NULL,
  `stock` float NOT NULL,
  `detail` text COLLATE utf8_unicode_ci,
  `category_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `{prefix}_inventory`
--

INSERT INTO `{prefix}_inventory` (`id`, `topic`, `product_no`, `create_date`, `unit`, `stock`, `detail`, `category_id`) VALUES
(1, 'จอมอนิเตอร์ ACER S220HQLEBD', '1108-365D', '2018-08-28 19:49:33', 1, 1, NULL, 1),
(2, 'ASUS A550JX', '0000-0001', '2018-08-28 19:49:33', 1, 1, NULL, 1),
(3, 'Crucial 4GB DDR3L&amp;1600 SODIMM', 'IF111/036/1', '2018-08-28 19:49:33', 1, 1, NULL, 3);

-- --------------------------------------------------------

--
-- Table structure for table `{prefix}_stock`
--

CREATE TABLE `{prefix}_stock` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `member_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `status` enum('IN','OUT','PO','QUO') COLLATE utf8_unicode_ci NOT NULL,
  `create_date` datetime NOT NULL,
  `topic` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `quantity` float NOT NULL,
  `used` float NOT NULL,
  `price` float NOT NULL,
  `vat` float NOT NULL,
  `discount` float NOT NULL,
  `total` float NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Dumping data for table `{prefix}_stock`
--

INSERT INTO `{prefix}_stock` (`id`, `order_id`, `member_id`, `product_id`, `status`, `create_date`, `topic`, `quantity`, `price`, `vat`, `discount`, `total`) VALUES
(1, 0, 1, 1, 'IN', '2017-01-10 21:10:41', NULL, 100, '100.00', '700.00', '0.00', '10000.00'),
(2, 0, 1, 2, 'IN', '2017-01-11 21:12:53', NULL, 50, '50.00', '175.00', '0.00', '2500.00'),
(3, 0, 1, 4, 'IN', '2016-01-01 21:18:36', NULL, 100, '10.00', '65.42', '0.00', '934.58'),
(4, 1, 1, 4, 'OUT', '2016-02-08 00:00:00', 'Cake A', 10, '30.00', '19.63', '0.00', '300.00'),
(5, 2, 1, 4, 'OUT', '2017-09-09 00:00:00', 'Cake A', 10, '30.00', '19.63', '0.00', '300.00'),
(6, 3, 11, 1, 'OUT', '2017-09-17 00:00:00', 'iPhone 7SE case iPhone 7SE เคสสีขาวใส มีลายกาตูน', 1, '500.00', '0.00', '0.00', '500.00'),
(7, 3, 11, 3, 'OUT', '2017-09-17 00:00:00', 'บริการติดตั้ง ติดฟิลม์ และเช็คความสะอาดเครื่อง', 1, '500.00', '0.00', '0.00', '500.00'),
(8, 4, 1, 1, 'OUT', '2017-02-03 00:00:00', 'iPhone 7SE case iPhone 7SE เคสสีขาวใส มีลายกาตูน', 25, '500.00', '875.00', '0.00', '12500.00');

-- --------------------------------------------------------

--
-- Table structure for table `{prefix}_user`
--

CREATE TABLE `{prefix}_user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `salt` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0,
  `permission` text COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `sex` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_card` varchar(13) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `provinceID` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `province` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zipcode` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `visited` int(11) DEFAULT 0,
  `lastvisited` int(11) DEFAULT 0,
  `session_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ip` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `social` tinyint(1) DEFAULT 0,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Indexes for table `{prefix}_category`
--
ALTER TABLE `{prefix}_category`
  ADD KEY `type` (`type`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `{prefix}_customer`
--
ALTER TABLE `{prefix}_customer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `{prefix}_language`
--
ALTER TABLE `{prefix}_language`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `{prefix}_number`
--
ALTER TABLE `{prefix}_number`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `{prefix}_orders`
--
ALTER TABLE `{prefix}_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `{prefix}_inventory`
--
ALTER TABLE `{prefix}_inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `product_no` (`product_no`);

--
-- Indexes for table `{prefix}_stock`
--
ALTER TABLE `{prefix}_stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`order_id`) USING BTREE;

--
-- Indexes for table `{prefix}_user`
--
ALTER TABLE `{prefix}_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`),
  ADD KEY `id_card` (`id_card`);

--
-- AUTO_INCREMENT for table `{prefix}_customer`
--
ALTER TABLE `{prefix}_customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `{prefix}_language`
--
ALTER TABLE `{prefix}_language`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `{prefix}_orders`
--
ALTER TABLE `{prefix}_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `{prefix}_inventory`
--
ALTER TABLE `{prefix}_inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `{prefix}_stock`
--
ALTER TABLE `{prefix}_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `{prefix}_user`
--
ALTER TABLE `{prefix}_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
