<?php
session_start();
$conn = new mysqli("localhost", "root", "", "blog");

// Check DB connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Redirect if already logged in
if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {
    header("Location: posts.php");
    exit;
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Check if username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "⚠ Username already taken.";
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert user (id will auto-increment in MySQL)
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashedPassword);

        if ($stmt->execute()) {
            $success = "✅ Registration successful! <a href='login.php'>Login here</a>";
        } else {
            $error = "❌ Error: " . $conn->error;
        }
    }
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        form { max-width: 300px; margin: auto; }
        input { width: 100%; padding: 10px; margin: 5px 0; }
        button { padding: 10px 20px; width: 100%; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>

<h2>Register</h2>

<?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
<?php if (!empty($success)) echo "<p class='success'>$success</p>"; ?>

<form method="POST">
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Register</button>
</form>

<p>Already have an account? <a href="login.php">Login here</a></p>

</body>
</html>
