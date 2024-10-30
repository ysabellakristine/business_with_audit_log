<?php 
require_once 'main/models.php'; 
require_once 'main/dbConfig.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit; // Ensure no further code is executed
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<hr> <hr>
<a href="logout.php" class="button">LOGOUT</a>
<div class="container">
    <h1>Welcome To the Toy Resellers System. <br> Insert a Toy Reseller below!</h1>
    <form action="main/handleForms.php" method="POST">
        <p>
            <label for="username">Username</label> 
            <input type="text" name="username" required>
        </p>
        <p>
            <label for="first_name">First Name</label> 
            <input type="text" name="first_name" required>
        </p>
        <p>
            <label for="last_name">Last Name</label> 
            <input type="text" name="last_name" required>
        </p>
        <p>
            <label for="gender">Gender</label>
            <select name="gender" required>
                <option value="">--Select Gender--</option>
                <?php
                $genders = ["Male", "Female", "Nonbinary", "Secret", "Helicopter"];
                foreach ($genders as $gender) {
                    echo "<option value='".htmlspecialchars($gender)."'>".htmlspecialchars($gender)."</option>";
                }
                ?>
            </select>
        </p>
        <p>
            <label for="age">Age</label> 
            <input type="number" name="age" min="0" required>
        </p>
        <p>
            <label for="date_of_birth">Date of Birth</label> 
            <input type="date" name="date_of_birth" required>
        </p>
        <p>
            <label for="location">Location</label> 
            <input type="text" name="location" required>
        </p>
        <p>
            <input type="submit" name="insertToyResellerBtn" value="Insert Toy Reseller">
        </p>
    </form>
</div>
<hr><hr>
<a href="view_audit_logs.php" class="button">View Audit Logs</a>
<hr>
<a href="view_users.php" class="button">View Users</a>
<hr> 
<hr>
<div class="table_container">
    <h2>Toy Resellers</h2>
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Date of Birth</th>
                <th>Location</th>
                <th>Date Added</th>
                <th>Last Updated</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $getAllToyResellers = getAllToyResellers($pdo); 
            if (empty($getAllToyResellers)) {
                echo "<tr><td colspan='10'>No toy resellers found.</td></tr>";
            } else {
                foreach ($getAllToyResellers as $row) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['age']); ?></td>
                        <td><?php echo htmlspecialchars($row['gender']); ?></td>
                        <td><?php echo htmlspecialchars($row['date_of_birth']); ?></td>
                        <td><?php echo htmlspecialchars($row['location']); ?></td>
                        <td><?php echo htmlspecialchars($row['date_added']); ?></td>
                        <td><?php echo htmlspecialchars($row['last_updated']); ?></td>
                        <td>
                            <div class="editAndDelete">
                                <a href="view_toy.php?toy_reseller_id=<?php echo $row['toy_reseller_id']; ?>">View Toys</a>
                                <a href="edit_reseller.php?toy_reseller_id=<?php echo $row['toy_reseller_id']; ?>">Edit</a>
                                <a href="deletereseller.php?toy_reseller_id=<?php echo $row['toy_reseller_id']; ?>">Delete</a>
                            </div>
                        </td>
                    </tr>
                <?php }
            } ?>
        </tbody>
    </table>
</div>
<hr>
<div class="table_container">
    <h2>List of Toys and Their Owners</h2>
    <table>
        <thead>
            <tr>
                <th>Toy ID</th>
                <th>Toy Name</th>
                <th>Toy Type</th>
                <th>Date Added</th>
                <th>Last Updated</th>
                <th>Toy Owner</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $toysWithOwners = getAllToysWithOwner($pdo); // Ensure this function is defined in models.php
            if (empty($toysWithOwners)) {
                echo "<tr><td colspan='6'>No toys found.</td></tr>";
            } else {
                foreach ($toysWithOwners as $toy) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($toy['toy_id']); ?></td>
                        <td><?php echo htmlspecialchars($toy['toy_name']); ?></td>
                        <td><?php echo htmlspecialchars($toy['toy_type']); ?></td>
                        <td><?php echo htmlspecialchars($toy['date_added']); ?></td>
                        <td><?php echo htmlspecialchars($toy['last_updated']); ?></td>
                        <td><?php echo htmlspecialchars($toy['toy_owner']); ?></td>
                    </tr>
                <?php }
            } ?>
        </tbody>
    </table>
</div>
</body>
</html>
