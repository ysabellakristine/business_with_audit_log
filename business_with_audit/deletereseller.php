<?php 
require_once 'main/models.php'; 
require_once 'main/dbConfig.php'; 

// Check for user login
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];
} else {
    // Redirect to login if not logged in
    header("Location: login.php");
    exit();
}

// Sanitizing input
$toy_reseller_id = isset($_GET['toy_reseller_id']) ? intval($_GET['toy_reseller_id']) : 0;

// Get toy reseller information
$getToyResellersByID = getToyResellerByID($pdo, $toy_reseller_id);

// Check if the toy reseller exists
if (!$getToyResellersByID) {
    echo "Toy reseller not found.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Confirm Deletion</title>
	<link rel="stylesheet" href="styles.css">
</head>
<body>
	<h1>Are you sure you want to delete this user?</h1>
	<div class="container">
		<h2>Username: <?php echo htmlspecialchars($getToyResellersByID['username']); ?></h2>
		<h2>First Name: <?php echo htmlspecialchars($getToyResellersByID['first_name']); ?></h2>
		<h2>Last Name: <?php echo htmlspecialchars($getToyResellersByID['last_name']); ?></h2>
		<h2>Date Of Birth: <?php echo htmlspecialchars($getToyResellersByID['date_of_birth']); ?></h2>
		<h2>Location: <?php echo htmlspecialchars($getToyResellersByID['location']); ?></h2>
		<h2>Date Added: <?php echo htmlspecialchars($getToyResellersByID['date_added']); ?></h2>

		<div class="deleteBtn" style="float: right; margin-right: 10px;">
			<form action="main/handleForms.php?toy_reseller_id=<?php echo $toy_reseller_id; ?>" method="POST">
				<input type="submit" name="deleteToyResellerBtn" value="Delete">
			</form>			
		</div>	
	</div>
</body>
</html>
