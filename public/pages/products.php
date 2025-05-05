<?php
// Dynamically load header
include "../../includes/header.php";

// Connect to database
require_once '../../includes/dbh.inc.php';

// Check database connection
if (!isset($conn) || $conn->connect_error) {
    die("Connection failed: " . (isset($conn) ? $conn->connect_error : "Connection variable not set"));
}
try {
    $stmt = $conn->prepare("SELECT p.product_id, p.product_name, p.price, p.image_url, c.category_name 
                            FROM products p
                            LEFT JOIN categories c ON p.category_id = c.category_id");
    $stmt->execute();

    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);

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
                        <div class="product-card h-100 text-center p-3 border rounded shadow-sm">
                            <!-- Image and Name linking to product details page -->
                            <a href="product_details.php?id=<?= $product['product_id'] ?>">
                                <img src="../assets/images/products/<?= htmlspecialchars($product['image_url']) ?>"
                                     class="img-fluid mb-3"
                                     alt="<?= htmlspecialchars($product['product_name']) ?>"
                                     style="max-height: 200px; object-fit: contain;">
                            </a>
                            <div class="card-body d-flex flex-column align-items-center">
                                <h5 class="card-title"><?= htmlspecialchars($product['product_name']) ?></h5>
                                <p class="card-text">$<?= number_format($product['price'], 2) ?></p>
                                <form method="post" action="shoppingCart.php" class="w-100">
                                    <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
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
