// === This file handles all form submissions ( Create, Update, Delete) for tourist spots ====



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
$tableName = "tourist_spots"; // Your table name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// ======================================================

// Check if an action is set
if (!isset($_POST['action'])) {
    header("Location: admin_spots.php");
    exit();
}

$action = $_POST['action'];

if ($action == 'create') {
    // --- CREATE Operation ---
    // Validate that necessary fields are present
    if (!isset($_POST['name'], $_POST['location'], $_POST['description'], $_POST['image'])) {
        die("Missing form fields for creation.");
    }
    
    $sql = "INSERT INTO $tableName (name, location, description, image) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    // 'ssss' means four string parameters
    $stmt->bind_param("ssss", $_POST['name'], $_POST['location'], $_POST['description'], $_POST['image']);
    
    if ($stmt->execute()) {
        header("Location: admin_spots.php?status=created");
    } else {
        echo "Error creating spot: " . $stmt->error;
    }
    $stmt->close();

} elseif ($action == 'update') {
    // --- UPDATE Operation ---
    if (!isset($_POST['spot_id'], $_POST['name'], $_POST['location'], $_POST['description'], $_POST['image'])) {
        die("Missing form fields for update.");
    }

    $sql = "UPDATE $tableName SET name=?, location=?, description=?, image=? WHERE spot_id=?";
    $stmt = $conn->prepare($sql);
    // 'ssssi' means four strings and one integer (spot_id)
    $stmt->bind_param("ssssi", $_POST['name'], $_POST['location'], $_POST['description'], $_POST['image'], $_POST['spot_id']);
    
    if ($stmt->execute()) {
        header("Location: admin_spots.php?status=updated");
    } else {
        echo "Error updating spot: " . $stmt->error;
    }
    $stmt->close();

} elseif ($action == 'delete') {
    // --- DELETE Operation ---
    if (!isset($_POST['spot_id'])) {
        die("Missing spot ID for deletion.");
    }

    $sql = "DELETE FROM $tableName WHERE spot_id=?";
    $stmt = $conn->prepare($sql);
    // 'i' means one integer (spot_id)
    $stmt->bind_param("i", $_POST['spot_id']);
    
    if ($stmt->execute()) {
        header("Location: admin_spots.php?status=deleted");
    } else {
        echo "Error deleting spot: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Invalid action specified.";
}

$conn->close();
?>