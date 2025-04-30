<?php
session_start();
require_once __DIR__ . '/../../includes/dbh.inc.php';

// Ensure the cart is not empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: shoppingCart.php");
    exit;
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = trim($_POST['customer_name']);
    $customer_email = trim($_POST['customer_email']);
    $customer_address = trim($_POST['customer_address']);
    $payment_method = $_POST['payment_method'];

    if (empty($customer_name) || empty($customer_email) || empty($customer_address) || empty($payment_method)) {
        $error = "Please fill in all fields.";
    } else {
        // Calculate total
        $total_amount = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total_amount += $item['price'] * $item['quantity'];
        }

        $order_status = 'Processing';
        $tracking_number = 'TRK' . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $created_at = date('Y-m-d H:i:s');

        // Insert into orders
        $stmt = $conn->prepare("INSERT INTO orders (user_id, customer_name, customer_email, customer_address, total_amount, order_status, tracking_number, created_at)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $user_id = $_SESSION['user_id'] ?? 1; // Default user if not logged in
        $stmt->bind_param('isssdsss', $user_id, $customer_name, $customer_email, $customer_address, $total_amount, $order_status, $tracking_number, $created_at);
        $stmt->execute();
        $order_id = $stmt->insert_id;

        // Simulate a transaction reference
        $transaction_reference = 'TXN' . rand(100000, 999999);
        $transaction_status = 'Completed';

        // Insert into transactions
        $stmt2 = $conn->prepare("INSERT INTO transactions (order_id, amount, payment_method, transaction_status, transaction_reference, created_at)
                                 VALUES (?, ?, ?, ?, ?, ?)");
        $stmt2->bind_param('idssss', $order_id, $total_amount, $payment_method, $transaction_status, $transaction_reference, $created_at);
        $stmt2->execute();

        // Clear the cart
        $_SESSION['cart'] = [];
    }
    // Redirect or show success
    $success = "Order placed successfully! Order ID: $order_id";
    header("Location: orderConfirmation.php?order_id=$order_id");
    exit;
}
?>

<?php include "../../includes/header.php"; ?>
<div class="container my-5">
    <h2>Checkout</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="post" class="row g-3">
        <div class="col-md-6">
            <label for="customer_name" class="form-label">Full Name</label>
            <input type="text" class="form-control" name="customer_name" required>
        </div>
        <div class="col-md-6">
            <label for="customer_email" class="form-label">Email</label>
            <input type="email" class="form-control" name="customer_email" required>
        </div>
        <div class="col-12">
            <label for="customer_address" class="form-label">Shipping Address</label>
            <input type="text" class="form-control" name="customer_address" required>
        </div>
        <div class="col-md-6">
            <label for="payment_method" class="form-label">Payment Method</label>
            <select class="form-select" name="payment_method" required>
                <option value="">Choose...</option>
                <option value="Credit Card">Credit Card</option>
                <option value="PayPal">PayPal</option>
                <option value="Bank Transfer">Bank Transfer</option>
            </select>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary w-100">Place Order</button>
        </div>
    </form>
</div>
<?php include "../../includes/footer.php"; ?>
