<html><head><link rel="stylesheet" href="gallery.css">
</head></html>
<!-- gallery.php -->
<?php

// Fetch saved drawings from the database
$host = 'localhost';
$db = 'paint_app';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, username, drawing_data FROM drawings";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo '<div class="gallery">'; // Wrap everything inside a gallery div
    while ($row = $result->fetch_assoc()) {
        echo '<div class="drawing">';
        echo '<h3>' . htmlspecialchars($row['username']) . '</h3>';
        echo '<a href="edit_drawing.php?drawing_id=' . $row['id'] . '">';
        echo '<img src="' . $row['drawing_data'] . '" alt="Drawing" class="drawing-thumbnail"/>';
        echo '</a>';
        echo '</div>';
    }
    echo '</div>';
} else {
    echo "<p>No drawings found.</p>";
}

$conn->close();
?>
