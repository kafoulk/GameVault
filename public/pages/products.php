<?php
// Dynamically load header
include "../../includes/header.php";

// Connect to database
require_once '../../includes/dbh.inc.php';

// Check database connection
if (!isset($conn) || $conn->connect_error) {
    die("Connection failed: " . (isset($conn) ? $conn->connect_error : "Connection variable not set"));
}

// Fetch products from the database, joining categories to get category name
try {
    $stmt = $conn->prepare("SELECT p.product_id, p.product_name, p.description, p.price, p.stock_quantity, p.image_url, c.category_name 
                            FROM products p
                            LEFT JOIN categories c ON p.category_id = c.category_id"); // Join products with categories table
    $stmt->execute();

    $result = $stmt->get_result(); // Get the result set
    $products = $result->fetch_all(MYSQLI_ASSOC); // Fetch all rows as associative array

} catch (mysqli_sql_exception $e) {
    die("Error fetching products: " . $e->getMessage());
}
?>

<body>
<main>
    <div class="container my-5">
        <h2 class="mb-4">Our Products</h2>
        <div class="row">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <!-- Image and Name handling -->
                            <img src="<?= htmlspecialchars($product['image_url'] ?? 'default.jpg') ?>" class="card-img-top" alt="<?= htmlspecialchars($product['product_name'] ?? 'No Name') ?>">
                            <div class="card-body d-flex flex-column">
                                <!-- Display Name and Price -->
                                <h5 class="card-title"><?= htmlspecialchars($product['product_name'] ?? 'No Name') ?></h5>
                                <p class="card-text">$<?= number_format($product['price'] ?? 0, 2) ?></p>
                                <form method="post" action="cart.php?action=add&id=<?= $product['product_id'] ?? 0 ?>" class="mt-auto">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-primary w-100">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No products available at the moment.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include "../../includes/footer.php"; ?>
<script src="/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
