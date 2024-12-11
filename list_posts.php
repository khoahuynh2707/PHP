<?php
session_start();
include 'db_connect.php';

// Redirect to login page if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch modules for the dropdown
$modules = [];
try {
    $stmt = $pdo->query("SELECT id, name FROM modules ORDER BY name ASC");
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching modules: " . $e->getMessage();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize user inputs
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $user_id = $_SESSION['user_id'];
    $module_id = $_POST['module_id'];
    $image = null; // Default to NULL in case no image is uploaded

    // Handle file upload if an image is provided
    if (isset($_FILES['post_image']) && $_FILES['post_image']['name']) {
        // Define allowed image types
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        // Check if the uploaded file is an allowed type
        if (in_array($_FILES['post_image']['type'], $allowed_types)) {
            $target_dir = "uploads/";
            // Create the uploads directory if it doesn't exist
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            // Define the target path for the uploaded image
            $target_file = $target_dir . basename($_FILES['post_image']['name']);

            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES['post_image']['tmp_name'], $target_file)) {
                $image = $target_file; // Set image path if upload is successful
            } else {
                $error_message = "File upload failed. Please check directory permissions or path.";
            }
        } else {
            $error_message = "Invalid file type. Only JPEG, PNG, GIF, and WEBP formats are allowed.";
        }
    }

    // Insert the post with the image path and module_id into the database
    if (!empty($title) && !empty($content) && !isset($error_message)) {
        try {
            // Prepare the SQL query for inserting the post data
            $query = "INSERT INTO posts (user_id, title, content, image, module_id) VALUES (:user_id, :title, :content, :image, :module_id)";
            $stmt = $pdo->prepare($query);
            
            // Bind parameters to the query
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':content', $content, PDO::PARAM_STR);
            $stmt->bindParam(':image', $image, PDO::PARAM_STR);
            $stmt->bindParam(':module_id', $module_id, PDO::PARAM_INT);
            
            // Execute the query
            $stmt->execute();

            // Redirect to index.php after successful post creation
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            // Handle database errors
            $error_message = "Error creating post: " . $e->getMessage();
        }
    } elseif (!isset($error_message)) {
        // Handle case where title or content is missing
        $error_message = "Title and content are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Post</title>
    <!-- Include Google Fonts for styling -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* General styles for the page */
        body {
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
            height: 100%;
            background-color: hsla(200,40%,30%,4);
            /* Background images with different animations */
            background-image:   
            url('https://78.media.tumblr.com/8cd0a12b7d9d5ba2c7d26f42c25de99f/tumblr_p7n8kqHMuD1uy4lhuo2_1280.png'),
            url('https://78.media.tumblr.com/5ecb41b654f4e8878f59445b948ede50/tumblr_p7n8on19cV1uy4lhuo1_1280.png'),
            url('https://78.media.tumblr.com/28bd9a2522fbf8981d680317ccbf4282/tumblr_p7n8kqHMuD1uy4lhuo3_1280.png');
            background-repeat: repeat-x;
            background-position:  0 20%, 0 100%, 0 50%, 0 100%, 0 0;
            background-size: 2500px, 800px, 500px 200px, 1000px, 400px 260px; 
            animation: 50s para infinite linear;
        }
        
        /* Animation for background images */
        @keyframes para {
            100% {
                background-position:  -5000px 20%, -800px 95%, 500px 50%, 1000px 100%, 400px 0;
            }
        }

        h1 {
            text-align: center;
        }

        /* Styling for the back button link */
        .back-link {
            background: linear-gradient(109.6deg, rgba(156, 252, 248, 1) 11.2%, rgba(110, 123, 251, 1) 91.1%);
            border-radius: 30px;
            padding: 10px 20px;
            text-decoration: none;
            margin-top: 20px;
            display: inline-block;
        }

        /* Form styling */
        .form-group, .submit {
            margin-bottom: 20px;
        }

        /* Button styling */
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
        <h1>Create a New Post</h1>
        <!-- Display error messages if any -->
        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

              <!-- Form for creating a new post -->
              <form action="list_posts.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" name="title" id="title" required>
            </div>

            <div class="form-group">
                <label for="content">Content:</label>
                <textarea name="content" id="content" rows="5" required></textarea>
            </div>

            <div class="form-group">
                <label for="module_id">Module:</label>
                <!-- Dropdown for selecting the module -->
                <select name="module_id" id="module_id" required>
                    <?php foreach ($modules as $module): ?>
                        <option value="<?php echo $module['id']; ?>"><?php echo htmlspecialchars($module['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="post_image">Upload Image:</label>
                <!-- File input for uploading an image -->
                <input type="file" name="post_image" id="post_image" accept="image/*">
            </div>

            <!-- Submit button for the form -->
            <button type="submit" class="btn">Submit Post</button>
        </form>

        <!-- Back to Dashboard link -->
        <div class="submit">
            <a href="index.php" class="back-link">Back to Dashboard</a>
        </div>
    </div>
    <!-- Include a header template -->
    <?php include 'templates/headercontent.php'; ?>
</body>
</html>


