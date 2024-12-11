<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Check if post ID is provided
if (!isset($_GET['post_id'])) {
    echo "No post ID specified.";
    exit;
}

$postId = $_GET['post_id'];
$successMessage = ""; // Variable to store success message

// Fetch the post data
try {
    $query = "SELECT * FROM posts WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $postId, PDO::PARAM_INT);
    $stmt->execute();
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if post exists
    if (!$post) {
        echo "Post not found.";
        exit;
    }
} catch (PDOException $e) {
    echo "Error fetching post: " . $e->getMessage();
    exit;
}

// Handle form submission for updating the post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newTitle = $_POST['title'];
    $newContent = $_POST['content'];
    $newModuleId = $_POST['module_id'];

    // Update the post in the database
    try {
        $updateQuery = "UPDATE posts SET title = :title, content = :content, module_id = :module_id WHERE id = :id";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->bindParam(':title', $newTitle, PDO::PARAM_STR);
        $stmt->bindParam(':content', $newContent, PDO::PARAM_STR);
        $stmt->bindParam(':module_id', $newModuleId, PDO::PARAM_INT);
        $stmt->bindParam(':id', $postId, PDO::PARAM_INT);
        if ($stmt->execute()) {
            // Redirect back to manage_posts.php with a success message
            header("Location: index.php?success=Post updated successfully");
            exit();
        } else {
            $error_message = "Failed to update post.";
        }
    } catch (PDOException $e) {
        echo "Error updating post: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
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
        h1 {
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
        .submit {   
            margin-bottom: 20px;
        }
        .btn {
            background-color: #6e7bfb;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Post</h1>  

        <!-- Display success message -->
        <?php if ($successMessage): ?>
            <p style="color: green;"><?php echo htmlspecialchars($successMessage); ?></p>
        <?php endif; ?>
        <!-- Form for editing post -->
        <form method="POST" action="">
            <label for="title">New Title:</label><br>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required><br><br>
            
            <label for="content">Content:</label><br>
            <textarea id="content" name="content" rows="10" required><?php echo htmlspecialchars($post['content']); ?></textarea><br><br>
            
            <label for="module_id">Module:</label><br>
            <select id="module_id" name="module_id" required>
                <option value="1" <?php echo $post['module_id'] == 1 ? 'selected' : ''; ?>>General</option>
                <option value="2" <?php echo $post['module_id'] == 2 ? 'selected' : ''; ?>>HTML&CSS</option>
                <option value="3" <?php echo $post['module_id'] == 3 ? 'selected' : ''; ?>>Java</option>
                <option value="4" <?php echo $post['module_id'] == 4 ? 'selected' : ''; ?>>MySQL</option>
            </select><br><br>

        
                <button type="submit" class="btn" fdprocessedid="qjtkrq">Update Post</button>
        </form>
          <!-- Back to dashboard link -->
         <div class="submit">
        <a href="index.php" class="back-link">Back to Dashboard</a>
    </div>
    </div>
    <?php include 'templates/headercontent.php'; ?>
</body>
</html>
 