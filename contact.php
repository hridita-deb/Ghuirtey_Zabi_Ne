<?php
// 1. Database Configuration
// !!! IMPORTANT: Replace these placeholder values with your actual database credentials !!!
// contact.php (Updated Credentials for XAMPP Default)
$servername = "localhost";
$username = "root"; // Set to XAMPP default root user
$password = "";    // Set to XAMPP default empty password (CRITICAL)
$dbname = "ghuirtey_zabi_ne"; // <-- Keep YOUR actual database name here







// Table name in your database
$tableName = "contact_messages";

// 2. Establish Database Connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Stop script execution and display an error message if connection fails
    die("Connection failed: " . $conn->connect_error);
}

// 3. Process Form Data
// Check if the form was submitted using the POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 4. Sanitize and Validate Input (CRITICAL for security)
    // Use $conn->real_escape_string to prevent SQL Injection
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $subject = $conn->real_escape_string($_POST['subject']);
    $message = $conn->real_escape_string($_POST['message']);

    // Generate the current timestamp for 'created_at' column
    $created_at = date('Y-m-d H:i:s');
    
    // Simple validation (can be more extensive)
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        echo "Error: All fields are required.";
        exit;
    }

    // 5. Construct the SQL INSERT Query
    $sql = "INSERT INTO $tableName (name, email, subject, message, created_at)
            VALUES ('$name', '$email', '$subject', '$message', '$created_at')";

    // 6. Execute the Query and Check for Success
    if ($conn->query($sql) === TRUE) {
        // Success message and redirect the user back to the contact page or a thank you page
        echo "Thank you for your message! We will be in touch shortly.";
        // Optional: Redirect to a success page after 3 seconds
        // header("refresh:3; url=contact.html"); 
    } else {
        // Error handling if the query fails
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    // If someone tries to access contact.php directly without form submission
    echo "Invalid request method.";
}

// 7. Close the Database Connection
$conn->close();
?>