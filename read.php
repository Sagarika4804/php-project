<?php
session_start();
$conn = new mysqli("localhost", "root", "", "blog");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$post = null;
$error = "";

if (isset($_POST['read'])) {
    $id = (int)$_POST['id']; 

    $stmt = $conn->prepare("SELECT id, title, content, created_at FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $post = $result->fetch_assoc();
    } else {
        $error = "No post found with ID: " . htmlspecialchars($id);
    }

    $stmt->close();
}

$conn->close();
?>

<h2>Read Post by ID</h2>

<?php if(!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

<form method="post">
    <label>Enter Post ID:</label><br>
    <input type="number" name="id" required><br><br>
    <button type="submit" name="read">Read Post</button>
</form>

<?php if ($post): ?>
    <h3>Post Details</h3>
    <p><strong>ID:</strong> <?= htmlspecialchars($post['id']) ?></p>
    <p><strong>Title:</strong> <?= htmlspecialchars($post['title']) ?></p>
    <p><strong>Content:</strong> <?= nl2br(htmlspecialchars($post['content'])) ?></p>
    <p><strong>Created At:</strong> <?= htmlspecialchars($post['created_at']) ?></p>
<?php endif; ?>
