<?php
// Include database connection
require_once '../../includes/dbh.inc2.php';





// Check if connection is successful
if (!isset($conn) || $conn->connect_error) {
    die("Connection failed: " . (isset($conn) ? $conn->connect_error : "Connection variable not set"));
}

// Fetch featured products
$featured_query = "SELECT p.*, c.category_name 
                  FROM products p
                  JOIN categories c ON p.category_id = c.category_id
                  ORDER BY p.price DESC
                  LIMIT 3";
$featured_result = $conn->query($featured_query);


// Include header
include '../../includes/header.php';
?>


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
                                    <?php
                                    // Fix for incorrect image paths
                                    $image_path = str_replace('../public/assets/images/products/', '../assets/images/products/', $product['image_url']);
                                    ?>
                                    <img src="<?php echo htmlspecialchars($image_path); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                                <?php else: ?>
                                    <img src="../assets/images/products/catan_logo.png" alt="Product image placeholder">
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
if (isset($conn)) $conn->close();

// Include footer
include "../../includes/footer.php";
?>