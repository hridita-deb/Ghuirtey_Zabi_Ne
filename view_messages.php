<?php


// === Protection Code to Ensure Admin is Logged In ===

session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: admin_login.php");
    exit;
}






// === DB CONNECTION BLOCK (Copied from contact.php) ===
$servername = "localhost";
$username = "root"; 
$password = "";
$dbname = "ghuirtey_zabi_ne"; 
$tableName = "contact_messages"; // Ensure this matches your table name

// 1. Establish Database Connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// ======================================================

// Handle Delete Message Logic
if(isset($_GET['delete_msg']) && is_numeric($_GET['delete_msg'])){
    $msg_id = $_GET['delete_msg'];
    
    // Use Prepared Statement for secure deletion
    $stmt = $conn->prepare("DELETE FROM $tableName WHERE id = ?");
    $stmt->bind_param("i", $msg_id);
    $stmt->execute();
    $stmt->close();
    
    // Redirect back after deletion
    header("location: view_messages.php");
    exit(); // Always use exit after header redirect
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Contact Messages</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; padding: 0; background: #f0f2f5; }
        
        /* Navbar CSS (Inline styles converted to class) */
        .admin-nav { background: #34495e; padding: 15px 30px; margin-bottom: 25px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .admin-nav a { color: white; text-decoration: none; margin-right: 25px; font-weight: bold; transition: color 0.2s; }
        .admin-nav a:hover { color: #3498db; }
        
        .message-container { max-width: 1200px; margin: 0 auto; padding: 0 30px; }
        
        h2 { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; margin-bottom: 20px; }
        
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 40px; }
        th, td { padding: 15px; border-bottom: 1px solid #eee; text-align: left; vertical-align: top; }
        th { background: #34495e; color: white; }
        tr:hover { background-color: #f9f9f9; }
        
        .delete-btn { color: #e74c3c; text-decoration: none; font-weight: bold; border: 1px solid #e74c3c; padding: 5px 10px; border-radius: 3px; display: inline-block; }
        .delete-btn:hover { background: #e74c3c; color: white; }
        .date-text { font-size: 0.85em; color: #7f8c8d; white-space: nowrap; }
        .message-content { max-width: 350px; }
    </style>
</head>
<body>

    <div class="admin-nav">
        <a href="admin_spots.php"> Manage Tourist Spots</a>
        <a href="view_messages.php"> View Messages</a>
        <a href="admin_logout.php" style="float: right; color: #ff6b6b; font-weight: bold;"> Logout</a>
    </div>
    
    <div class="message-container">
        <h2>Customer Inquiries</h2>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sender Info</th>
                    <th>Subject</th>
                    <th class="message-content">Message</th>
                    <th>Date Received</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetching data using your exact column names
                $result = $conn->query("SELECT * FROM $tableName ORDER BY created_at DESC");
                
                if($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($row['name']); ?></strong><br>
                                <span style="color: #666; font-size: 0.9em;"><?php echo htmlspecialchars($row['email']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($row['subject']); ?></td>
                            <td class="message-content"><?php echo nl2br(htmlspecialchars($row['message'])); ?></td>
                            <td class="date-text"><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td>
                                <a href="view_messages.php?delete_msg=<?php echo $row['id']; ?>" 
                                   class="delete-btn" 
                                   onclick="return confirm('Delete message from <?php echo htmlspecialchars($row['name']); ?>?')">Delete</a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align:center;'>No messages found in database.</td></tr>";
                }
                
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

</body>
</html>
