<?php 
include 'db_connect.php'; // Connect to the database

// Fetch all users
try {
    $stmt = $pdo->query("SELECT fullname, username FROM users ORDER BY username ASC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching users: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&amp;display=swap" rel="stylesheet">
    <style>
      body {
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-size: cover;
        background-position: top;
        width: 100%;
        height: 100%;
        font-family: Arial, Helvetica, sans-serif;
        letter-spacing: 0.02em;
        font-weight: 400;
        -webkit-font-smoothing: antialiased;
        background-color: hsla(200, 40%, 30%, 4);
        background-image:
        url('https://78.media.tumblr.com/8cd0a12b7d9d5ba2c7d26f42c25de99f/tumblr_p7n8kqHMuD1uy4lhuo2_1280.png'),
        url('https://78.media.tumblr.com/5ecb41b654f4e8878f59445b948ede50/tumblr_p7n8on19cV1uy4lhuo1_1280.png'),
        url('https://78.media.tumblr.com/28bd9a2522fbf8981d680317ccbf4282/tumblr_p7n8kqHMuD1uy4lhuo3_1280.png');
    background-repeat: repeat-x;
    background-position: 0 20%, 0 100%, 0 50%, 0 100%, 0 0;
    background-size: 2500px, 800px, 500px 200px, 1000px, 400px 260px;
    animation: 50s para infinite linear;
    }

    @keyframes para {
    100% {
        background-position: -5000px 20%, -800px 95%, 500px 50%, 1000px 100%, 400px 0;
        }
    }

    .container {
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 900px;
    }

    h1 {
        color: #4a4a4a;
        margin-bottom: 20px;
        font-weight: 600;
        text-align: center;
    }

    .button-container {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }

    .button-container a {
        margin: 0 10px;
    }

    .add-post-btn {
        display: inline-block;
        padding: 10px 20px;
        font-size: 16px;
        color: #4a4a4a;
        background: #fbd786;
        border-radius: 30px;
        text-decoration: none;
        text-align: center;
    }

    .add-post-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.4);
    }

    .btn {
        background-color: #667eea;
        color: #ffffff;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }

    .user-list {
        list-style-type: none;
        padding: 0;
    }   

    .user-list li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding: 10px 20px;
        background: #f9f9f9;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .user-list li span {
        flex: 1;
        text-align: left;
    }

    .edit-btn, .delete-btn {
        display: inline-block;
        padding: 10px 20px;
        text-align: center;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        white-space: nowrap;
        margin-left: 10px;
    }   

    .edit-btn {
        background: #1E90FF;
        color: #ffffff;
    }

    .edit-btn:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.4);
        transform: translateY(-2px);
    }

    .delete-btn {
        background: #ff0033;
        color: whitesmoke;
        border: none;
        cursor: pointer;
    }

    .delete-btn:hover {
        color: #ffffff;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.4);
        transform: translateY(-2px);
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
    <script>
        // AJAX function to delete user without reloading the page
        function deleteUser(username) {
            if (confirm("Are you sure you want to delete this user?")) {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "delete_user.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            document.getElementById('user-' + username).remove(); // Remove user from list
                        } else {
                            alert("Error: " + response.message);
                        }
                    }
                };
                xhr.send("username=" + encodeURIComponent(username));
            }
        }
    </script>
</head>
<body>
    <div class="container">
    <h1>Manage Users</h1>
        <div class="button-container">
            <a href="add_newuser.php" class="add-post-btn">Add New User</a>
            <a href="admin_dashboard.php" class="add-post-btn">Back to Admin Dashboard</a>
        </div>
        <ul class="user-list">
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $user): ?>
                    <li id="user-<?php echo htmlspecialchars($user['username']); ?>">
                        <span><?php echo htmlspecialchars($user['fullname'] . ' (' . $user['username'] . ')'); ?></span>
                        <a href="edit_user.php?username=<?php echo urlencode($user['username']); ?>" class="edit-btn">Edit</a>
                        <button onclick="deleteUser('<?php echo htmlspecialchars($user['username']); ?>')" class="delete-btn">Delete</button>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No users found.</li>
            <?php endif; ?>
        </ul>
    </div>

    <?php include 'templates/headercontent.php'; ?>
</body>
</html>
