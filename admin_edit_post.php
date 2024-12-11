<?php
session_start();
include 'db_connect.php';

// Check if the post ID is set and valid
if (!isset($_GET['post_id']) || !is_numeric($_GET['post_id'])) {
    header('Location: manage_posts.php');
    exit;
}

$post_id = $_GET['post_id'];

// Fetch post details based on post_id
try {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = :post_id");
    $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->execute();
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        header('Location: manage_posts.php');
        exit;
    }
} catch (PDOException $e) {
    die("Error fetching post: " . $e->getMessage());
}

// Fetch modules for the dropdown
$modules = [];
try {
    $stmt = $pdo->query("SELECT id, name FROM modules ORDER BY name ASC");
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching modules: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize user inputs
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $module_id = $_POST['module_id'];

    // Validate inputs
    if (!empty($title) && !empty($content)) {
        try {
            // Update the post
            $query = "UPDATE posts SET title = :title, content = :content, module_id = :module_id WHERE id = :post_id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':content', $content, PDO::PARAM_STR);
            $stmt->bindParam(':module_id', $module_id, PDO::PARAM_INT);
            $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
            
            $stmt->execute();

            // Redirect to manage_posts.php after successful update
            header("Location: manage_posts.php");
            exit;
        } catch (PDOException $e) {
            $error_message = "Error updating post: " . $e->getMessage();
        }
    } else {
        $error_message = "Title and content are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Post</title>
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
        .container {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            margin: auto;
        }
        h1 {
            color: black;
            margin-bottom: 20px;
            font-weight: 600;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
        }
        .btn {
            padding: 12px 20px;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            display: inline-block;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Post</h1>
        
        <?php if (isset($error_message)) echo "<p class='error'>$error_message</p>"; ?>

        <form action="admin_edit_post.php?post_id=<?php echo htmlspecialchars($post_id); ?>" method="POST">
            <div class="form-group">
                <label for="module_id">Module:</label>
                <select id="module_id" name="module_id" class="form-control" required>
                    <?php foreach ($modules as $module): ?>
                        <option value="<?php echo $module['id']; ?>" <?php if ($post['module_id'] == $module['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($module['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($post['title']); ?>" required>
            </div>

            <div class="form-group">
                <label for="content">Content:</label>
                <textarea id="content" name="content" class="form-control" rows="6" required><?php echo htmlspecialchars($post['content']); ?></textarea>
            </div>

            <button type="submit" class="btn">Update Post</button>
        </form>
        <br>
        <a href="manage_posts.php" class="btn">Back to Manage Posts</a>
    </div>
    <?php include 'templates/headercontent.php'; ?>
</body>
</html>
