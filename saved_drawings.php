<?php
// Enable error reporting to display all errors and warnings
error_reporting(E_ALL); 
ini_set('display_errors', 1);

$host = 'localhost';
$db = 'paint_app';    // Ensure this matches the database you created
$user = 'root';       // MySQL username (use 'root' if you're using XAMPP, MAMP, etc.)
$pass = '';           // MySQL password (use empty string if there's no password)

$conn = new mysqli($host, $user, $pass, $db);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the request is a POST request (this ensures the form is submitted via POST method)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve input data from the form
    $username = $_POST['username'];
    $passcode = $_POST['passcode'];
    $drawing_data = $_POST['image'];

    // Validate input (ensure username is <= 16 characters and passcode is a 4-digit number)
    if (strlen($username) > 16 || !preg_match('/^\d{4}$/', $passcode)) {
        // Return error if validation fails
        echo json_encode(['success' => false, 'error' => 'Invalid input. Ensure username is <= 16 characters and passcode is 4 digits.']);
        exit();
    }

    // Prepare and execute the SQL query to insert the drawing data into the database
    $stmt = $conn->prepare("INSERT INTO drawings (username, passcode, drawing_data) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $username, $passcode, $drawing_data);

    // Check if the query was executed successfully
    if ($stmt->execute()) {
        // Return success response in JSON format
        echo json_encode(['success' => true]);
    } else {
        // Return an error if the query failed
        echo json_encode(['success' => false, 'error' => 'Database error. Failed to save the drawing.']);
    }

    // Close the statement and the database connection
    $stmt->close();
    $conn->close();
} else {
    // If the request method is not POST, return an error
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
?>
