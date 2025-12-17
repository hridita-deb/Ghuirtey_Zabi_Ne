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

$spot_id = 0;
$name = '';
$location = '';
$description = '';
$image = '';
$page_title = 'Add New Tourist Spot';
$action = 'create'; // Default action

// Check if an ID is passed (meaning we are UPDATE-ing)
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $spot_id = $_GET['id'];
    $page_title = 'Edit Tourist Spot (ID: ' . $spot_id . ')';
    $action = 'update';

    // Fetch existing data using Prepared Statements
    $sql = "SELECT * FROM $tableName WHERE spot_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $spot_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $data = $result->fetch_assoc();
        $name = $data['name'];
        $location = $data['location'];
        $description = $data['description'];
        $image = $data['image'];
    } else {
        header("Location: admin_spots.php?status=notfound");
        exit();
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?></title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
        }

        .form-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .form-container h2 {
            text-align: center;
            color: #2c3e50;
            border-bottom: 3px solid #3498db;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #34495e;
            font-size: 0.95em;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; /* Ensures padding doesn't affect total width */
            font-size: 1em;
            transition: border-color 0.2s;
        }

        input[type="text"]:focus,
        textarea:focus {
            border-color: #3498db;
            outline: none;
        }

        textarea {
            resize: vertical;
        }
        
        /* --- Button Styling --- */
        .action-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 25px;
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
            text-align: center;
        }

        .btn-submit { /* Submit/Update Button */
            background-color: #2ecc71;
            color: white;
            flex-grow: 1; /* Make it take up the remaining space */
            margin-right: 10px;
        }

        .btn-submit:hover {
            background-color: #27ae60;
        }

        .btn-cancel { /* Cancel/Back Link */
            background-color: #95a5a6;
            color: white;
            padding: 10px 20px;
        }

        .btn-cancel:hover {
            background-color: #7f8c8d;
        }
    </style>
    </head>
<body>

    <div class="form-container">
        <h2><?php echo $page_title; ?></h2>

        <form action="crud_handler.php" method="POST">
            
            <input type="hidden" name="action" value="<?php echo $action; ?>">
            <input type="hidden" name="spot_id" value="<?php echo $spot_id; ?>">

            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>

            <label for="location">Location:</label>
            <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($location); ?>" required>
            
            <label for="image">Image File Name (e.g., image.jpg - stored in Images folder):</label>
            <input type="text" id="image" name="image" value="<?php echo htmlspecialchars($image); ?>" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="5" required><?php echo htmlspecialchars($description); ?></textarea>
            
            <div class="action-group">
                <button type="submit" class="btn btn-submit"><?php echo ($action == 'create' ? 'Create New Spot' : 'Save Changes'); ?></button>
                <a href="admin_spots.php" class="btn btn-cancel">Go Back</a>
            </div>
        </form>
    </div>
</body>
</html>