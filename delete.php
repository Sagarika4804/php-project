<?php
session_start();
$conn = new mysqli("localhost", "root", "", "blog");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}


$role = $_SESSION['role'] ?? 'user';

if (isset($_POST['delete'])) {
    $id = (int)$_POST['id'];


    if ($role != 'admin') {
        $stmt = $conn->prepare("SELECT user_id FROM posts WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $post = $result->fetch_assoc();
        $stmt->close();

        if (!$post || $post['user_id'] != $_SESSION['user_id']) {
            die("⛔ You are not authorized to delete this post.");
        }
    }

    
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: read_posts.php?msg=Post+deleted+successfully");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>

<h2>Delete Post</h2>
<form method="post">
    <label>Enter Post ID:</label><br>
    <input type="number" name="id" required><br><br>
    <button type="submit" name="delete">Delete Post</button>
</form>
<a href="read_posts.php">Back to Posts</a>
