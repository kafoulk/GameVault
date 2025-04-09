-- Create the gamevault database if it doesn't exist
CREATE DATABASE IF NOT EXISTS gamevault;
USE gamevault;

-- Create categories table
CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(50) NOT NULL,
    description TEXT
);

-- Create products table (no image field)
CREATE TABLE products (
  product_id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  product_name VARCHAR(100) NOT NULL,
  description TEXT,
  price DECIMAL(10, 2) NOT NULL,
  FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

-- Create orders table
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_address TEXT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL
);

-- Insert sample data into categories
INSERT INTO categories (category_name, description) VALUES
    ('Board Games', 'Strategic board games for all ages'),
    ('Card Games', 'Trading and collectible card games'),
    ('Family Games', 'Games suitable for family gatherings'),
    ('Party Games', 'Fun games for parties and large groups'),
    ('Strategy Games', 'Complex strategy-based games');

-- Insert sample data into products
INSERT INTO products (category_id, product_name, description, price) VALUES
     (1, 'Catan', 'A classic strategy board game where players collect resources and build settlements', 49.99),
     (1, 'Ticket to Ride', 'Cross-country train adventure board game', 44.99),
     (2, 'Magic: The Gathering Starter Kit', 'Trading card game starter kit with two ready-to-play decks', 14.99),
     (2, 'Pokémon Trading Card Game Elite Trainer Box', 'Contains booster packs and accessories', 39.99),
     (3, 'Monopoly', 'Classic property trading board game for the whole family', 29.99),
     (3, 'Scrabble', 'Classic word-building game', 19.99),
     (4, 'Exploding Kittens', 'Highly-strategic, kitty-powered card game', 19.99),
     (4, 'Cards Against Humanity', 'Fill-in-the-blank party game for adults', 29.99),
     (5, 'Twilight Imperium', 'Epic space opera strategy game', 149.99),
     (5, 'Terraforming Mars', 'Strategic game about making Mars habitable', 69.99);

-- Insert sample data into orders
INSERT INTO orders (customer_name, customer_email, customer_address, total_amount) VALUES
   ('John Smith', 'john.smith@email.com', '123 Main St, Anytown, AN 12345', 94.98),
   ('Sarah Johnson', 'sarah.j@email.com', '456 Oak Ave, Somewhere, SW 67890', 149.99),
   ('Michael Brown', 'mbrown@email.com', '789 Pine Rd, Elsewhere, EL 13579', 39.99),
   ('Emily Davis', 'emily.davis@email.com', '321 Maple Dr, Nowhere, NW 24680', 59.98),
   ('David Wilson', 'dwilson@email.com', '654 Cedar Ln, Anywhere, AW 97531', 219.97);
