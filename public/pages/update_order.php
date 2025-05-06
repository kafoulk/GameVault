<?php
session_start();
require_once '../../includes/dbh.inc.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    // Redirect non-admin users to the home page
    header("Location: index.php");
    exit;
}

// Initialize variables
$update_message = '';
$order = null;

// Get order ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: login.php");
    exit;
}

$order_id = $_GET['id'];

// Handle order update
if (isset($_POST['update_order'])) {
    $order_status = $_POST['order_status'];
    $tracking_number = $_POST['tracking_number'];

    try {
        $update_stmt = $conn->prepare("UPDATE orders SET order_status = ?, tracking_number = ? WHERE order_id = ?");
        $update_stmt->bind_param("ssi", $order_status, $tracking_number, $order_id);

        if ($update_stmt->execute()) {
            $update_message = '<div class="alert alert-success">Order updated successfully!</div>';
        } else {
            $update_message = '<div class="alert alert-danger">Error updating order.</div>';
        }
    } catch (mysqli_sql_exception $e) {
        $update_message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
    }
}

// Fetch order details
try {
    $stmt = $conn->prepare("SELECT o.*, t.transaction_reference 
                           FROM orders o
                           LEFT JOIN transactions t ON o.order_id = t.order_id
                           WHERE o.order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();

    if (!$order) {
        header("Location: login.php");
        exit;
    }

} catch (mysqli_sql_exception $e) {
    die("Error fetching order details: " . $e->getMessage());
}

// Fetch order items
try {
    $items_stmt = $conn->prepare("SELECT oi.*, p.product_name, p.image_url 
                                 FROM order_items oi
                                 JOIN products p ON oi.product_id = p.product_id
                                 WHERE oi.order_id = ?");
    $items_stmt->bind_param("i", $order_id);
    $items_stmt->execute();
    $items_result = $items_stmt->get_result();
    $order_items = $items_result->fetch_all(MYSQLI_ASSOC);

} catch (mysqli_sql_exception $e) {
    $order_items = [];
}

include "../../includes/header.php";
?>

    <main>
        <div class="container my-5">
            <h2 class="mb-4">Update Order #<?= $order_id ?></h2>

            <?= $update_message ?>

            <div class="mb-4">
                <a href="login.php" class="btn btn-secondary">Back to Admin Dashboard</a>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4>Order Information</h4>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <th>Order ID:</th>
                                    <td><?= $order['order_id'] ?></td>
                                </tr>
                                <tr>
                                    <th>Date:</th>
                                    <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                                </tr>
                                <tr>
                                    <th>Customer:</th>
                                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td><?= htmlspecialchars($order['customer_email']) ?></td>
                                </tr>
                                <tr>
                                    <th>Address:</th>
                                    <td><?= htmlspecialchars($order['customer_address']) ?></td>
                                </tr>
                                <tr>
                                    <th>Total Amount:</th>
                                    <td>$<?= number_format($order['total_amount'], 2) ?></td>
                                </tr>
                                <tr>
                                    <th>Transaction Ref:</th>
                                    <td><?= $order['transaction_reference'] ?? 'N/A' ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4>Update Order Status</h4>
                        </div>
                        <div class="card-body">
                            <form method="post" action="">
                                <div class="mb-3">
                                    <label for="order_status" class="form-label">Order Status:</label>
                                    <select name="order_status" id="order_status" class="form-select" required>
                                        <option value="Pending" <?= $order['order_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="Processing" <?= $order['order_status'] == 'Processing' ? 'selected' : '' ?>>Processing</option>
                                        <option value="Shipped" <?= $order['order_status'] == 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                                        <option value="Delivered" <?= $order['order_status'] == 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                                        <option value="Cancelled" <?= $order['order_status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="tracking_number" class="form-label">Tracking Number:</label>
                                    <input type="text" name="tracking_number" id="tracking_number" class="form-control" value="<?= htmlspecialchars($order['tracking_number'] ?? '') ?>">
                                </div>

                                <button type="submit" name="update_order" class="btn btn-primary w-100">Update Order</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h4>Order Items</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($order_items)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($order_items as $item): ?>
                                    <tr>
                                        <td>
                                            <img src="../assets/images/products/<?= htmlspecialchars($item['image_url']) ?>"
                                                 class="img-thumbnail"
                                                 alt="<?= htmlspecialchars($item['product_name']) ?>"
                                                 style="max-width: 80px;">
                                        </td>
                                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                                        <td>$<?= number_format($item['price_per_unit'], 2) ?></td>
                                        <td><?= $item['quantity'] ?></td>
                                        <td>$<?= number_format($item['subtotal'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>No items found for this order.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

<?php include "../../includes/footer.php"; ?>