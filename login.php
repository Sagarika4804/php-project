<?php
session_start();
require_once "pdo.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']); 
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['logged_in'] = true;

        
        switch ($_SESSION['role']) {
            case 'admin':
                header("Location: admin.php");
                break;
            case 'editor':
                header("Location: editor.php");
                break;
            case 'user':
            default:
                header("Location: user_dashboard.php");
        }
        exit;
    } else {
        $error = "❌ Invalid username or password!";
    }
}
?>

<h2>Login</h2>
<?php if(!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

<form method="POST">
    Username: <input type="text" name="username" required><br><br>
    Password: <input type="password" name="password" required><br><br>
    <button type="submit">Login</button>
</form>
