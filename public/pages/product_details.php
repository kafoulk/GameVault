<?php
// Dynamically load header
include "../../includes/header.php";

// Connect to database
require_once '../../includes/dbh.inc.php';

// Check database connection
if (!isset($conn) || $conn->connect_error) {
    die("Connection failed: " . (isset($conn) ? $conn->connect_error : "Connection variable not set"));
}

// Get product ID from the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Product ID is missing.");
}

$product_id = $_GET['id'];

// Fetch product details from the database based on product_id
try {
    $stmt = $conn->prepare("SELECT p.product_id, p.product_name, p.description, p.price, p.stock_quantity, p.image_url, c.category_name 
                            FROM products p
                            LEFT JOIN categories c ON p.category_id = c.category_id
                            WHERE p.product_id = ?");
    $stmt->bind_param("i", $product_id); // Bind the product ID as an integer
    $stmt->execute();

    $result = $stmt->get_result();
    $product = $result->fetch_assoc(); // Fetch product details
    if (!$product) {
        die("Product not found.");
    }

} catch (mysqli_sql_exception $e) {
    die("Error fetching product details: " . $e->getMessage());
}

// Calculate delivery date (4 to 7 days from today)
$delivery_date = date('l, F j, Y', strtotime('+4 days'));
$max_delivery_date = date('l, F j, Y', strtotime('+7 days'));


?>

<body>
<main>
    <div class="container my-5">
        <h2 class="mb-4"><?= htmlspecialchars($product['product_name']) ?></h2>

        <div class="row">
            <div class="col-md-6 mb-4">
                <!-- Product Image -->
                <img src="<?= htmlspecialchars($product['image_url'] ?? 'default.jpg') ?>" class="img-fluid" alt="<?= htmlspecialchars($product['product_name'] ?? 'No Name') ?>">
            </div>
            <div class="col-md-6 mb-4">
                <h3 class="card-title"><?= htmlspecialchars($product['product_name']) ?></h3>
                <p class="card-text"><?= nl2br(htmlspecialchars($product['description'] ?? 'No description available.')) ?></p>
                <p><strong>Category:</strong> <?= htmlspecialchars($product['category_name'] ?? 'Uncategorized') ?></p>
                <p><strong>Price:</strong> $<?= number_format($product['price'] ?? 0, 2) ?></p>
                <p><strong>Delivery Date:</strong> <?= $delivery_date ?> to <?= $max_delivery_date ?></p>
                <form method="post" action="cart.php?action=add&id=<?= $product['product_id'] ?? 0 ?>" class="mt-3">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn btn-primary w-100">Add to Cart</button>
                </form>
            </div>
        </div>
    </div>
</main>

<?php include "../../includes/footer.php"; ?>
<script src="/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
