CREATE DATABASE IF NOT EXISTS `e-commerce project`;
USE `e-commerce project`;

CREATE TABLE `admins` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL
);

CREATE TABLE `admin_requests` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL UNIQUE,
  `password` varchar(255) DEFAULT NULL
);

CREATE TABLE `cart` (
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `color` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`product_id`, `user_id`, `color`),
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`)
);

CREATE TABLE `categories` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `categorie_name` varchar(255) DEFAULT NULL UNIQUE,
  `image` varchar(255) DEFAULT NULL
);

CREATE TABLE `colors` (
  `product_id` int(11) NOT NULL,
  `color` varchar(255) NOT NULL,
  PRIMARY KEY (`product_id`, `color`),
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`)
);

CREATE TABLE `products` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `product_name` varchar(128) NOT NULL,
  `product_desc` text NOT NULL,
  `product_img` varchar(255) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `categorie` varchar(255) DEFAULT NULL
);

CREATE TABLE `users` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `email` varchar(255) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL
);
