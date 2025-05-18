-- Create database
CREATE DATABASE IF NOT EXISTS shop_oto_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE shop_oto_db;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user'
);

-- Insert fixed admin account
INSERT INTO users (name, email, password, role) VALUES
('Chung@admin', 'chung@admin.com', SHA2('060906', 256), 'admin');

-- Create products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    price DECIMAL(18,2) NOT NULL,
    description TEXT NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    discount_percent DECIMAL(5,2) NULL DEFAULT NULL,
    discount_fixed DECIMAL(10,2) NULL DEFAULT NULL,
    increase_percent DECIMAL(5,2) NULL DEFAULT NULL,
    increase_fixed DECIMAL(10,2) NULL DEFAULT NULL,
    brand VARCHAR(255) NOT NULL DEFAULT ''
);

-- Create product_images table for multiple images per product
CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);
