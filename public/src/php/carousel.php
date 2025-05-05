<?php

include __DIR__ . '/../../../includes/dbh.inc.php';

// Define base URL relative to your server root
$basePath = '../../../../GameVault/public/assets/images/products/';

$sql = "SELECT product_name, image_url FROM products";
$result = $conn->query($sql);
?>

<div class="carousel-wrapper">
    <div class="carousel" id="carousel">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="featured-products">
                <img
                        src="<?php echo $basePath . htmlspecialchars($row['image_url']); ?>"
                        alt="<?php echo htmlspecialchars($row['product_name']); ?>"
                />
                <p><?php echo htmlspecialchars($row['product_name']); ?></p>
            </div>
        <?php endwhile; ?>
    </div>
</div>
