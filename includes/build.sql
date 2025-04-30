-- Create the gamevault database if it doesn't exist
CREATE DATABASE IF NOT EXISTS gamevault;
USE gamevault;

-- Create users table for secure user information
CREATE TABLE users (
user_id INT AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(50) NOT NULL UNIQUE,
email VARCHAR(100) NOT NULL UNIQUE,
password_hash VARCHAR(255) NOT NULL, -- Store hashed passwords, never plaintext
first_name VARCHAR(50),
last_name VARCHAR(50),
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
last_login TIMESTAMP NULL
);

-- Create categories table
CREATE TABLE categories (
category_id INT AUTO_INCREMENT PRIMARY KEY,
category_name VARCHAR(50) NOT NULL,
description TEXT,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create products table with image field
CREATE TABLE products (
product_id INT AUTO_INCREMENT PRIMARY KEY,
category_id INT NOT NULL,
product_name VARCHAR(100) NOT NULL,
description TEXT,
price DECIMAL(10, 2) NOT NULL,
stock_quantity INT NOT NULL DEFAULT 0,
image_url VARCHAR(255), -- Store path to image
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

-- Create orders table with tracking information
CREATE TABLE orders (
order_id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT,
customer_name VARCHAR(100) NOT NULL,
customer_email VARCHAR(100) NOT NULL,
customer_address TEXT NOT NULL,
total_amount DECIMAL(10, 2) NOT NULL,
order_status ENUM('Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Pending',
tracking_number VARCHAR(100),
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Create order_items table to handle multiple products per order and quantities
CREATE TABLE order_items (
order_item_id INT AUTO_INCREMENT PRIMARY KEY,
order_id INT NOT NULL,
product_id INT NOT NULL,
quantity INT NOT NULL DEFAULT 1,
price_per_unit DECIMAL(10, 2) NOT NULL,
subtotal DECIMAL(10, 2) NOT NULL,
FOREIGN KEY (order_id) REFERENCES orders(order_id),
FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- Create transactions table to track payment information
CREATE TABLE transactions (
transaction_id INT AUTO_INCREMENT PRIMARY KEY,
order_id INT NOT NULL,
amount DECIMAL(10, 2) NOT NULL,
payment_method ENUM('Credit Card', 'PayPal', 'Bank Transfer', 'Other') NOT NULL,
transaction_status ENUM('Pending', 'Completed', 'Failed', 'Refunded') DEFAULT 'Pending',
transaction_reference VARCHAR(100),
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (order_id) REFERENCES orders(order_id)
);

-- Insert sample data into categories
INSERT INTO categories (category_name, description) VALUES
('Board Games', 'Strategic board games for all ages'),
('Card Games', 'Trading and collectible card games'),
('Family Games', 'Games suitable for family gatherings'),
('Party Games', 'Fun games for parties and large groups'),
('Strategy Games', 'Complex strategy-based games');

-- Insert sample data into products with corrected image paths
INSERT INTO products (category_id, product_name, description, price, stock_quantity, image_url) VALUES
(4, 'Cards Against Humanity', 'Fill-in-the-blank party game for adults', 29.99, 12, '../public/assets/images/products/cards_against_humanity_logo.png'),
(1, 'Catan', 'A classic strategy board game where players collect resources and build settlements', 49.99, 25, '../public/assets/images/products/catan_logo.png'),
(4, 'Exploding Kittens', 'Highly-strategic, kitty-powered card game', 19.99, 28, '../public/assets/images/products/exploding_kittens_logo.png'),
(2, 'Magic: The Gathering Starter Kit', 'Trading card game starter kit with two ready-to-play decks', 14.99, 30, '../public/assets/images/products/magic_the_gathering_starter_kit_logo.jpg'),
(3, 'Monopoly', 'Classic property trading board game for the whole family', 29.99, 22, '../public/assets/images/products/monopoly_game.jpg'),
(3, 'Monopoly', 'Classic property trading board game for the whole family', 29.99, 22, '../public/assets/images/products/monopoly_logo.png'),
(2, 'Pokémon Trading Card Game Elite Trainer Box', 'Contains booster packs and accessories', 39.99, 15, '../public/assets/images/products/pokemon_trading_card_game_logo.jpg'),
(3, 'Scrabble', 'Classic word-building game', 19.99, 20, '../public/assets/images/products/scrabble_logo.png'),
(5, 'Terraforming Mars', 'Strategic game about making Mars habitable', 69.99, 10, '../public/assets/images/products/terraforming_mars_logo.jpg'),
(1, 'Ticket to Ride', 'Cross-country train adventure board game', 44.99, 18, '../public/assets/images/products/ticket_to_ride_logo.png'),
(5, 'Twilight Imperium', 'Epic space opera strategy game', 149.99, 8, '../public/assets/images/products/twilight_imperium_logo.png');
-- Insert sample user data with hashed passwords (in a real application, you would hash passwords properly)
INSERT INTO users (username, email, password_hash, first_name, last_name) VALUES
('jsmith', 'john.smith@email.com', 'hashed_password_placeholder', 'John', 'Smith'),
('sjohnson', 'sarah.j@email.com', 'hashed_password_placeholder', 'Sarah', 'Johnson'),
('mbrown', 'mbrown@email.com', 'hashed_password_placeholder', 'Michael', 'Brown'),
('edavis', 'emily.davis@email.com', 'hashed_password_placeholder', 'Emily', 'Davis'),
('dwilson', 'dwilson@email.com', 'hashed_password_placeholder', 'David', 'Wilson');

-- Insert sample orders data
INSERT INTO orders (user_id, customer_name, customer_email, customer_address, total_amount, order_status, tracking_number) VALUES
(1, 'John Smith', 'john.smith@email.com', '123 Main St, Anytown, AN 12345', 94.98, 'Delivered', 'TRK123456'),
(2, 'Sarah Johnson', 'sarah.j@email.com', '456 Oak Ave, Somewhere, SW 67890', 149.99, 'Shipped', 'TRK789012'),
(3, 'Michael Brown', 'mbrown@email.com', '789 Pine Rd, Elsewhere, EL 13579', 39.99, 'Processing', NULL),
(4, 'Emily Davis', 'emily.davis@email.com', '321 Maple Dr, Nowhere, NW 24680', 59.98, 'Pending', NULL),
(5, 'David Wilson', 'dwilson@email.com', '654 Cedar Ln, Anywhere, AW 97531', 219.97, 'Processing', NULL);

-- Insert sample order items data
INSERT INTO order_items (order_id, product_id, quantity, price_per_unit, subtotal) VALUES
(1, 1, 1, 49.99, 49.99),
(1, 3, 3, 14.99, 44.97),
(2, 9, 1, 149.99, 149.99),
(3, 4, 1, 39.99, 39.99),
(4, 6, 1, 19.99, 19.99),
(4, 7, 2, 19.99, 39.98),
(5, 5, 1, 29.99, 29.99),
(5, 9, 1, 149.99, 149.99),
(5, 8, 1, 29.99, 39.99);

-- Insert sample transaction data
INSERT INTO transactions (order_id, amount, payment_method, transaction_status, transaction_reference) VALUES
(1, 94.98, 'Credit Card', 'Completed', 'TXN123456'),
(2, 149.99, 'PayPal', 'Completed', 'TXN789012'),
(3, 39.99, 'Credit Card', 'Completed', 'TXN345678'),
(4, 59.98, 'Bank Transfer', 'Pending', 'TXN901234'),
(5, 219.97, 'Credit Card', 'Completed', 'TXN567890');