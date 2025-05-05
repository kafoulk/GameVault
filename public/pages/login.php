<?php
session_start();
require_once '../../includes/dbh.inc.php';

// Initialize variables
$login_error = '';
$register_error = '';
$register_success = '';

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

                // Redirect to home page
                header("Location: index.php");
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

include "../../includes/header.php";
?>

    <main>
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
    </main>

<?php include "../../includes/footer.php"; ?>