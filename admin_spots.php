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
$tableName = "tourist_spots"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// ======================================================

// --- READ Operation ---
$sql = "SELECT * FROM $tableName ORDER BY spot_id DESC";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Tourist Spots</title>
    
   <style>
    /* --- General Styles --- */
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f7f6;
        margin: 0;
        padding: 0;
    }

    .admin-nav { 
        background: #34495e; 
        padding: 15px 30px; 
        margin-bottom: 25px; 
        box-shadow: 0 2px 5px rgba(0,0,0,0.1); 
    }
    .admin-nav a { 
        color: white; 
        text-decoration: none; 
        margin-right: 25px; 
        font-weight: bold; 
        transition: color 0.2s; 
    }
    .admin-nav a:hover { 
        color: #3498db; 
    }

    .admin-container {
        /* Set a specific max-width and center it */
        max-width: 1200px;
        margin: 40px auto;
        padding: 30px;
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        /* 💡 FIX: Ensure no content overflows the container */
        overflow-x: auto; 
    }

    .admin-container h2 {
        text-align: center;
        color: #2c3e50;
        border-bottom: 3px solid #3498db;
        padding-bottom: 15px;
        margin-bottom: 25px;
    }

    /* --- Add New Button Styling --- */
    .add-new {
        text-align: right;
        margin-bottom: 20px;
    }

    .btn {
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        display: inline-block;
        transition: background-color 0.2s ease;
        border: none;
        font-size: 1em;
    }

    .btn-primary { 
        background-color: #2ecc71;
        color: white;
    }

    .btn-primary:hover {
        background-color: #27ae60;
    }


    /* --- Table Styles --- */
    .spot-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .spot-table th, .spot-table td {
        padding: 12px 10px; /* Reduced horizontal padding slightly */
        text-align: left;
        border-bottom: 1px solid #ecf0f1;
        vertical-align: top; /* Align all cell content to the top */
    }

    .spot-table thead th {
        background-color: #34495e;
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85em;
    }

    .spot-table tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    
    .spot-table tbody tr:hover {
        background-color: #ecf0f1;
    }

    /* 💡 FIX: Styles for the Image File Column (URLs) */
    .image-file {
         font-family: 'Courier New', monospace;
         font-size: 0.8em;
         /* 🌟 CRITICAL FIX: Force long text/URLs to break and wrap */
         word-wrap: break-word; /* For older browsers */
         word-break: break-all; /* For modern browsers */
         max-width: 200px; /* Give it a controlled maximum width */
    }
    
    /* 💡 FIX: Ensure the header and body columns align vertically */
    .spot-table thead th, .spot-table tbody td {
        /* Remove any conflicting display properties */
        display: table-cell;
    }

    /* --- Action Buttons in Table --- */
    .action-buttons {
        white-space: nowrap; 
    }

    .btn-edit {
        background-color: #f39c12;
        color: white;
        margin-right: 8px;
        padding: 6px 10px;
        font-size: 0.9em;
    }

    .btn-edit:hover {
        background-color: #e67e22;
    }

    .btn-delete {
        background-color: #e74c3c;
        color: white;
        padding: 6px 10px;
        font-size: 0.9em;
    }

    .btn-delete:hover {
        background-color: #c0392b;
    }

    /* --- Special Cell Styles --- */
    .description-snippet {
        font-size: 0.9em;
        color: #7f8c8d;
        max-width: 250px; 
        overflow: hidden;
        text-overflow: ellipsis;
        /* Setting this property prevents forced horizontal expansion */
        word-wrap: break-word;
    }

    .no-data {
        text-align: center !important;
        font-style: italic;
        color: #888;
    }
</style>
    </head>
<body>

<div class="admin-nav">
        <a href="admin_spots.php">📍 Manage Tourist Spots</a>
        <a href="view_messages.php">✉ View Messages</a>
        <a href="admin_logout.php" style="float: right; color: #ff6b6b; font-weight: bold;"> Logout</a>

    </div>
    

    <div class="admin-container">

        <h2>🧭 Manage Tourist Spots (CRUD Dashboard)</h2>

        <p class="add-new">
            <a href="edit_spot.php" class="btn btn-primary">
                + Add New Tourist Spot
            </a>
        </p>

        <table class="spot-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Description (Snippet)</th>
                    <th>Image File</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row["spot_id"]) . "</td>";
                        echo "<td class='spot-name'>" . htmlspecialchars($row["name"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["location"]) . "</td>";
                        $snippet = substr($row["description"], 0, 50);
                        echo "<td class='description-snippet'>" . htmlspecialchars($snippet) . "...</td>";
                        echo "<td class='image-file'>" . htmlspecialchars($row["image"]) . "</td>";
                        echo "<td class='action-buttons'>";
                        
                        // Edit Button
                        echo "<a href='edit_spot.php?id=" . $row["spot_id"] . "' class='btn btn-edit'>Edit</a>";
                        
                        // Delete Form
                        echo "<form method='POST' action='crud_handler.php' style='display:inline;' onsubmit='return confirm(\"Confirm delete spot: " . htmlspecialchars($row["name"]) . "?\");'>";
                        echo "<input type='hidden' name='action' value='delete'>";
                        echo "<input type='hidden' name='spot_id' value='" . $row["spot_id"] . "'>";
                        echo "<button type='submit' class='btn btn-delete'>Delete</button>";
                        echo "</form>";
                        
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='no-data'>No tourist spots found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>