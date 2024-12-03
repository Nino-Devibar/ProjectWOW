// script.js

const canvas = document.getElementById('canvas');
const ctx = canvas.getContext('2d');
let drawing = false;
let color = document.getElementById('color').value;
let thickness = document.getElementById('thickness').value;

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

// Clear canvas
function clearCanvas() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
}

// Save drawing to backend
function saveDrawing() {
    const dataUrl = canvas.toDataURL(); // Get the drawing data in Base64 format
    document.getElementById('saveModal').style.display = 'flex'; // Show the modal overlay

    // Handle the form submission for saving the drawing
    document.getElementById('saveForm').onsubmit = function(e) {
        e.preventDefault(); // Prevent the default form submission

        const username = document.getElementById('username').value;
        const passcode = document.getElementById('passcode').value;

        // Validate user input
        if (username.length === 0 || username.length > 16) {
            alert('Username must be between 1 and 16 characters.');
            return;
        }

        if (!/^\d{4}$/.test(passcode)) {
            alert('Passcode must be exactly 4 digits.');
            return;
        }

        // Prepare form data to be sent to the backend
        const formData = new FormData();
        formData.append('image', dataUrl); // Base64 image data
        formData.append('username', username); // User's name
        formData.append('passcode', passcode); // User's passcode

        // Send the data to the PHP backend using fetch
        fetch('saved_drawings.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json()) // Parse response as JSON
        .then(data => {
            if (data.success) {
                alert('Drawing saved successfully!');
                window.location.href = 'gallery.php'; // Redirect to gallery page
            } else {
                alert('Error: ' + data.error); // Show error from PHP
            }
        })
        .catch(error => {
            console.error('Error during fetch operation:', error);
            alert('An error occurred while saving the drawing. Please try again.');
        });
    };

    // Close the modal if the user clicks the close button
    document.getElementById('closeModal').onclick = function() {
        document.getElementById('saveModal').style.display = 'none'; // Hide the modal
    };
}
