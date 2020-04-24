-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 16, 2019 at 03:48 PM
-- Server version: 5.7.20
-- PHP Version: 7.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `rescue_development`
--
CREATE DATABASE IF NOT EXISTS `rescue_development` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `rescue_development`;

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

DROP TABLE IF EXISTS `packages`;
CREATE TABLE `packages` (
  `id` int(20) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `package_items`
--

DROP TABLE IF EXISTS `package_items`;
CREATE TABLE `package_items` (
  `id` int(20) NOT NULL,
  `package_id` int(20) NOT NULL,
  `item` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `package_items`
--
ALTER TABLE `package_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `package_foreign_package_item_package_id` (`package_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `package_items`
--
ALTER TABLE `package_items`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `package_items`
--
ALTER TABLE `package_items`
  ADD CONSTRAINT `package_foreign_package_item_package_id` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;
