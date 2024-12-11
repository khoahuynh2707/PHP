<?php
include 'db_connect.php';
session_start();

// Fetch posts with module information from the database
try {
    $stmt = $pdo->query("
        SELECT posts.title, posts.content, posts.created_at, modules.name
        FROM posts 
        LEFT JOIN modules ON posts.module_id = modules.id 
        ORDER BY posts.created_at DESC
    ");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Public Dashboard</title>
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

        /* Container for all posts */
        .posts-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.85);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        /* Each post styling */
        .post {
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }

        .post:last-child {
            border-bottom: none;
        }

        .post h2 {
            color: #5e60ce;
            margin-top: 0;
            font-size: 24px;
        }

        .post p {
            font-size: 16px;
            line-height: 1.6;
        }
  
        .post small {
            color: #666;
            font-size: 12px;
        }

        /* Button and login message */
        .actions {
            text-align: center;
            margin-top: 20px;
        }

        .btn {
            display: inline-block;
            padding: 12px 20px;
            background-color: #5e60ce;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #4c51b3;
        }

        .login-message {
            margin-top: 15px;
            font-size: 26px;
        }

        .login-message a {
            color: #5e60ce;
            text-decoration: none;
        }

        .login-message a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
            <div class="posts-container">
            <div class="actions">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="add_new_posts.php" class="btn">Create New Post</a>
                    <?php else: ?>
                        <div class="login-message">
                            <p><a href="login.php">Login</a> to create a new post.</p>
                        </div>
                    <?php endif; ?>
                </div>
            <h1>All Posts</h1>
                
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <h2>Post title: <?php echo htmlspecialchars($post['title']); ?></h2>
                <p> Post Content: <?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                <small>Posted on:  <?php echo $post['created_at']; ?></small>
                <?php if (!empty($post['module_name'])): ?>
                    <div class="module-info">
                        <strong>Module:</strong> <?php echo htmlspecialchars($post['name']); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
