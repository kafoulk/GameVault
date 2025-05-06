<?php
session_start();
require_once '../../includes/dbh.inc.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    // Redirect non-admin users to the home page
    header("Location: index.php");
    exit;
}

// Handle product updates
$update_message = '';
if (isset($_POST['update_product'])) {
    $product_id = $_POST['product_id'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];

    try {
        $update_stmt = $conn->prepare("UPDATE products SET category_id = ?, price = ?, stock_quantity = ? WHERE product_id = ?");
        $update_stmt->bind_param("idii", $category_id, $price, $stock_quantity, $product_id);

        if ($update_stmt->execute()) {
            $update_message = '<div class="alert alert-success">Product updated successfully!</div>';
        } else {
            $update_message = '<div class="alert alert-danger">Error updating product.</div>';
        }
    } catch (mysqli_sql_exception $e) {
        $update_message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
    }
}

// Fetch all products
try {
    $stmt = $conn->prepare("SELECT p.product_id, p.product_name, p.price, p.stock_quantity, p.image_url, c.category_name, c.category_id 
                            FROM products p
                            LEFT JOIN categories c ON p.category_id = c.category_id
                            ORDER BY p.product_name");
    $stmt->execute();
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);
} catch (mysqli_sql_exception $e) {
    die("Error fetching products: " . $e->getMessage());
}

// Fetch all categories for dropdown
$categories_stmt = $conn->prepare("SELECT category_id, category_name FROM categories ORDER BY category_name");
$categories_stmt->execute();
$categories_result = $categories_stmt->get_result();
$categories = $categories_result->fetch_all(MYSQLI_ASSOC);

include "../../includes/header.php";
?>

<main>
    <div class="container my-5">
        <h2 class="mb-4">Admin Product Management</h2>

        <?php echo $update_message; ?>

        <div class="mb-4">
            <a href="login.php" class="btn btn-secondary">Back to Admin Dashboard</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                <tr>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td>
                            <img src="../assets/images/products/<?= htmlspecialchars($product['image_url']) ?>"
                                 class="img-thumbnail"
                                 alt="<?= htmlspecialchars($product['product_name']) ?>"
                                 style="max-width: 100px;">
                        </td>
                        <td><?= htmlspecialchars($product['product_name']) ?></td>
                        <td>
                            <form method="post" action="" class="product-form">
                                <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">

                                <select name="category_id" class="form-select form-select-sm">
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['category_id'] ?>" <?= ($category['category_id'] == $product['category_id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($category['category_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" name="price" class="form-control form-control-sm" value="<?= $product['price'] ?>" required>
                            </div>
                        </td>
                        <td>
                            <input type="number" min="0" name="stock_quantity" class="form-control form-control-sm" value="<?= $product['stock_quantity'] ?>" required>
                        </td>
                        <td>
                            <button type="submit" name="update_product" class="btn btn-primary btn-sm">Update</button>
                            <a href="product_details.php?id=<?= $product['product_id'] ?>" class="btn btn-info btn-sm">View</a>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php include "../../includes/footer.php"; ?>
<script>
    // Add confirmation for product updates
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('.product-form');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Are you sure you want to update this product?')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>