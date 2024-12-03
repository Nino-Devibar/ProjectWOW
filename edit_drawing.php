<?php
// Get the drawing ID from the URL
if (isset($_GET['drawing_id'])) {
    $drawing_id = $_GET['drawing_id'];
} else {
    die("Drawing ID is missing.");
}

// Step 2: Get the drawing details from the database
$host = 'localhost';
$db = 'paint_app';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, username, passcode, drawing_data FROM drawings WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $drawing_id);
$stmt->execute();
$stmt->store_result();

// Check if drawing exists
if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $username, $passcode, $drawing_data);
    $stmt->fetch();
} else {
    die("Drawing not found.");
}

$stmt->close();
$conn->close();

// Trim passcode to avoid issues with spaces
$correctPasscode = trim($passcode); // Trim any leading/trailing spaces from the passcode
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Drawing</title>
    <link rel="stylesheet" href="styles.css"> <!-- Your CSS file -->
</head>
<body>
    <h1>Edit Drawing by <?php echo htmlspecialchars($username); ?></h1>
    
    <!-- Passcode Form -->
    <div>
        <form id="passcodeForm">
            <label for="passcode">Enter Passcode:</label>
            <input type="text" id="passcodeInput" required maxlength="4" placeholder="4-digit passcode">
            <button type="submit">Verify</button>
        </form>
    </div>
    
    <!-- Canvas (Initially hidden until passcode is correct) -->
    <div id="canvasWrapper" style="display: none;">
        <canvas id="canvas" width="800" height="600"></canvas>
    </div>

    <!-- Save Drawing Button (Initially hidden) -->
    <div id="saveWrapper" style="display: none;">
        <button id="saveButton">Save Drawing</button>
    </div>

    <script>
        window.onload = function() {
    const passcodeForm = document.getElementById('passcodeForm');
    const passcodeInput = document.getElementById('passcodeInput');
    const canvasWrapper = document.getElementById('canvasWrapper');
    const saveWrapper = document.getElementById('saveWrapper');
    const saveButton = document.getElementById('saveButton');
    const canvas = document.getElementById('canvas');
    const ctx = canvas.getContext('2d');

    // The passcode from the database
    const correctPasscode = '<?php echo $correctPasscode; ?>';
    const drawingData = '<?php echo $drawing_data; ?>';

    // Allow drawing on canvas
    let drawing = false;
    let color = 'black';  // Default color
    let thickness = 2;    // Default thickness

    // Event listeners for drawing on canvas
    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('mouseout', stopDrawing);

    // Drawing functions
    function startDrawing(e) {
        drawing = true;
        draw(e);
    }

    function stopDrawing() {
        drawing = false;
        ctx.beginPath();
    }

    function draw(e) {
        if (!drawing) return;

        ctx.lineWidth = thickness;
        ctx.lineCap = 'round';
        ctx.strokeStyle = color;

        ctx.lineTo(e.clientX - canvas.offsetLeft, e.clientY - canvas.offsetTop);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(e.clientX - canvas.offsetLeft, e.clientY - canvas.offsetTop);
    }

    // Handle passcode form submission
    passcodeForm.addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent page refresh when submitting the form

        const enteredPasscode = passcodeInput.value.trim(); // Trim the entered passcode

        // Verify the passcode
        if (enteredPasscode === correctPasscode) {
            alert('Passcode correct! You can now edit the drawing.');

            // Load the drawing onto the canvas (Base64 string)
            const image = new Image();
            image.onload = function() {
                ctx.drawImage(image, 0, 0); // Draw the image on canvas
                canvasWrapper.style.display = 'block'; // Show the canvas
                saveWrapper.style.display = 'block'; // Show the save button
            };
            image.src = drawingData; // Set the Base64 string from PHP

        } else {
            alert('Incorrect passcode. Try again.');
        }
    });

    // Handle saving the edited drawing
    saveButton.addEventListener('click', function() {
    const drawingData = canvas.toDataURL(); // Get the Base64 image data
    const username = '<?php echo $username; ?>'; // Use the same username from the database
    const passcode = prompt("Enter your passcode to save this drawing:");

    // Validate passcode
    if (!passcode || passcode.length !== 4 || isNaN(passcode)) {
        alert('Invalid passcode. Please try again.');
        return;
    }

    // Trim passcode to ensure no extra spaces
    const trimmedPasscode = passcode.trim();

    // Log to check what data is being sent
    console.log("Sending the following data to backend:");
    console.log("Drawing Data:", drawingData);
    console.log("Username:", username);
    console.log("Passcode:", trimmedPasscode);

    // Send the drawing data to the backend to save it
    const formData = new FormData();
    formData.append('image', drawingData); // Send the Base64 drawing data
    formData.append('username', username); // Send the username
    formData.append('passcode', trimmedPasscode); // Send the trimmed passcode

    fetch('saved_edited_drawing.php', {  // Ensure this path is correct
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Drawing saved successfully!');
            window.location.href = 'gallery.php'; // Redirect to gallery after saving
        } else {
            alert('Error saving the drawing: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error during fetch operation:', error);
        alert('An error occurred while saving the drawing. Please try again.');
    });
});
        }
    </script>
</body>
</html>
