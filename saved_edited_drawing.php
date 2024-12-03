<?php
// Capture POST data
$image = $_POST['image'] ?? null;
$username = $_POST['username'] ?? null;
$passcode = $_POST['passcode'] ?? null;

// Ensure passcode is provided
if (empty($passcode)) {
    echo json_encode(['success' => false, 'error' => 'Passcode is required.']);
    exit;
}

// Step 2: Get the drawing details from the database
$host = 'localhost';
$db = 'paint_app';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch the passcode from the database for the username
$sql = "SELECT passcode FROM drawings WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->store_result();

// Check if we have a matching user
if ($stmt->num_rows > 0) {
    // Bind the result to a variable
    $stmt->bind_result($db_passcode);
    $stmt->fetch();
} else {
    echo json_encode(['success' => false, 'error' => 'User not found']);
    exit;
}

$stmt->close();

// Step 3: Compare the passcodes
if ($passcode === $db_passcode) {
    // Passcode matches, proceed to save the drawing
    // Prepare the SQL statement to update the drawing data
    $sql = "UPDATE drawings SET drawing_data = ? WHERE username = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        // Log SQL prepare error
        error_log("SQL prepare error: " . $conn->error);
        echo json_encode(['success' => false, 'error' => 'SQL prepare error']);
        exit;
    }

    // Bind the parameters
    $stmt->bind_param('ss', $image, $username);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Drawing saved successfully.']);
    } else {
        // Log any SQL execution errors
        error_log("SQL execution error: " . $stmt->error);
        echo json_encode(['success' => false, 'error' => 'Failed to save drawing.']);
    }

    $stmt->close();
} else {
    // Passcodes do not match
    echo json_encode(['success' => false, 'error' => 'Invalid passcode']);
}

$conn->close();
?>
