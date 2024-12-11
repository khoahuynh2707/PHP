<?php
session_start();
include 'db_connect.php'; // Ensure database connection is established

$post_id = $_GET['post_id'] ?? null; // Fetch post ID from URL

if (!$post_id) {
    die("Post not specified.");
}

try {
    // Prepare and execute query to fetch post details
    $stmt = $pdo->prepare("SELECT posts.*, modules.name AS module FROM posts LEFT JOIN modules ON posts.module_id = modules.id WHERE posts.id = :post_id");
    $stmt->execute([':post_id' => $post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        die("Post not found.");
    }
} catch (PDOException $e) {
    die("Error fetching post: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Post - <?php echo htmlspecialchars($post['title']); ?></title>
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
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            text-align: left;
        }

        h1 {
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
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
        <h1><?php echo htmlspecialchars($post['title']); ?></h1>
        <p><strong>Post Content:</strong> <?php echo htmlspecialchars($post['content']); ?></p>
        <p><strong>Module:</strong> <?php echo htmlspecialchars($post['module'] ?? 'Not assigned'); ?></p>
        <p><strong>Posted on:</strong> <?php echo (new DateTime($post['created_at']))->format('F j, Y, g:i a'); ?></p>
    <div class="submit">
    <a href="manage_posts.php" class="back-link">Back to Manage Posts</a>
    </div>
    <?php include 'templates/headercontent.php'; ?>
</body>
</html>
