<?php
// Include database connection
require_once '../../includes/dbh.inc.php';

// Check if connection is successful
if (!isset($conn) || $conn->connect_error) {
    die("Connection failed: " . (isset($conn) ? $conn->connect_error : "Connection variable not set"));
}

// Fetch featured products with debug to see actual image URL values
$featured_query = "SELECT p.*, c.category_name 
                  FROM products p
                  JOIN categories c ON p.category_id = c.category_id
                  ORDER BY p.price DESC
                  LIMIT 3";
$featured_result = $conn->query($featured_query);

// Handle newsletter subscription
$subscribeMessage = '';
if (isset($_POST['subscribe'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    // Check if email already exists - first check if the table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'subscribers'");
    if($table_check->num_rows == 0) {
        // Create subscribers table if it doesn't exist
        $conn->query("CREATE TABLE subscribers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(100) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }

    $check = $conn->prepare("SELECT id FROM subscribers WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $subscribeMessage = "You're already subscribed.";
    } else {
        $stmt = $conn->prepare("INSERT INTO subscribers (email) VALUES (?)");
        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {
            $subscribeMessage = "Thank you for subscribing!";
        } else {
            $subscribeMessage = "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    $check->close();
}

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
                        // Get product name for use in image selection
                        $product_name = strtolower($product['product_name']);

                        // Map specific product names to image files
                        $image_map = [
                            'twilight imperium' => 'twilight_imperium_logo.jpg',
                            'terraforming mars' => 'terraforming_mars_logo.jpg',
                            'catan' => 'catan_logo.png',
                            'exploding kittens' => 'exploding_kittens_logo.png',
                            'magic: the gathering starter kit' => 'magic_the_gathering_starter_kit_logo.jpg',
                            'monopoly' => 'monopoly_logo.png',
                            'pokémon trading card game elite trainer box' => 'pokemon_trading_card_game_logo.jpg',
                            'scrabble' => 'scrabble_logo.png',
                            'ticket to ride' => 'ticket_to_ride_logo.png',
                            'cards against humanity' => 'cards_against_humanity_logo.png'
                        ];

                        // Set image path based on product name
                        if (isset($image_map[$product_name])) {
                            $image_filename = $image_map[$product_name];
                        } else {
                            // Extract filename from the database if not in our mapping
                            $image_filename = basename($product['image_url'] ?? '');
                        }

                        // Create path to the image
                        $image_path = '../assets/images/products/' . $image_filename;

                        // Fallback image
                        if (empty($image_filename)) {
                            $image_path = '../assets/images/products/default.png';
                        }
                        ?>
                        <div class="product-card">
                            <div class="product-image">
                                <img src="<?= htmlspecialchars($image_path) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
                            </div>
                            <div class="product-info">
                                <h3><?= htmlspecialchars($product['product_name']) ?></h3>
                                <p class="product-category"><?= htmlspecialchars($product['category_name']) ?></p>
                                <p class="product-price">$<?= number_format($product['price'], 2) ?></p>
                                <a href="product_details.php?id=<?= $product['product_id'] ?>" class="btn btn-view">View Details</a>
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
                <form class="newsletter-form" action="" method="POST">
                    <input type="email" id="email_input" name="email" placeholder="Your email address" required>
                    <button type="submit" class="btn btn-primary" name="subscribe">Subscribe</button>
                </form>
                <?php if (!empty($subscribeMessage)) : ?>
                    <p class="subscribe-message"><?= htmlspecialchars($subscribeMessage) ?></p>
                <?php endif; ?>
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
//include "../../includes/footer.php";
?>