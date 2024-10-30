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

// Ensure toy_id and toy_reseller_id are set and valid
$toy_id = isset($_GET['toy_id']) ? intval($_GET['toy_id']) : 0;
$toy_reseller_id = isset($_GET['toy_reseller_id']) ? intval($_GET['toy_reseller_id']) : 0;

// Fetch the toy by ID
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
	<title>Edit Toy</title>
	<link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
	<a href="view_toy.php?toy_reseller_id=<?php echo $toy_reseller_id; ?>">View The Toys</a>
	<h1>Edit the Toy!</h1>

	<form action="main/handleForms.php?toy_id=<?php echo $toy_id; ?>&toy_reseller_id=<?php echo $toy_reseller_id; ?>" method="POST">
		<p>
			<label for="toy_name">Toy Name</label> 
			<input type="text" name="toy_name" value="<?php echo htmlspecialchars($getToyByID['toy_name']); ?>" required>
		</p>
		<p>
			<label for="toy_type">Toy Type</label> 
			<input type="text" name="toy_type" value="<?php echo htmlspecialchars($getToyByID['toy_type']); ?>" required>
		</p>
		<p>
			<input type="submit" name="editToyBtn" value="Edit Toy">
		</p>
	</form>
</div>
</body>
</html>
