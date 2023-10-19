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
    $username_email = $_POST["username_email"];
    $password = $_POST["password"];

    // Validate input (you can add more validation as needed)
    if (empty($username_email) || empty($password)) {
        echo "All fields are required.";
    } else {
        // Check user credentials
        $sql = "SELECT * FROM users WHERE username = '$username_email' OR email = '$username_email'";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Start session
                session_start();

                // Store user data in session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];

                // Redirect to the profile page
                header("Location: profile.php");
                exit();
            } else {
                echo "Incorrect password.";
            }
        } else {
            echo "User not found.";
        }
    }
}

// Close the connection
mysqli_close($conn);
?>
