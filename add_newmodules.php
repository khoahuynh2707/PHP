<?php
// add_new_modules.php
// Database connection
include 'db_connect.php';

try {
    // Check if the request method is POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $moduleName = $_POST['module_name'];

        // Prepare the statement to insert the new module into the database
        $stmt = $pdo->prepare("INSERT INTO modules (name) VALUES (:name)");
        $stmt->bindParam(':name', $moduleName);
        $stmt->execute();

        // Redirect to manage_modules.php after adding the module
        header("Location: manage_modules.php");
        exit();
    }
} catch (PDOException $e) {
    // Handle exception (optional)
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Module</title>
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
            background-color: hsla(200,40%,30%,4);
            background-image:   
            url('https://78.media.tumblr.com/8cd0a12b7d9d5ba2c7d26f42c25de99f/tumblr_p7n8kqHMuD1uy4lhuo2_1280.png'),
            url('https://78.media.tumblr.com/5ecb41b654f4e8878f59445b948ede50/tumblr_p7n8on19cV1uy4lhuo1_1280.png'),
            url('https://78.media.tumblr.com/28bd9a2522fbf8981d680317ccbf4282/tumblr_p7n8kqHMuD1uy4lhuo3_1280.png');
            background-repeat: repeat-x;
            background-position:  0 20%, 0 100%, 0 50%, 0 100%, 0 0;
            background-size: 2500px, 800px, 500px 200px, 1000px, 400px 260px;
            animation: 50s para infinite linear;
        }
        @keyframes para {
            100% {
                background-position:  -5000px 20%, -800px 95%, 500px 50%, 1000px 100%, 400px 0;
            }
        }
        .container {
            background-color: #fff;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }
        h1 {
            color: #4a4a4a;
            text-align: center;
            margin-bottom: 30px;
            font-weight: 600;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: 600;
            margin-bottom: 5px;
            display: block;
            color: #333;
        }
        .form-group input[type="text"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .back-link {
            background: linear-gradient(109.6deg, rgba(156, 252, 248, 1) 11.2%, rgba(110, 123, 251, 1) 91.1%);
            border-radius: 30px;
            padding: 10px 20px;
            text-decoration: none;
            margin-top: 20px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add New Module</h1>
        <form method="POST" action="add_newmodules.php">
            <div class="form-group">
                <label for="module_name">New Module Name:</label>
                <input type="text" id="module_name" name="module_name" required>
            </div>
            <button type="submit" class="btn">Add Module</button>
        </form>
        <br>
        <div class="submit">
        <a href="manage_modules.php" class="back-link">Back to Manage Modules</a>
        </div>
    </div>
    <?php include 'templates/headercontent.php'; ?>
</body>
</html>
