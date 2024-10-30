<?php 

require_once 'dbConfig.php'; 
require_once 'models.php';

if (isset($_POST['insertToyResellerBtn'])) {
    if (!isset($_SESSION['user_id'])) {
        echo "User is not logged in.";
        exit; // Stop execution if the user is not logged in
    }
    
    $user_id = $_SESSION['user_id'];
    
    $query = insertToyReseller($pdo, $_POST['username'], $_POST['first_name'], 
        $_POST['last_name'], $_POST['gender'], $_POST['age'], $_POST['date_of_birth'], 
        $_POST['location'], $user_id, $user_id); // Use user_id here

    if ($query) {
        header("Location: ../index.php");
        exit;
    } else {
        echo "Insertion failed";
    }
}

if (isset($_POST['editToyResellerBtn'])) {
    $user_id = $_SESSION['user_id'] ?? null; // Get user_id from session
    $username = $_SESSION['username'] ?? null;
    $query = updateToyReseller($pdo, $_POST['username'], $_POST['first_name'], $_POST['last_name'], 
        $_POST['gender'], $_POST['age'], $_POST['date_of_birth'], $_POST['location'], 
        $_GET['toy_reseller_id'], $user_id); // Ensure user_id is passed

    if ($query) {
        header("Location: ../index.php");
        exit;
    } else {
        echo "Edit failed";
    }
}

if (isset($_POST['editToyBtn'])) {
    $user_id = $_SESSION['user_id'] ?? null; // Ensure user_id is set
    $username = $_SESSION['username'] ?? null;
    $query = updateToy($pdo, $_POST['toy_name'], $_POST['toy_type'], $_GET['toy_id'], 
        $user_id, $username); // Use user_id here

    if ($query) {
        header("Location: ../view_toy.php?toy_reseller_id=" . $_GET['toy_reseller_id']);
        exit;
    } else {
        echo "Update failed";
    }
}

if (isset($_POST['deleteToyResellerBtn'])) {

    $query = deleteToyReseller($pdo, $_GET['toy_reseller_id']); // Ensure all variables are set

    if ($query) {
        header("Location: ../index.php");
        exit;
    } else {
        echo "Deletion failed";
    }
}

if (isset($_POST['insertExistingToyBtn'])) {
    $existing_toy = $_POST['existing_toy'];
    $toy_reseller_id = filter_input(INPUT_GET, 'toy_reseller_id', FILTER_VALIDATE_INT);
    $user_id = $_SESSION['user_id'] ?? null; // Get the user_id from the session

    if (!empty($existing_toy) && $user_id !== null) {
        list($toy_name, $toy_type) = explode(' - ', $existing_toy);

        // Query to update the toy's reseller
        $query = $pdo->prepare("UPDATE toys SET toy_reseller_id = :toy_reseller_id, added_by = :user_id WHERE toy_name = :toy_name AND toy_type = :toy_type");
        $query->bindParam(':toy_reseller_id', $toy_reseller_id);
        $query->bindParam(':toy_name', $toy_name);
        $query->bindParam(':toy_type', $toy_type);
        $query->bindParam(':user_id', $user_id);

        if ($query->execute()) {
            logAction($pdo, $user_id, 'UPDATE', "Changed ownership of $toy_name to toy reseller id: $toy_reseller_id");
            header("Location: ../view_toy.php?toy_reseller_id=" . $toy_reseller_id);
            exit;
        } else {
            echo "Insertion failed";
        }
    } else {
        echo "No toy selected or user not authenticated.";
    }
}

if (isset($_POST['insertNewToyBtn'])) {
    $toy_name = filter_input(INPUT_POST, 'toy_name', FILTER_SANITIZE_STRING);
    $toy_type = filter_input(INPUT_POST, 'toy_type', FILTER_SANITIZE_STRING);
    $toy_reseller_id = filter_input(INPUT_GET, 'toy_reseller_id', FILTER_VALIDATE_INT);
    $user_id = $_SESSION['user_id'] ?? null; // Ensure user_id is set
    $username = $_SESSION['username'] ?? null;
    // Insert new toy with valid toy_reseller_id
    if ($toy_reseller_id !== false && insertToys($pdo, $toy_name, $toy_type, $toy_reseller_id, $user_id, $user_id, $username)) {
        header("Location: ../view_new_toy.php?toy_reseller_id=" . $toy_reseller_id);
        exit;
    } else {
        echo "Failed to add new toy. Please check the reseller ID and other inputs.";
    }
}

if (isset($_POST['deleteToyBtn'])) {
    $user_id = $_SESSION['user_id'] ?? null; // Ensure user_id is set
    $query = deleteToy($pdo, $_GET['toy_id']); // Ensure all variables are set
    if ($query) {
        header("Location: ../view_toy.php?toy_reseller_id=" . $_GET['toy_reseller_id']);
        exit;
    } else {
        echo "Deletion failed";
    }
}

// Registration Logic
if (isset($_POST['regBtn'])) {
    // Sanitize and retrieve input
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
    $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
    $gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $age = filter_input(INPUT_POST, 'age', FILTER_SANITIZE_NUMBER_INT);
    $date_of_birth = filter_input(INPUT_POST, 'date_of_birth', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $contact_no = filter_input(INPUT_POST, 'contact_no', FILTER_SANITIZE_STRING);

    // Validate input
    if (empty($username) || empty($first_name) || empty($last_name) || empty($gender) || empty($email) || empty($password) || empty($age) || empty($date_of_birth) || empty($address) || empty($contact_no)) {
        $_SESSION['message'] = "All fields are required.";
        header('Location: ../register.php');
        exit;
    }

    // Password complexity check (optional)
    if (strlen($password) < 8) {
        $_SESSION['message'] = "Password must be at least 8 characters long.";
        header('Location: ../register.php');
        exit;
    }

    // Check if username or email already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->rowCount() > 0) {
        $_SESSION['message'] = "Username or email already exists.";
        header('Location: ../register.php');
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into the database
    $stmt = $pdo->prepare("INSERT INTO users (username, first_name, last_name, gender, email, password, age, date_of_birth, address, contact_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$username, $first_name, $last_name, $gender, $email, $hashed_password, $age, $date_of_birth, $address, $contact_no])) {
        $_SESSION['message'] = "Registration successful! You can log in now.";
        header('Location: ../login.php');
        exit;
    } else {
        $_SESSION['message'] = "Registration failed. Please try again.";
        header('Location: ../register.php');
        exit;
    }
}

if (isset($_POST['loginBtn'])) {
    // Sanitize input
    $login_input = filter_input(INPUT_POST, 'login_input', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    // Fetch user from the database by either username or email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$login_input, $login_input]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch as associative array

    // Verify the user
    if ($user && password_verify($password, $user['password'])) { // Change 'hashed_password' to 'password' to match your schema
        // Password is correct, set session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['first_name'] . ' ' . $user['last_name']; 
        $_SESSION['welcomeMessage'] = "Welcome, " . $_SESSION['username'] . "!";

        header("Location: ../index.php");
        exit;
    } else {
        // Invalid input
        $_SESSION['message'] = "Invalid login input. Please try again.";
        header("Location: ../login.php");
        exit;
    }
}

