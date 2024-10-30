<?php 
require_once 'main/models.php'; 
require_once 'main/dbConfig.php'; 

// Get the toy reseller ID and validate it
$toy_reseller_id = filter_input(INPUT_GET, 'toy_reseller_id', FILTER_VALIDATE_INT);
if ($toy_reseller_id === false) {
    header('Location: error.php?msg=Invalid reseller ID.'); // Redirect to an error page
    exit;
}

$getToyResellerByID = getToyResellerByID($pdo, $toy_reseller_id);
if (!$getToyResellerByID) {
    echo "Reseller not found.";
    exit; // Alternatively, redirect to an error page
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make New Toy</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>Username: <?php echo htmlspecialchars($getToyResellerByID['username']); ?></h1>
    
    <h1>Add New Toy</h1>
    <form action="main/handleForms.php?toy_reseller_id=<?php echo $toy_reseller_id; ?>" method="POST">
        <p>
            <label for="toy_name">New Toy Name</label>
            <input type="text" id="toy_name" name="toy_name" placeholder="Enter toy name" required>
        </p>
        <p>
            <label for="toy_type">New Toy Type</label>
            <input type="text" id="toy_type" name="toy_type" placeholder="Enter toy type" required>
        </p>
        <input type="submit" name="insertNewToyBtn" value="Add Toy">
    </form>
</div>

<a href="index.php">Return to home</a>
<hr>

<div class="table_container">
    <table>
        <tr>
            <th>Toy ID</th>
            <th>Toy Name</th>
            <th>Toy Type</th>
            <th>Date Added</th>
            <th>Action</th>
        </tr>
        <?php 
        // Fetch toys for the reseller
        $getToysByToyReseller = getToysByToyReseller($pdo, $toy_reseller_id); 
        foreach ($getToysByToyReseller as $row): 
        ?>
        <tr>
            <td><?php echo htmlspecialchars($row['toy_id']); ?></td>     
            <td><?php echo htmlspecialchars($row['toy_name']); ?></td>     
            <td><?php echo htmlspecialchars($row['toy_type']); ?></td>     
            <td><?php echo htmlspecialchars($row['date_added']); ?></td>
            <td>
                <a href="edit_toy.php?toy_id=<?php echo $row['toy_id']; ?>&toy_reseller_id=<?php echo $toy_reseller_id; ?>">Edit</a>
                <a href="deletetoy.php?toy_id=<?php echo $row['toy_id']; ?>&toy_reseller_id=<?php echo $toy_reseller_id; ?>">Delete</a>
            </td>     
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
