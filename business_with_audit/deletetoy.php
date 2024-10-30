<?php 
require_once 'main/dbConfig.php';
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

// Ensure the toy_id and toy_reseller_id are present and valid
$toy_id = isset($_GET['toy_id']) ? intval($_GET['toy_id']) : 0;
$toy_reseller_id = isset($_GET['toy_reseller_id']) ? intval($_GET['toy_reseller_id']) : 0;

// Get toy details
$getToyByID = getToyByID($pdo, $toy_id);

// Check if the toy exists
if (!$getToyByID) {
    echo "Toy not found.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Delete Toy Confirmation</title>
	<link rel="stylesheet" href="styles.css">
</head>
<body>
	<h1>Are you sure you want to delete this toy?</h1>
	<div class="container">
		<h2>Toy Name: <?php echo htmlspecialchars($getToyByID['toy_name']); ?></h2>
		<h2>Toy Type: <?php echo htmlspecialchars($getToyByID['toy_type']); ?></h2>
		<h2>Toy Owners: <?php echo htmlspecialchars($getToyByID['toy_owner']); ?></h2>
		<h2>Date Added: <?php echo htmlspecialchars($getToyByID['date_added']); ?></h2>

		<div class="deleteBtn" style="float: right; margin-right: 10px;">
			<form action="main/handleForms.php?toy_id=<?php echo $toy_id; ?>&toy_reseller_id=<?php echo $toy_reseller_id; ?>" method="POST">
				<input type="submit" name="deleteToyBtn" value="Delete">
			</form>			
		</div>	
	</div>
</body>
</html>
