<?php 
require_once 'main/handleForms.php';
require_once 'main/models.php'; 

// Check for user login
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];
} else {
    // Redirect to login if not logged in
    header("Location: login.php");
    exit();
}

// Ensure toy_reseller_id is set and valid
$toy_reseller_id = isset($_GET['toy_reseller_id']) ? intval($_GET['toy_reseller_id']) : 0;
$getToyResellersByID = getToyResellerByID($pdo, $toy_reseller_id);

// Check if the toy reseller exists
if (!$getToyResellersByID) {
    echo "Toy Reseller not found.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Toy Reseller</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>Edit the Toy Reseller!</h1>

    <!-- Display error message if exists -->
    <?php if (isset($error_message)): ?>
        <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>

    <form action="main/handleForms.php?toy_reseller_id=<?php echo $toy_reseller_id; ?>" method="POST">
        <p>
            <label for="username">Username</label> 
            <input type="text" name="username" value="<?php echo htmlspecialchars($getToyResellersByID['username']); ?>" required>
        </p>

        <p>
            <label for="first_name">First Name</label> 
            <input type="text" name="first_name" value="<?php echo htmlspecialchars($getToyResellersByID['first_name']); ?>" required>
        </p>
        <p>
            <label for="last_name">Last Name</label> 
            <input type="text" name="last_name" value="<?php echo htmlspecialchars($getToyResellersByID['last_name']); ?>" required>
        </p>
        <p>
            <label for="gender">Gender</label>
            <select name="gender" required>
                <option value="">--Select Gender--</option>
                <?php
                $genders = ["Male", "Female", "Nonbinary", "Secret", "Helicopter"];
                foreach ($genders as $gender) {
                    $selected = ($gender === $getToyResellersByID['gender']) ? "selected" : "";
                    echo "<option value='" . htmlspecialchars($gender) . "' $selected>" . htmlspecialchars($gender) . "</option>";
                }
                ?>
            </select>
        </p>
        <p>
            <label for="age">Age</label> 
            <input type="number" name="age" value="<?php echo htmlspecialchars($getToyResellersByID['age']); ?>" required>
        </p>
        <p>
            <label for="date_of_birth">Date of Birth</label> 
            <input type="date" name="date_of_birth" value="<?php echo htmlspecialchars($getToyResellersByID['date_of_birth']); ?>" required>
        </p>
        <p>
            <label for="location">Location</label> 
            <input type="text" name="location" value="<?php echo htmlspecialchars($getToyResellersByID['location']); ?>" required>
        </p>
        <p>
            <input type="submit" name="editToyResellerBtn" value="Edit">
        </p>
    </form>
</div>
</body>
</html>
