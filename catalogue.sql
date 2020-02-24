-- SET UP THE TABLES NEEDED FOR ACME WIDGET POC 
-- initialise with data provided in the initial briefing


-- CREATE THE TABLES FOR PRODUCTS, OFFERS AND DELIVERY DISCOUNTS

-- Table for products

CREATE TABLE `products` (
  `id` int(8) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `image` text NOT NULL,
  `price` double(10,2) NOT NULL
);

-- Table for offers 

CREATE TABLE `offers` (
  `id` int(8) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `number-of-products` double(10,2) NOT NULL,
  `discount` double(10,2) NOT NULL
);

-- Table for deliver discounts

CREATE TABLE `delivery` (
  `id` int(8) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `total-amount` double(10,2) NOT NULL,
  `delivery-amount` double(10,2) NOT NULL
);


-- POPULATE THE CATALOGUE

-- Add the initial products

INSERT INTO `products` (`name`, `code`, `image`, `price`) VALUES
('Red Widget',   'R01', 'images/red-widget.jpeg',   32.95),
('Green Widget', 'G01', 'images/green-widget.jpeg', 24.95),
('Blue Widget',  'B01', 'images/blue-widget.jpeg',  7.95);

-- Add the initial offer on Red Widgets

INSERT INTO `offers` (`description`, `code`, `number-of-products`, `discount`) VALUES
('Buy one, get the second half price on Red Widgets', 'R01', 2, 0.5);

-- Add the delivery discount structure

INSERT INTO `delivery` (`total-amount`, `delivery-amount`) VALUES
(50, 4.95),
(90, 2.95)

