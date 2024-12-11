<?php
include 'db_connect.php';

$error_message = '';
$success_message = '';

// Check if the token is provided in the URL
if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token exists and hasn't expired
    $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        // Handle form submission for password reset
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            // Check if passwords match
            if ($password === $confirm_password) {
                // Hash the new password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Update the password in the database and clear the reset token
                $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?");
                $stmt->execute([$hashed_password, $token]);

                $success_message = "Your password has been reset successfully.";
            } else {
                $error_message = "Passwords do not match.";
            }
        }
    } else {
        $error_message = "Invalid or expired reset token. Please request a new password reset.";
    }
} else {
    $error_message = "No reset token provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<style>
    body {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;    
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-size: cover;
        background-position: top;
        width: 100%;      
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
        background-color: #ffffff;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        text-align: center;
        max-width: 400px;
        width: 100%;
    }
    h2 {
        margin-bottom: 20px;
        font-size: 28px;
    }
    form {
        display: flex;
        flex-direction: column;
    }
    input[type="password"] {
        padding: 14px;
        font-size: 16px;
        border: 2px solid #ddd;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    button {
        background-color: #5e60ce;
        color: white;
        padding: 14px;
        font-size: 16px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    button:hover {
        background-color: #4c51b3;
    }
    .message {
        margin-top: 20px;
        font-size: 14px;
    }
    .success {
        color: #28a745;
    }
    .error {
        color: #dc3545;
    }
    .back-link {
        margin-top: 20px;
        font-size: 16px;
        color: #5e60ce;
        text-decoration: none;
        display: inline-block;
        padding: 10px;
        border-radius: 5px;
        background-color: #f1f1f1;
        transition: background-color 0.3s;
    }
    .back-link:hover {
        background-color: #e2e2e2;
    }
    @media (max-width: 600px) {
        .container {
            padding: 30px;
        }
        h2 {
            font-size: 24px;
        }
        input[type="password"], button {
            font-size: 14px;
        }
    }
</style>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <?php if ($error_message): ?>
            <p class="message error"><?php echo $error_message; ?></p>
        <?php elseif ($success_message): ?>
            <p class="message success"><?php echo $success_message; ?></p>
            <a href="login.php" class="back-link">Back to Login</a>
        <?php else: ?>
            <form method="POST">
                <input type="password" name="password" placeholder="Enter new password" required>
                <input type="password" name="confirm_password" placeholder="Confirm new password" required>
                <button type="submit">Reset Password</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
