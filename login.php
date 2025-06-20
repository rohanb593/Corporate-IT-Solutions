<?php
session_start();

// Database configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = 'root';
$db_name = 'Corporate IT Solutions';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['city'])) {
        header("Location: loginDisplay.php?error=empty_fields");
        exit();
    }

    // Sanitize user input
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    $city = $conn->real_escape_string($_POST['city']);

    // Verify city exists
    $city_check = $conn->prepare("SELECT city_name FROM zambian_cities WHERE city_name = ?");
    $city_check->bind_param("s", $city);
    $city_check->execute();
    $city_result = $city_check->get_result();

    if ($city_result->num_rows === 0) {
        header("Location: loginDisplay.php?error=invalid_city");
        exit();
    }

    // Check user credentials
    $stmt = $conn->prepare("SELECT id, username, password FROM USERS WHERE username = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['city'] = $city;
            
            // Redirect to success page
            header("Location: homePage.php");
            exit();
        } else {
            header("Location: loginDisplay.php?error=invalid_password");
            exit();
        }
    } else {
        header("Location: loginDisplay.php?error=user_not_found");
        exit();
    }
} else {
    header("Location: loginDisplay.php?error=invalid_method");
    exit();
}

$conn->close();
?>