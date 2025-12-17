<?php
session_start();

$servername = "localhost";
$username = "root"; 
$db_password = ""; // Renamed variable to avoid conflict with login password
$dbname = "ghuirtey_zabi_ne"; 

$conn = new mysqli($servername, $username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get data from the login form
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // 1. Updated query to select 'password' instead of 'password_hash'
    $stmt = $conn->prepare("SELECT id, password FROM admin_users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // 2. Simple text comparison (Best for your student project)
        if ($pass === $row['password']) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $user;
            header("location: admin_spots.php");
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Username not found.";
    }
    $stmt->close();
}
$conn->close();
?>