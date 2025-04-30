<?php
require_once __DIR__ . '/../../../includes/dbh.inc.php';

// create cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// add to cart action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['quantity'])) {
    // Sanitize inputs
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    // fetch product details from the db
    $product = getProductById($product_id);

    if ($product) {
        // check if the product is already in the cart
        if (isset($_SESSION['cart'][$product_id])) {
            // update the quantity
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            // add new product to the cart
            $_SESSION['cart'][$product_id] = [
                'id' => $product['product_id'],
                'description' => $product['description'],
                'name' => $product['product_name'],
                'price' => $product['price'],
                'quantity' => $quantity
            ];
        }
    }
}

// remove from cart action
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'remove') {
    $product_id = $_GET['id'];
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
    header('Location: ../../pages/shoppingCart.php');
    exit;
}

// Increase quantity
if (isset($_GET['action']) && $_GET['action'] === 'increase' && isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] += 1;
    }
    header('Location: ../../pages/shoppingCart.php');
    exit;
}

// Decrease quantity
if (isset($_GET['action']) && $_GET['action'] === 'decrease' && isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] -= 1;

        // Remove item if quantity goes below 1
        if ($_SESSION['cart'][$product_id]['quantity'] < 1) {
            unset($_SESSION['cart'][$product_id]);
        }
    }
    header('Location: ../../pages/shoppingCart.php');
    exit;
}

// fetch product details from db
function getProductById($id)
{
    global $conn;
    $stmt = $conn->prepare('SELECT product_id, product_name, price, description FROM products WHERE product_id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Calculate cart total
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
