<?php 
require_once 'main/handleForms.php';
require_once 'main/models.php'; 

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize user inputs
    $login_input = filter_input(INPUT_POST, 'login_input', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    // Fetch user from the database by either username or email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$login_input, $login_input]);
    $user = $stmt->fetch();

    // Verify the user
    if ($user && password_verify($password, $user['hashed_password'])) { 
        // Password is correct, set session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['first_name'] . ' ' . $user['last_name']; 
        $_SESSION['welcomeMessage'] = "Welcome, " . $_SESSION['username'] . "!";

        header("Location: index.php");
        exit;
    } else {
        // Invalid input
        $_SESSION['message'] = "Invalid Login input. Please try again.";
        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css"> 
</head>
    <?php if (isset($_SESSION['message'])): ?>
        <p style="color: red;"><?php echo $_SESSION['message']; ?></p>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <div class = 'container'>
    <h1>Login</h1>
    <form action="main/handleForms.php" method="POST">
        <p>
            <label for="login_input">Username or Email</label>
            <input type="text" name="login_input" required placeholder="Enter your username or email">
        </p>
        <p>
            <label for="password">Password</label>
            <input type="password" name="password" required>
        </p>
        <p><input type="submit" value="login" id="loginBtn" name="loginBtn"></p>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a>.</p>
</body>
</div>
</html>
