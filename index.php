<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paint App</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h1>Paint App</h1>

<!-- Canvas area to draw -->
<canvas id="canvas" width="600" height="400"></canvas>

<div class="controls">
    <label for="color">Color:</label>
    <input type="color" id="color" value="#000000">
    
    <label for="thickness">Thickness:</label>
    <input type="range" id="thickness" min="1" max="10" value="3">
    
    <button onclick="clearCanvas()">Clear</button>
    <button onclick="saveDrawing()">Save</button>
</div>

<!-- Save Drawing Modal -->
<div id="saveModal" class="modal">
    <div class="modal-content">
        <form id="saveForm">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" maxlength="16" required>
            <br>
            <label for="passcode">Passcode (4 digits):</label>
            <input type="text" id="passcode" name="passcode" maxlength="4" required pattern="\d{4}">
            <br>
            <button type="submit">Save Drawing</button>
        </form>
        <button id="closeModal">Close</button>
    </div>
</div>


<script src="script.js"></script>

</body>
</html>
