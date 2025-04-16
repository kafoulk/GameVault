<?php
// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle add to cart action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    // Fetch product details from the database
    $product = getProductById($product_id);

    if ($product) {
        // Check if the product is already in the cart
        if (isset($_SESSION['cart'][$product_id])) {
            // Update the quantity
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            // Add new product to the cart
            $_SESSION['cart'][$product_id] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity
            ];
        }
    }

    // Redirect to the cart page
    header('Location: cart.php');
    exit;
}

// Handle remove from cart action
if (isset($_GET['action']) && $_GET['action'] === 'remove' && isset($_GET['id'])) {
    $product_id = $_GET['id'];
    unset($_SESSION['cart'][$product_id]);
    header('Location: cart.php');
    exit;
}

// Function to fetch product details from the database
function getProductById($id)
{
    // Replace with your actual database connection and query
    // Example using PDO:
    $pdo = new PDO('mysql:host=localhost;dbname=gamevault', 'root', '');
    $stmt = $pdo->prepare('SELECT id, name, price FROM products WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function calculate_cart_total()
{
    $total = 0;

    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
    }

    return $total;
}
