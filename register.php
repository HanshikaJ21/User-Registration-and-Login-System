<?php
// Database connection details
$host = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password is empty
$database = "users"; // Replace with your actual database name

// Create a connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check the connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Validate input (you can add more validation as needed)
    if (empty($username) || empty($email) || empty($password)) {
        echo "All fields are required.";
    } else {
        // Hash the password using bcrypt
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert user data into the database
        $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashedPassword')";

        if (mysqli_query($conn, $sql)) {
            // Registration successful message with link to login page
            echo '<div style="text-align: center; padding: 20px; background-color: #f0f0f0;">';
            echo '<img src="success_image.gif" alt="Success Image" style="width: 150px; height: 150px; border-radius: 50%;">';
            echo '<h2>Registration successful!</h2>';
            echo '<p>Thank you for registering. You can now <a href="login.html">log in</a>.</p>';
            echo '</div>';
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }
}

// Close the connection
mysqli_close($conn);
?>
