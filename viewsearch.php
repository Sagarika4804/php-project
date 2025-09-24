<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "blog";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$limit = 5; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // ensure page >= 1
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$params = [];
$whereClause = "";

if ($search !== "") {
    $whereClause = "WHERE title LIKE ? OR content LIKE ?";
    $searchParam = "%$search%";
    $params = [$searchParam, $searchParam];
}

// Count total posts
$countSql = "SELECT COUNT(*) AS total FROM posts $whereClause";
$stmt = $conn->prepare($countSql);
if (!empty($params)) $stmt->bind_param("ss", ...$params);
$stmt->execute();
$countResult = $stmt->get_result();
$totalPosts = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalPosts / $limit);
$stmt->close();

// Fetch paginated posts
$sql = "SELECT * FROM posts $whereClause ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param("ssii", ...$params, $limit, $offset);
} else {
    $stmt->bind_param("ii", $limit, $offset);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Posts</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<h2 class="mb-4">Posts</h2>

<form method="GET" class="d-flex mb-4">
    <input type="text" name="search" class="form-control me-2" placeholder="Search posts..." value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit" class="btn btn-primary">Search</button>
</form>

<?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='card mb-3'>";
        echo "<div class='card-body'>";
        echo "<h5 class='card-title'>" . htmlspecialchars($row['title']) . "</h5>";
        echo "<p class='card-text'>" . nl2br(htmlspecialchars($row['content'])) . "</p>";
        echo "<small class='text-muted'>Posted on " . htmlspecialchars($row['created_at']) . "</small>";
        echo "</div>";
        echo "</div>";
    }
} else {
    echo "<p>No posts found.</p>";
}
$stmt->close();
$conn->close();
?>

<nav>
  <ul class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>">
          <?php echo $i; ?>
        </a>
      </li>
    <?php endfor; ?>
  </ul>
</nav>

</body>
</html>
