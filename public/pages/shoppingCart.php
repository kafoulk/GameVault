<?php
// Dynamically load header
include "../../includes/header.php";
// Import backend logic for cart
require_once '../src/php/cart.php';
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
                foreach ($_SESSION['cart'] as $item):
                    $item_total = $item['price'] * $item['quantity'];
                    $total_price += $item_total;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td>$<?= number_format($item['price'], 2) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>$<?= number_format($item_total, 2) ?></td>
                        <td>
                            <a href="cart.php?action=remove&id=<?= $item['id'] ?>" class="btn btn-sm btn-danger">Remove</a>
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
            <p>Your cart is empty. <a href="products.php">Browse products</a>.</p>
            <?php include '../src/php/carousel.php'; ?>
        <?php endif; ?>
    </div>
</main>
<?php include "../../includes/footer.php"; ?>
<script src="/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>