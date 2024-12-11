<?php
include 'db_connect.php';

$username = '';
$success_message = '';
$error_message = '';

// Check if the username is provided
if (isset($_GET['username'])) {
    $username = $_GET['username'];

    // Fetch user details
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $error_message = "User not found.";
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_username'], $_POST['new_email'])) {
    $new_username = $_POST['new_username'];
    $new_email = $_POST['new_email'];
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';

    try {
        // Update user details in the database
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT); // Hash the new password
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE username = ?");
            $stmt->execute([$new_username, $new_email, $hashed_password, $username]);
        } else {
            // Update username and email only if password is not provided
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE username = ?");
            $stmt->execute([$new_username, $new_email, $username]);
        }

        $success_message = "User updated successfully!";
        $username = $new_username; // Update for display purposes
    } catch (PDOException $e) {
        $error_message = "An error occurred: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
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
        .submit{
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Edit User</h1>
    <?php if ($success_message): ?>
        <p class="success"><?php echo $success_message; ?></p>
    <?php elseif ($error_message): ?>
        <p class="error"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <?php if ($user): ?>
        <form method="POST">
            <label>New Username:</label>
            <input type="text" name="new_username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            <br>
            <label>Email:</label>
            <input type="email" name="new_email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            <br>
            <label>New Password:</label>
            <input type="password" name="new_password" placeholder="Leave blank to keep current password">
            <br>
            <button type="submit" class="btn">Update User</button>
        </form>
    <?php endif; ?>
       <div class="submit">
    <a href="manage_users.php" class="btn">Back to Manage Users</a>
</div>
<?php include 'templates/headercontent.php'; ?>
</body>
</html>
