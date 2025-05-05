<?php
require_once '../../includes/dbh.inc.php';

if (!isset($_GET['q']) || empty(trim($_GET['q']))) {
    echo "<p class='alert alert-warning'>No search term provided.</p>";
    exit;
}
$search = $conn->real_escape_string($_GET['q']);

// check db for products matching the search term
$sql = "SELECT * FROM products WHERE product_name LIKE '%$search%' OR description LIKE '%$search%'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    // Start displaying search results
    echo '<div class="row">';
    while ($row = $result->fetch_assoc()) {
        echo '
            <div class="container col-md-6 mb-4" style="padding-top:50px">
                <div class="product-card d-flex align-items-center border p-3 rounded">
                    <img src="../assets/images/products/' . $row['image_url'] . '" class="img-fluid" alt="' . htmlspecialchars($row['product_name']) . '" style="max-width: 150px; height: auto; margin-right: 20px;">
                    <div>
                        <h5 class="card-title">' . htmlspecialchars($row['product_name']) . '</h5>
                        <p class="card-text">' . htmlspecialchars($row['description']) . '</p>
                        <p class="card-text"><strong>$' . number_format($row['price'], 2) . '</strong></p>
                        <form method="post" action="shoppingCart.php">
                          <input type="hidden" name="product_id" value="' . $row['product_id'] . '">
                          <input type="hidden" name="quantity" value="1">
                          <button type="submit" class="btn btn-primary w-100">Add to Cart</button>
                        </form>
                    </div>
                </div>
            </div>
        ';
    }
    echo '</div>'; // Close row
} else {
    echo "<p class='alert alert-info'>No results found for '<strong>" . htmlspecialchars($search) . "</strong>'.</p>";
}
?>
