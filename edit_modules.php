<?php
include 'db_connect.php'; // Ensure db_connect.php connects to your database

// Check if a module ID has been provided for editing
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $moduleId = $_GET['id'];

    // Fetch the specific module from the database
    try {
        $stmt = $pdo->prepare("SELECT id, name FROM modules WHERE id = :id");
        $stmt->execute(['id' => $moduleId]);
        $module = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$module) {
            echo "Module not found.";
            exit;
        }
    } catch (PDOException $e) {
        // Handle exception (optional)
        echo "Error: " . $e->getMessage();
    }
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Prepare an update statement
            $stmt = $pdo->prepare("UPDATE modules SET name = :name WHERE id = :id");
            $stmt->execute(['name' => $_POST['module_name'], 'id' => $moduleId]);

            // Redirect back to manage modules page
            header("Location: manage_modules.php?success=1");
            exit;
        } catch (PDOException $e) {
            // Handle exception (optional)
            echo "Error: " . $e->getMessage();
        }
    }
} else {
    // Redirect to manage modules if no ID is set
    header("Location: manage_modules.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Module</title>
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
        h1 {
            text-align: center;
        }
        .module-item {
            margin: 10px 0;
        }
        input[type="text"] {
            width: 70%;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .btn {
            background-color: #007BFF;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            margin-top: 10px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
    <h1>Edit Module</h1>
    <form method="POST" action="">
        <div class="module-item">
            <label for="module_name">New Module Name:</label><br>
            <input type="text" id="module_name" name="module_name" value="<?php echo htmlspecialchars($module['name']); ?>" required>
        </div>
        <button type="submit" class="btn">Update Module</button>
    </form>
    <a href="manage_modules.php" class="btn">Back to Manage Modules</a>
</body>
</html>

    <?php include 'templates/headercontent.php'; ?>
</body>
</html>
