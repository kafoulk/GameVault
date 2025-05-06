<?php
session_start();
require_once '../../includes/dbh.inc.php';

// Initialize variables
$login_error = '';
$register_error = '';
$register_success = '';

// Handle logout
if (isset($_GET['logout'])) {
    // Destroy the session
    session_unset();
    session_destroy();

    // Redirect to login page
    header("Location: login.php");
    exit;
}

// Check if login form was submitted
if (isset($_POST['login'])) {
    $email = trim($_POST['login_email']);
    $password = $_POST['login_password'];

    // Validate input
    if (empty($email) || empty($password)) {
        $login_error = "Please fill in all fields";
    } else {
        // Query to find user with the provided email
        $stmt = $conn->prepare("SELECT user_id, username, email, password_hash, first_name, last_name FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify password (in a real app, use password_verify() for hashed passwords)
            // For demonstration, we're using the placeholder password
            if ($password === $user['password_hash'] || $password === 'hashed_password_placeholder') {
                // Password is correct, start a new session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                $_SESSION['email'] = $user['email'];

                // Check if user is admin (for demonstration, we're using user_id 1 as admin)
                $_SESSION['is_admin'] = ($user['user_id'] == 1);

                // Redirect to the login page to show the welcome message
                header("Location: login.php");
                exit;
            } else {
                $login_error = "Invalid email or password";
            }
        } else {
            $login_error = "Invalid email or password";
        }
    }
}

// Check if registration form was submitted
if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);

    // Validate input
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($first_name) || empty($last_name)) {
        $register_error = "Please fill in all fields";
    } elseif ($password !== $confirm_password) {
        $register_error = "Passwords do not match";
    } else {
        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $register_error = "Username or email already exists";
        } else {
            // Hash the password (in a real app, use password_hash())
            $password_hash = $password; // For demonstration purposes only

            // Insert new user into database
            $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, first_name, last_name) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $username, $email, $password_hash, $first_name, $last_name);

            if ($stmt->execute()) {
                $register_success = "Registration successful! You can now log in.";
            } else {
                $register_error = "Error creating account: " . $conn->error;
            }
        }
    }
}

// Get user orders if logged in
$user_orders = [];
if (isset($_SESSION['user_id'])) {
    // If user is admin, get all orders
    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
        $order_query = "SELECT o.*, t.transaction_reference
                        FROM orders o
                        LEFT JOIN transactions t ON o.order_id = t.order_id
                        ORDER BY o.created_at DESC";
        $stmt = $conn->prepare($order_query);
    } else {
        // Get only the user's orders
        $order_query = "SELECT o.*, t.transaction_reference
                        FROM orders o
                        LEFT JOIN transactions t ON o.order_id = t.order_id
                        WHERE o.user_id = ?
                        ORDER BY o.created_at DESC";
        $stmt = $conn->prepare($order_query);
        $stmt->bind_param("i", $_SESSION['user_id']);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $user_orders[] = $row;
        }
    }
}

include "../../includes/header.php";
?>

    <main>
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- User is logged in - show welcome message and orders -->
            <div class="d-flex align-items-center justify-content-center" style="min-height: 70vh;">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="form p-4 text-center" style="border-radius: 10px; background-color: rgba(52, 58, 64, 0.6);">
                                <h2 class="mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>

                                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                                    <div class="alert alert-info">
                                        You are logged in as an administrator.
                                    </div>
                                <?php endif; ?>

                                <!-- Order Tracking Section -->
                                <div class="mt-4">
                                    <h3 class="mb-3"><?php echo (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) ? 'All Orders' : 'Your Orders'; ?></h3>

                                    <?php if (empty($user_orders)): ?>
                                        <div class="alert alert-info">
                                            <?php echo (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) ? 'There are no orders in the system yet.' : 'You have not placed any orders yet.'; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-dark table-hover">
                                                <thead>
                                                <tr>
                                                    <th>Order ID</th>
                                                    <th>Date</th>
                                                    <th>Total</th>
                                                    <th>Status</th>
                                                    <th>Tracking #</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach ($user_orders as $order): ?>
                                                    <tr>
                                                        <td><?php echo $order['order_id']; ?></td>
                                                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                                        <td>
                                                            <span class="badge <?php
                                                            switch($order['order_status']) {
                                                                case 'Pending': echo 'bg-warning text-dark'; break;
                                                                case 'Processing': echo 'bg-info text-dark'; break;
                                                                case 'Shipped': echo 'bg-primary'; break;
                                                                case 'Delivered': echo 'bg-success'; break;
                                                                case 'Cancelled': echo 'bg-danger'; break;
                                                                default: echo 'bg-secondary';
                                                            }
                                                            ?>">
                                                                <?php echo $order['order_status']; ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo $order['tracking_number'] ? $order['tracking_number'] : 'N/A'; ?></td>
                                                        <td>
                                                            <a href="order_details.php?id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-primary">View</a>

                                                            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                                                                <a href="update_order.php?id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-warning">Update</a>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                                    <div class="mt-4 text-center">
                                        <a href="#" class="btn btn-primary m-2">Manage Products</a>
                                        <a href="#" class="btn btn-primary m-2">Generate Reports</a>
                                    </div>
                                <?php endif; ?>

                                <div class="mt-4">
                                    <a href="login.php?logout=1" class="btn btn-danger px-5">Logout</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- User is not logged in - show login/register forms -->
            <div class="container my-5">
                <h2 class="mb-4 text-center">User Login</h2>

                <div class="row">
                    <!-- Login Form -->
                    <div class="col-lg-6 mb-4">
                        <div class="form" style="padding:30px; border-radius: 10px; background-color: rgba(52, 58, 64, 0.6);">
                            <h3 class="mb-3">Login to Your Account</h3>

                            <?php if (!empty($login_error)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $login_error; ?>
                                </div>
                            <?php endif; ?>

                            <form action="" method="POST" class="needs-validation" novalidate>
                                <div class="mb-3">
                                    <label for="login_email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control bg-dark text-light" id="login_email" name="login_email" placeholder="johndoe@email.com" required>
                                    <div class="invalid-feedback">Please enter a valid email address.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="login_password" class="form-label">Password</label>
                                    <input type="password" class="form-control bg-dark text-light" id="login_password" name="login_password" required>
                                    <div class="invalid-feedback">Please enter your password.</div>
                                </div>
                                <div class="text-center">
                                    <button type="submit" name="login" class="btn btn-success px-5">Login</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Registration Form -->
                    <div class="col-lg-6">
                        <div class="form" style="padding:30px; border-radius: 10px; background-color: rgba(52, 58, 64, 0.6);">
                            <h3 class="mb-3">Create an Account</h3>

                            <?php if (!empty($register_error)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $register_error; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($register_success)): ?>
                                <div class="alert alert-success" role="alert">
                                    <?php echo $register_success; ?>
                                </div>
                            <?php endif; ?>

                            <form action="" method="POST" class="row g-3 needs-validation" novalidate>
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control bg-dark text-light" id="first_name" name="first_name" placeholder="John" required>
                                    <div class="invalid-feedback">Please enter your first name.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control bg-dark text-light" id="last_name" name="last_name" placeholder="Doe" required>
                                    <div class="invalid-feedback">Please enter your last name.</div>
                                </div>
                                <div class="col-md-12">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control bg-dark text-light" id="username" name="username" placeholder="johndoe" required>
                                    <div class="invalid-feedback">Please choose a username.</div>
                                </div>
                                <div class="col-md-12">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control bg-dark text-light" id="email" name="email" placeholder="johndoe@email.com" required>
                                    <div class="invalid-feedback">Please enter a valid email address.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control bg-dark text-light" id="password" name="password" required>
                                    <div class="invalid-feedback">Please enter a password.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control bg-dark text-light" id="confirm_password" name="confirm_password" required>
                                    <div class="invalid-feedback">Please confirm your password.</div>
                                </div>
                                <div class="col-12 text-center">
                                    <button type="submit" name="register" class="btn btn-success px-5">Create Account</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

<?php include "../../includes/footer.php"; ?>