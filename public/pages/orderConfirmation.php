<?php
require_once '../../includes/header.php';
require_once __DIR__ . '/../../includes/dbh.inc.php';

$transaction_ref = htmlspecialchars($_GET['TXN'] ?? 'UNKNOWN');
?>

<main class="container my-5 text-center">
    <h2>Thank You for Your Purchase!</h2>
    <p>Your order has been placed successfully.</p>
    <p><strong>Tracking Number:</strong> <?= $transaction_ref ?></p>
    <a href="index.php" class="btn btn-primary mt-4">Back to Home</a>
</main>

<?php include '../../includes/footer.php'; ?>
