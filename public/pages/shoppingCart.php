<?php
session_start();
include "../../includes/header.php";
require_once '../src/php/cart.php';
require_once '../../includes/dbh.inc.php';
$total = calculate_cart_total();
?>
<body>
<main>
    <div class="container my-5">
        <h2 class="mb-4">Your Shopping Cart</h2>
        <?php if (!empty($_SESSION['cart'])): ?>
            <table class="table table-bordered">
                <thead class="table-light">
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $total_price = 0;
                foreach ($_SESSION['cart'] as $product_id => $item):
                    $item_total = $item['price'] * $item['quantity'];
                    $total_price += $item_total;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td>$<?= number_format($item['price'], 2) ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <a href="../src/php/cart.php?action=decrease&id=<?= $product_id ?>" class="btn btn-sm btn-outline-secondary me-1">−</a>
                                <span><?= $item['quantity'] ?></span>
                                <a href="../src/php/cart.php?action=increase&id=<?= $product_id ?>" class="btn btn-sm btn-outline-secondary ms-1">+</a>
                            </div>
                        </td>
                        <td>$<?= number_format($item_total, 2) ?></td>
                        <td>
                            <form method="post" action="../src/php/cart.php" style="display:inline;">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="product_id" value="<?= $product_id ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                    <td colspan="2"><strong>$<?= number_format($total_price, 2) ?></strong></td>
                </tr>
                </tbody>
            </table>
            <div class="d-flex justify-content-between">
                <a href="products.php" class="btn btn-secondary">Continue Shopping</a>
                <a href="checkout.php" class="btn btn-primary">Checkout</a>
            </div>
        <?php else: ?>
            <div class="text-center mb-4">
                <p>Your cart is empty. <a href="products.php">Browse products</a>.</p>
            </div>

            <?php
            // Include the carousel with fixed image paths
            // This is assuming we need to modify carousel.php as well
            ?>
            <div id="recommendedProducts" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php
                    // Get recommended products from the database
                    try {
                        $stmt = $conn->prepare("SELECT product_id, product_name, price, image_url FROM products ORDER BY RAND() LIMIT 3");
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $products = $result->fetch_all(MYSQLI_ASSOC);

                        // Check if there are products
                        if (!empty($products)) {
                            foreach ($products as $index => $product) {
                                // Get product name for image mapping
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

                                // Determine if this is the active slide
                                $active_class = ($index === 0) ? 'active' : '';
                                ?>
                                <div class="carousel-item <?= $active_class ?>">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div class="card" style="width: 18rem;">
                                            <img src="<?= htmlspecialchars($image_path) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['product_name']) ?>" style="height: 200px; object-fit: contain;">
                                            <div class="card-body text-center">
                                                <h5 class="card-title"><?= htmlspecialchars($product['product_name']) ?></h5>
                                                <p class="card-text">$<?= number_format($product['price'], 2) ?></p>
                                                <a href="product_details.php?id=<?= $product['product_id'] ?>" class="btn btn-primary">View Details</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            ?>
                            <div class="carousel-item active">
                                <div class="d-flex justify-content-center align-items-center">
                                    <p>No recommended products available.</p>
                                </div>
                            </div>
                            <?php
                        }
                    } catch (mysqli_sql_exception $e) {
                        echo "<p>Error loading recommended products: " . htmlspecialchars($e->getMessage()) . "</p>";
                    }
                    ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#recommendedProducts" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#recommendedProducts" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php include "../../includes/footer.php"; ?>
<script src="../../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>