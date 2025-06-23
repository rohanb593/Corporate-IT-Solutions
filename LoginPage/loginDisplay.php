<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "Corporate IT Solutions";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get all Zambian cities for the dropdown
$cities = array();
$sql = "SELECT city_name FROM zambian_cities ORDER BY city_name ASC";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $cities[] = $row['city_name'];
    }
    $result->free();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="login.css">
</head>
<body>
    <header>Corporate IT Solutions</header>
    <section>
        <div class="outer-card">
            <form action="login.php" method="POST">
                <h2>Login</h2>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="error-message" style="color: red; margin-bottom: 15px;">
                        <?php 
                        $errors = [
                            'empty_fields' => 'Please fill all fields',
                            'invalid_password' => 'Invalid password',
                            'user_not_found' => 'User not found',
                            'invalid_method' => 'Invalid request method',
                            'invalid_city' => 'Invalid city selection'
                        ];
                        echo $errors[$_GET['error']] ?? 'An error occurred';
                        ?>
                    </div>
                <?php endif; ?>

                <label>Username:</label>
                <input type="text" name="username" required>

                <label>Password:</label>
                <input type="password" name="password" required>

                <label>Select your city:</label>
                <select name="city" required>
                    <option value="">Select your city</option>
                    <?php foreach ($cities as $city): ?>
                        <option value="<?php echo htmlspecialchars($city); ?>">
                            <?php echo htmlspecialchars($city); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <input type="submit" value="Login">
                
                <p style="margin-top: 15px; text-align: center;">
                    Don't have an account? <a href="signup.html" style="color: #ff6b00;">Sign up</a>
                </p>
            </form>
        </div>
    </section>
</body>
</html>