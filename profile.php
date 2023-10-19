<?php
// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

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

// Get user data from the session
$user_id = $_SESSION['user_id'];

// Retrieve user information from the database
$sql = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);

    // Check if the user has a profile image
    $profileImage = "uploads/" . $user['id'] . ".jpg"; // Assuming profile images are in JPG format

    // If the file exists, use it as the profile picture
    if (file_exists($profileImage)) {
        $profilePicture = $profileImage;
    } else {
        // Default profile picture if no custom image is found
        $profilePicture = "default.png"; // Replace with your default image filename
    }
} else {
    echo "User not found.";
    exit();
}

// Handle image upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_image"])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["profile_image"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["profile_image"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        // If everything is ok, try to upload file
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            // Rename the uploaded file to associate it with the user
            $newProfileImage = "uploads/" . $user['id'] . ".jpg";
            rename($target_file, $newProfileImage);
            $profilePicture = $newProfileImage;
            echo "The file " . htmlspecialchars(basename($_FILES["profile_image"]["name"])) . " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}

// Close the connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
        body {
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        #header {
            text-align: center;
            margin-bottom: 20px;
        }
        #profile-info {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: left;
        }
        #logout-btn {
            background-color: #FF6347; /* Tomato color */
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }
        #image-upload-form {
            margin-top: 20px;
        }
        #profile-picture {
            max-width: 100%;
            border-radius: 50%;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div id="header">
        <h2>Welcome, <?php echo $user['username']; ?>!</h2>
    </div>
    <div id="profile-info">
        <h3>Your Profile Information</h3>
        <p><strong>Username:</strong> <?php echo $user['username']; ?></p>
        <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
        <!-- Display profile picture -->
        <img id="profile-picture" src="<?php echo $profilePicture; ?>" alt="Profile Picture">
    </div>

    <!-- Logout button -->
    <form action="logout.php" method="post">
        <button id="logout-btn" type="submit">Logout</button>
    </form>

    <!-- Image upload form -->
    <div id="image-upload-form">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
            <label for="profile_image">Update Profile Image:</label>
            <input type="file" name="profile_image" accept="image/*">
            <button type="submit">Upload Image</button>
        </form>
    </div>
</body>
</html>
