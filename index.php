<?php
session_start();
include 'db_connect.php';

// Redirect to login page if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Handle new post creation
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $module_id = $_POST['module_id'];

    $query = "INSERT INTO posts (title, content, module_id, user_id, created_at) VALUES (:title, :content, :module_id, :user_id, NOW())";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':title', $title);
    $stmt->bindValue(':content', $content);
    $stmt->bindValue(':module_id', $module_id);
    $stmt->bindValue(':user_id', $_SESSION['user_id']);

    try {
        $stmt->execute();
        header('Location: list_posts.php');
        exit;
    } catch (PDOException $e) {
        echo "Error creating post: " . $e->getMessage();
    }
}

// Initialize filters
$category = isset($_GET['category']) && $_GET['category'] !== 'all' ? $_GET['category'] : null;
$date = isset($_GET['date']) ? $_GET['date'] : null;
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : null;

// Construct query to fetch posts with filters
$query = "SELECT posts.*, modules.name AS module_name FROM posts 
          LEFT JOIN modules ON posts.module_id = modules.id WHERE 1=1";
$filters = [];

if ($category) {
    $query .= " AND category = :category";
    $filters[':category'] = $category;
}

if ($date) {
    $query .= " AND DATE(posts.created_at) = :date";
    $filters[':date'] = $date;
}

if ($keyword) {
    $query .= " AND (posts.title LIKE :keyword OR posts.content LIKE :keyword)";
    $filters[':keyword'] = '%' . $keyword . '%';
}

$query .= " ORDER BY posts.created_at DESC";

// Execute the query with filters
try {
    $stmt = $pdo->prepare($query);
    foreach ($filters as $param => $value) {
        $stmt->bindValue($param, $value);
    }
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error fetching posts: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
       body{
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            background-position: top;
            width: 100%;
            height: 100%;
            font-family: Arial, Helvetica;
            letter-spacing: 0.02em;
            font-weight: 400;   
            -webkit-font-smoothing: antialiased; 
            height: 100%;/* max-height: 600px; */
            background-color: hsla(200,40%,30%,4);
            background-image:   
            url('https://78.media.tumblr.com/8cd0a12b7d9d5ba2c7d26f42c25de99f/tumblr_p7n8kqHMuD1uy4lhuo2_1280.png'),
            url('https://78.media.tumblr.com/5ecb41b654f4e8878f59445b948ede50/tumblr_p7n8on19cV1uy4lhuo1_1280.png'),
            url('https://78.media.tumblr.com/28bd9a2522fbf8981d680317ccbf4282/tumblr_p7n8kqHMuD1uy4lhuo3_1280.png');
            background-repeat: repeat-x;
            background-position:  0 20%, 0 100%, 0 50%, 0 100%, 0 0;
            background-size: 2500px, 800px, 500px 200px, 1000px, 400px 260px; animation: 50s para infinite linear;
        }
        @keyframes para {100% {
            background-position:  -5000px 20%, -800px 95%, 500px 50%,
            1000px 100%, 400px 0;
        }
    }
    input[type="date"] {
        padding: 8px ;
        border-radius: 8px;
        border: 1px solid #ddd;
    }

    </style>
    <script>
        function toggleFilterForm() {
            const filterForm = document.getElementById('filterForm');
            filterForm.style.display = filterForm.style.display === 'block' ? 'none' : 'block';
        }

        function deletePost(postId) {
            if (confirm('Are you sure you want to delete this post?')) {
                fetch('delete_post.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ post_id: postId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        document.getElementById(`post-${postId}`).remove();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the post.');
                });
            }
        }
    </script>
</head>
<body>
<div class="container">
        <div class="welcome">
            <h1>
                <?php
                if (isset($_SESSION['username'])) {
                    echo "Welcome to Dashboard, <strong>" . htmlspecialchars($_SESSION['username']) . "</strong>!";
                } else {
                    echo "You are not logged in.";
                }
                ?>
            </h1>
        </div>

        <!-- Action buttons -->
        <div class="action-buttons">
            <a href="Home.php" class="btn">Home</a>
            <a href="list_posts.php" class="btn">Create New Post</a>
            <a href="mailto:huynhanhkhoa2707@gmail.com" class="btn">Contact Admin</a>
        </div>

        <!-- Filter Button and Form -->
        <div class="flex--item">
            <button class="s-btn s-btn__outlined s-btn__sm s-btn__icon ws-nowrap btn" onclick="toggleFilterForm()" role="button" aria-expanded="false">
                <svg aria-hidden="true" class="svg-icon iconFilter" width="18" height="18" viewBox="0 0 18 18">
                    <path d="M2 4h14v2H2zm2 4h10v2H4zm8 4H6v2h6z"></path>
                </svg>
                Filter
            </button>
        </div>

        <!-- Filter Form -->
        <div id="filterForm" class="filter-form" style="display:none;">
            <h3>Filter Options</h3>
            <form action="index.php" method="GET">
                <div class="filter-option">
                    <label for="category">Category:</label>
                    <select name="category" id="category">
                        <option value="all">All</option>
                        <option value="category1">Nearest</option>
                        <option value="category2">Recent activity</option>
                        <option value="category3">Most Frequent</option>
                        <option value="category4">Answers</option>
                    </select>
                </div>
                <div class="filter-option">
                    <label for="date">Date:</label>
                    <input type="date" name="date" id="date">
                </div>
                <div class="filter-option">
                    <label for="keyword">Keyword Content:</label>
                    <input type="text" name="keyword" id="keyword" placeholder="Enter keyword...">
                </div>
                <input type="submit" class="btn" value="Apply Filters">
            </form>
        </div>

        <!-- Display Posts -->
        <h2>All Posts</h2>
        <div class="posts">
    <?php
    if (isset($error_message)) {
        echo "<p style='color: red;'>" . htmlspecialchars($error_message) . "</p>";
    } elseif ($posts) {
        foreach ($posts as $post) {
            echo "<div id='post-" . htmlspecialchars($post['id']) . "' class='post'>";
            echo "<h2>Poster: <a href='view_post.php?post_id=" . htmlspecialchars($post['id']) . "'>" . htmlspecialchars($post['title']) . "</a></h2>";
            echo "<p>Post Content: " . htmlspecialchars($post['content']) . "</p>";

            // Display the module name below the post content
            echo "<p>Module: " . htmlspecialchars($post['module_name']) . "</p>";

            // Display the created_at timestamp
            $createdAt = new DateTime($post['created_at']);
            echo "<div class='post-meta'>Posted on: " . $createdAt->format('F j, Y, g:i a') . "</div>";

            // Check if within 5 minutes to show edit and delete options
            $currentTime = new DateTime();
            $timeDiff = $currentTime->getTimestamp() - $createdAt->getTimestamp();

            if ($timeDiff <= 300) {
                echo "<a href='edit_post.php?post_id=" . htmlspecialchars($post['id']) . "' class='btn'>Edit</a>";
                echo "<button class='btn' onclick='deletePost(" . htmlspecialchars($post['id']) . ")'>Delete</button>";
            }
            
            echo "</div>";
        }
    } else {
        echo "<p>No posts found.</p>";
    }
    ?>
    </div>
        <?php include 'templates/headercontent.php'; ?>
    </div>
</body>
</html>