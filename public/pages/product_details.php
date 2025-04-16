
<?php
include '../../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_name']); ?> - GameVault</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="product-details">
    <h1><?php echo htmlspecialchars($product['product_name']); ?></h1>
    <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category_name']); ?></p>
    <p><strong>Price:</strong> $<?php echo htmlspecialchars($product['price']); ?></p>
    <p><strong>Stock:</strong> <?php echo htmlspecialchars($product['stock_quantity']); ?> available</p>
    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" />
</div>
</body>
</html>

<?php include "../../includes/footer.php"; ?>