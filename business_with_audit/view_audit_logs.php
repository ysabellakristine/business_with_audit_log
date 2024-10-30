<?php
require_once 'main/dbConfig.php';
require_once 'main/models.php'; 


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}

// Prepare and execute the SQL query to fetch audit logs
$stmt = $pdo->prepare("SELECT a.log_id, u.username, a.action_type, a.action_details, a.timestamp 
                        FROM audit_logs a 
                        JOIN users u ON a.user_id = u.user_id 
                        ORDER BY a.timestamp ASC");
$stmt->execute();
$auditLogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if there are audit logs
$message = $stmt->rowCount() === 0 ? "No audit logs found." : "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    
    <h1>Audit Logs</h1>
    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <div class="table_Container">
        <table border="1">
            <thead>
                <tr>
                    <th>Log ID</th>
                    <th>Username</th>
                    <th>Action Type</th>
                    <th>Action Details</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($auditLogs as $log): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($log['log_id']); ?></td>
                        <td><?php echo htmlspecialchars($log['username']); ?></td>
                        <td><?php echo htmlspecialchars($log['action_type']); ?></td>
                        <td><?php echo htmlspecialchars($log['action_details']); ?></td>
                        <td><?php echo htmlspecialchars($log['timestamp']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<hr> <hr>
    <a href="view_users.php" class="button">View Users</a>
    <p><a href="index.php">RETURN</a></p>
</body>
</html>
