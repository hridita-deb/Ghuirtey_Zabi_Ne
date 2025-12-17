<?php
header('Content-Type: application/json');

// --- 1. Database Configuration ---
$servername = "localhost"; // Your database server name
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "ghuirtey_zabi_ne"; // Replace with your actual database name

// --- 2. Get Search Query ---
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

// Function to safely execute the search
function getSearchResults($conn, $query) {
    if (empty($query)) {
        return [];
    }

    // Sanitize the input for the LIKE clause
    $search_term = "%" . $query . "%";

    // SQL to search the correct columns (name, location)
    // We use AS (aliases) to rename the columns for the JavaScript to understand
    $sql = "SELECT 
                name AS spot_name, 
                location AS district_name, 
                description, 
                image AS image_url 
            FROM tourist_spots 
            WHERE name LIKE ? OR location LIKE ?
            LIMIT 10"; // Limit results for performance

    // --- Prepare and Execute Statement ---
    $stmt = $conn->prepare($sql);
    
    // Bind parameters: 'ss' for two strings ($search_term, $search_term)
    // The first $search_term binds to 'name LIKE ?'
    // The second $search_term binds to 'location LIKE ?'
    if (!$stmt->bind_param("ss", $search_term, $search_term)) {
        // Handle error in binding if necessary
        error_log("Binding parameters failed: " . $stmt->error);
        return [];
    }
    
    $stmt->execute();
    $result = $stmt->get_result();

    $spots = [];
    while ($row = $result->fetch_assoc()) {
        $spots[] = $row;
    }
    
    $stmt->close();
    return $spots;
}

// --- 3. Database Connection and Execution ---
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    // Return an HTTP 500 status and an error message if connection fails
    http_response_code(500);
    // Remove $conn->connect_error for production security, but helpful for debugging
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]); 
    exit();
}

// Check if the tourist_spots table exists or if there are any other DB-related errors
$results = getSearchResults($conn, $query);

$conn->close();

// --- 4. Return Results as JSON ---
echo json_encode($results);
?>