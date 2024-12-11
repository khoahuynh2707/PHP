<?php
session_start();
include 'db_connect.php';

// Redirect to login page if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch the post based on post_id from URL
if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];

    try {
        // Fetch the post with image_path
        $query = "SELECT * FROM posts WHERE id = :post_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->execute();
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch replies to the post
        $reply_query = "SELECT replies.*, users.username FROM replies JOIN users ON replies.user_id = users.id WHERE replies.post_id = :post_id ORDER BY replies.created_at ASC";
        $reply_stmt = $pdo->prepare($reply_query);
        $reply_stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        $reply_stmt->execute();
        $replies = $reply_stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
} else {
    echo "Post not found.";
    exit;
}

// Handle reply form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reply_content = trim($_POST['reply_content']);
    $user_id = $_SESSION['user_id'];

    if (!empty($reply_content)) {
        try {
            $query = "INSERT INTO replies (post_id, user_id, reply_content) VALUES (:post_id, :user_id, :reply_content)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':reply_content', $reply_content, PDO::PARAM_STR);
            $stmt->execute();

            // Reload the page to show the new reply
            header("Location: view_post.php?post_id=" . $post_id);
            exit;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        $error_message = "Please enter a reply.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Post</title>
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
            background-position: 0 20%, 0 100%, 0 50%, 0 100%, 0 0;
            background-size: 2500px, 800px, 500px 200px, 1000px, 400px 260px; animation: 50s para infinite linear;
        }
        @keyframes para {100% {
            background-position: -5000px 20%, -800px 95%, 500px 50%,
            1000px 100%, 400px 0;
        }
    }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .back-link {
            background: linear-gradient(109.6deg, rgba(156, 252, 248, 1) 11.2%, rgba(110, 123, 251, 1) 91.1%);
            border-radius: 30px;
            padding: 10px 20px;
            text-decoration: none;
            margin-top: 20px;
            display: inline-block;
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
        .submit {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 10px;
        }
        .back-link {
            background: linear-gradient(109.6deg, rgba(156, 252, 248, 1) 11.2%, rgba(110, 123, 251, 1) 91.1%);
            border-radius: 30px;
            padding: 10px 20px;
            text-decoration: none;
            color: black;
            margin-top: 10px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($post['title']); ?></h1>
        <p>Post content: <?php echo htmlspecialchars($post['content']); ?></p>
        <p>Posted on: <?php echo $post['created_at']; ?></p>

        <!-- Display post image if it exists -->
        <?php if ($post['image']): ?>
                <img src="<?= htmlspecialchars($post['image']) ?>" alt="Post Image" style="max-width: 100%;">
            <?php endif; ?>

        <hr>

        <!-- Display replies -->
        <h2>Replies</h2>
        <?php if ($replies): ?>
            <?php foreach ($replies as $reply): ?>
                <div class="reply">
                    <p><strong><?php echo htmlspecialchars($reply['username']); ?>:</strong> <?php echo htmlspecialchars($reply['reply_content']); ?></p>
                    <p>Replied on: <?php echo $reply['created_at']; ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No replies yet. Be the first to reply!</p>
        <?php endif; ?>

        <hr>

        <!-- Reply form -->
        <h2>Reply to this post</h2>

        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <form method="POST">
            <textarea name="reply_content" rows="4" cols="50" placeholder="Write your reply here..." required></textarea>
            <button type="submit" class="btn">Submit Reply</button>
            <div class="submit">
                <a href="index.php" class="back-link">Back to Dashboard</a>
            </div>
        </form>
    </div>
    <?php include 'templates/headercontent.php'; ?>
</body>
</html>