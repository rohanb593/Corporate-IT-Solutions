<?php

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "Corporate IT Solutions";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize inputs
    $user = $conn->real_escape_string($_POST['username']);
    $pass1 = $conn->real_escape_string($_POST['password']);
    $pass2 = $conn->real_escape_string($_POST['confirmPassword']);

    // Validate password match
    if ($pass1 !== $pass2) {
        die("Error: Passwords do not match");
    }

    // Hash the password for security
    $hashed_password = password_hash($pass1, PASSWORD_DEFAULT);

    // SQL to insert data
    $sql = "INSERT INTO USERS (username, password) VALUES ('$user', '$hashed_password')";

    // Execute query and check result
    if ($conn->query($sql) === TRUE) {
        // Registration successful - redirect to login page
        header("Location: loginDisplay.php?registration=success&name=" . urlencode($user));
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close connection
    $conn->close();
} else {
    // If someone tries to access this page directly without submitting the form
    header("Location: signup.html");
    exit();
}
?>
