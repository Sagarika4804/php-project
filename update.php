<?php
session_start();
$conn = new mysqli("localhost", "root", "", "blog");

if (!isset($_SESSION["loggedin"])) {
    header("Location: login.php");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die("Invalid post ID.");
}


$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();
$stmt->close();

if (!$post) {
    die("Post not found.");
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    $stmt = $conn->prepare("UPDATE posts SET title=?, content=? WHERE id=?");
    $stmt->bind_param("ssi", $title, $content, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: read_posts.php");
    exit;
}

$conn->close();
?>

<h2>Edit Post</h2>
<form method="POST">
    <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required><br>
    <textarea name="content" rows="5" required><?php echo htmlspecialchars($post['content']); ?></textarea><br>
    <button type="submit">Update Post</button>
</form>
<a href="read_posts.php">Back to Posts</a>
