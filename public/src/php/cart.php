<?php
ob_start();
SESSION_START();
require_once __DIR__ . '/../../../includes/dbh.inc.php';

// Ensure cart is initialized
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];

    // If it's a remove action
    if (isset($_POST['action']) && $_POST['action'] === 'remove') {
        unset($_SESSION['cart'][$product_id]);

        // Else, assume it's an add-to-cart
    } elseif (isset($_POST['quantity'])) {
        $quantity = (int)$_POST['quantity'];
        $product = getProductById($product_id);

        if ($product) {
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = [
                    'id' => $product_id,
                    'name' => $product['product_name'],
                    'description' => $product['description'],
                    'price' => $product['price'],
                    'quantity' => $quantity
                ];
            }
        }
    }

    // Redirect after any POST action
    header("Location: /GameVault/public/pages/shoppingCart.php");
    exit;
}

// Handle GET actions like increase, decrease
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'], $_GET['id'])) {
    $action = $_GET['action'];
    $product_id = (int)$_GET['id'];

    if (isset($_SESSION['cart'][$product_id])) {
        switch ($action) {
            case 'increase':
                $_SESSION['cart'][$product_id]['quantity'] += 1;
                break;

            case 'decrease':
                if ($_SESSION['cart'][$product_id]['quantity'] > 1) {
                    $_SESSION['cart'][$product_id]['quantity'] -= 1;
                } else {
                    unset($_SESSION['cart'][$product_id]);
                }
                break;
        }
    }

    // Always redirect after a GET cart action
    header("Location: /GameVault/public/pages/shoppingCart.php");
    exit;
}


// ---------- Helper Functions ----------

/**
 * Get product details from the database by ID
 */
function getProductById(int $id): array|false
{
    global $conn;

    $stmt = $conn->prepare("SELECT product_id, product_name, price, description FROM products WHERE product_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Calculate the total value of the cart
 */
function calculate_cart_total(): float
{
    $total = 0.0;

    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
    }

    return $total;
}
