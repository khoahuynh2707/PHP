<?php
session_start();
include 'db_connect.php'; // Ensure this file contains your database connection setup

// Fetch all posts with user information, no filters
$query = "SELECT posts.*, modules.name AS module, users.username AS creator 
          FROM posts 
          LEFT JOIN modules ON posts.module_id = modules.id 
          LEFT JOIN users ON posts.user_id = users.id 
          ORDER BY created_at DESC"; // Ordering by created_at in descending order

// Prepare and execute query
try {
    $stmt = $pdo->prepare($query);
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
    <title>Manage Posts</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&amp;display=swap" rel="stylesheet">
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
            color: black;
            margin-bottom: 20px;
            font-weight: 600;
            text-align: center;
        }

        .posts {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .post {
            background-color: rgba(255, 255, 255, 0.85);
            width: 80%;
            max-width: 600px;
            padding: 20px;
            margin: 15px auto;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            text-align: left;
        }

        .post h2 {
            font-size: 1.5em;
            color: #333;
            margin-bottom: 10px;
        }

        .post p {
            color: #555;
            font-size: 1em;
            line-height: 1.6;
            margin: 8px 0;
        }

        .post-meta {
            color: #777;
            font-size: 0.9em;
        }

        .button-container {
            display: flex;
            justify-content: center; /* Center the buttons horizontally */
            margin-top: 20px; /* Space above the button container */
        }

        .button-container a {
            margin: 0 10px; /* Space between buttons */
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
            margin: 5px;
            background:blue ;
        }
        
        .add-post-btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: white;
            background: #00BFFF;
            border-radius: 30px;
            text-decoration: none;
            text-align: center;
        }

        .add-post-btn:hover {
            
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.4);
        }

        .message {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }
            
        .success {
            background-color: #e8f5e9;
            color: #4caf50;
            }

        .error {
            background-color: #ffebee;
            color: #f44336;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Posts</h1>
        <div class="button-container">
            <a href="admin_posts.php" class="add-post-btn">Add New Post</a>
            <a href="admin_dashboard.php" class="add-post-btn">Back to Admin Dashboard</a>
        </div>
    </div>

    <!-- Display posts -->
    <h2>All Posts</h2>
    <div class="posts">
        <?php if ($posts): ?>
            <?php foreach ($posts as $post): ?>
                <div id="post-<?php echo htmlspecialchars($post['id']); ?>" class="post">
                    <h2>
                        <a href="admin_viewpost.php?post_id=<?php echo htmlspecialchars($post['id']); ?>">
                            <?php echo htmlspecialchars($post['title']); ?>
                        </a>
                    </h2>
                    <p>Post Content: <?php echo htmlspecialchars($post['content']); ?></p>
                    <p>Module: <?php echo isset($post['module']) ? htmlspecialchars($post['module']) : 'Not assigned'; ?></p>
                    <div class="post-meta">
                        Posted on: <?php echo (new DateTime($post['created_at']))->format('F j, Y, g:i a'); ?>
                    </div>
                    <!-- Edit and Delete buttons -->
                    <a href="admin_edit_post.php?post_id=<?php echo htmlspecialchars($post['id']); ?>" class="btn">Edit</a>
                    <a href="admin_delete.php?id=<?php echo htmlspecialchars($post['id']); ?>" class="btn" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No posts found.</p>
        <?php endif; ?>
    </div>
    <?php include 'templates/headercontent.php'; ?>
</body>
</html>