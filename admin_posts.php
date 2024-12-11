<?php
session_start();
// Database connection
include 'db_connect.php'; // Ensure this file sets up $pdo for database access

$error_message = '';

// Fetch modules from the database
try {
    $stmt = $pdo->query("SELECT id, name FROM modules ORDER BY name ASC");
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error fetching modules: " . $e->getMessage();
}
try {
    $userStmt = $pdo->query("SELECT id, username FROM users ORDER BY username ASC");
    $users = $userStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error fetching users: " . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $module_id = intval($_POST['module_id']);

    // Handle file upload
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        // Ensure the upload directory exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $image_path = $upload_dir . $image_name;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
            $error_message = "Failed to upload image.";
            $image_path = null;
        }
    }

    // Insert post into the database
    if (!empty($title) && !empty($content)) {
        try {
            $query = "INSERT INTO posts (title, content, image_path, module_id, user_id) VALUES (:title, :content, :image_path, :module_id, :user_id)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':content', $content, PDO::PARAM_STR);
            $stmt->bindParam(':image_path', $image_path, PDO::PARAM_STR);
            $stmt->bindParam(':module_id', $module_id, PDO::PARAM_INT);
            $stmt->execute();

            // Redirect after success
            header("Location: manage_posts.php");
            exit;
        } catch (PDOException $e) {
            $error_message = "Error creating post: " . $e->getMessage();
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
    <title>Add Post</title>
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
        }}
        h1{
            text-align: center;
        }
        .back-link {
            background: linear-gradient(109.6deg, rgba(156, 252, 248, 1) 11.2%, rgba(110, 123, 251, 1) 91.1%);
            border-radius: 30px;
            padding: 10px 20px;
            text-decoration: none;
            margin-top: 20px;
            display: inline-block;
        }
        .submit{
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add New Post</h1>
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <form action="admin_posts.php" method="POST" enctype="multipart/form-data">
              <!-- User dropdown -->
    <label for="user_id">User:</label>
    <select name="user_id" required>
        <?php foreach ($users as $user): ?>
            <option value="<?php echo htmlspecialchars($user['id']); ?>">
                <?php echo htmlspecialchars($user['username']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <label for="module_id">Module:</label>
    <select name="module_id" required>
        <?php foreach ($modules as $module): ?>
            <option value="<?php echo htmlspecialchars($module['id']); ?>">
                <?php echo htmlspecialchars($module['name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <label for="title">Title:</label>
    <input type="text" name="title" required>

    <label for="content">Content:</label>
    <textarea name="content" rows="5" required></textarea>

    <label for="image">Image:</label>
    <input type="file" name="image" accept="image/*">

    <button type="submit" class="btn">Add Post</button>
</form>
        <div class="submit">
            <a href="manage_posts.php" class="back-link">Back to Manage Posts</a>
        </div>
    </div>
    <?php include 'templates/headercontent.php'; ?>
</body>
</html>