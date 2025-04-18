<?php
// Include database connection
require_once '../../includes/dbh.inc2.php';


// Check if connection is successful
if (!isset($mysqli) || $mysqli->connect_error) {
    die("Connection failed: " . (isset($mysqli) ? $mysqli->connect_error : "Connection variable not set"));
}

// Fetch featured products
$featured_query = "SELECT p.*, c.category_name 
                  FROM products p
                  JOIN categories c ON p.category_id = c.category_id
                  ORDER BY p.price DESC
                  LIMIT 5";
$featured_result = $mysqli->query($featured_query);

// Fetch categories
$categories_query = "SELECT * FROM categories LIMIT 5";
$categories_result = $mysqli->query($categories_query);

// Include header
include '../../includes/header.php';
?>
    <style>
        /* Product grid styling */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }

        .product-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-image {
            height: 200px;
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-info {
            padding: 15px;
        }

        .product-info h3 {
            margin-top: 0;
            font-size: 18px;
        }

        .product-category {
            color: #666;
            font-size: 14px;
        }

        .product-price {
            font-weight: bold;
            color: #e63946;
            margin: 10px 0;
        }

        .btn-view {
            display: inline-block;
            background-color: #457b9d;
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-view:hover {
            background-color: #1d3557;
        }
    </style>
    <main>
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <h1>Welcome to GameVault</h1>
                <p>Your ultimate destination for board games, card games, and collectibles</p>
                <div class="hero-buttons">
                    <a href="products.php" class="btn btn-primary">Browse Collection</a>
                    <a href="about.php" class="btn btn-secondary">About Us</a>
                </div>
            </div>
        </section>

        <!-- Featured Products Section -->

        <section class="featured-products">
            <h2>Featured Products</h2>
            <div class="product-grid">
                <?php
                // Check if there are products
                if ($featured_result && $featured_result->num_rows > 0) {
                    // Output data of each row
                    while ($product = $featured_result->fetch_assoc()) {
                        ?>
                        <div class="product-card">
                            <div class="product-image">
                                <?php if (!empty($product['image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                                <?php else: ?>
                                    <img src="../../public/assets/images/GameVault.png" alt="Product image placeholder">
                                <?php endif; ?>
                            </div>
                            <div class="product-info">
                                <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                                <p class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                                <p class="product-price">$<?php echo number_format($product['price'], 2); ?></p>
                                <a href="product_details.php?id=<?php echo $product['product_id']; ?>" class="btn btn-view">View Details</a>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p>No featured products available at this time.</p>";
                }
                ?>
            </div>
        </section>

        <!-- Categories Showcase -->
        <section class="categories-showcase">
            <h2>Explore Categories</h2>
            <div class="categories-grid">
                <?php
                // Check if there are categories
                if ($categories_result && $categories_result->num_rows > 0) {
                    while ($category = $categories_result->fetch_assoc()) {
                        ?>
                        <a href="products.php?category=<?php echo $category['category_id']; ?>" class="category-card">
                            <h3><?php echo htmlspecialchars($category['category_name']); ?></h3>
                            <p><?php echo htmlspecialchars($category['description']); ?></p>
                        </a>
                        <?php
                    }
                } else {
                    echo "<p>No categories available at this time.</p>";
                }
                ?>
            </div>
        </section>

        <!-- Promotional Banner -->
        <section class="promo-banner">
            <div class="promo-content">
                <h2>New Arrivals Every Week!</h2>
                <p>Sign up for our newsletter to get updates on the latest games and exclusive offers.</p>
                <form class="newsletter-form" action="subscribe.php" method="post">
                    <input type="email" name="email" placeholder="Your email address" required>
                    <button type="submit" class="btn btn-primary">Subscribe</button>
                </form>
            </div>
        </section>
    </main>

<?php
// Free result set
if (isset($featured_result)) $featured_result->free_result();
if (isset($categories_result)) $categories_result->free_result();

// Close connection
if (isset($mysqli)) $mysqli->close();

// Include footer
include "../../includes/footer.php";
?>