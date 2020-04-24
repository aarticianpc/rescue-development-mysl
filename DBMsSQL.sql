-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 16, 2019 at 03:48 PM
-- Server version: 5.7.20
-- PHP Version: 7.1.25

/* SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO"; */
-- SET @AUTOCOMMIT = 0;
-- START TRANSACTION;
-- SET @time_zone = "+00:00";

--
-- Database: `rescue_development`
--
CREATE DATABASE [rescue_development];
USE [rescue_development];

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

DROP TABLE IF EXISTS rescue_development.[packages];
CREATE TABLE rescue_development.[packages] ( [id] int IDENTITY(1,1) PRIMARY KEY, [name] varchar(255) DEFAULT NULL, [created_at] datetime2(0) NULL DEFAULT GETDATE());


-- --------------------------------------------------------

--
-- Table structure for table `package_items`
--

DROP TABLE IF EXISTS rescue_development.[package_items];
CREATE TABLE rescue_development.[package_items] ( [id] int IDENTITY (1,1) PRIMARY KEY, [package_id] int NOT NULL, [item] varchar(max), [created_at] datetime2(0) NULL DEFAULT GETDATE());


ALTER TABLE rescue_development.[package_items] ADD CONSTRAINT package_foreign_package_item_package_id FOREIGN KEY (package_id) REFERENCES rescue_development.packages(id);
