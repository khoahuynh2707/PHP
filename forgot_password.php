<?php
require_once __DIR__ . '/vendor/autoload.php'; // Ensure the correct autoload path

include 'db_connect.php';

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Check if the email exists in the database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Generate a unique reset token
        $token = bin2hex(random_bytes(50));

        // Store the token in the database with an expiration time
        $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?");
        $stmt->execute([$token, $email]);

        // Generate the reset link with the token
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $reset_link = $protocol . $_SERVER['HTTP_HOST'] . "/Courswork/newpassword.php?token=" . urlencode($token);


        
        // Set up PHPMailer
        $mail = new PHPMailer\PHPMailer\PHPMailer();

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // Set mail server
            $mail->SMTPAuth   = true;
            $mail->Username   = 'anhkhoahuynh270705@gmail.com'; 
            $mail->Password   = 'japu nrwi eczt rqtc'; // Use app-specific password (not Gmail password)
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('anhkhoahuynh270705@gmail.com'); // Replace with  email
            $mail->addAddress($email);  // Recipient's email

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body    = "Click the link below to reset your password: <br><a href='$reset_link'>$reset_link</a>";

            // Send email
            $mail->send();
            $success_message = "Password reset link has been sent to your email.";
        } catch (Exception $e) {
            $error_message = "Mailer Error: " . $mail->ErrorInfo;
        }
    } else {
        $error_message = "Email not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<style>
    body {
        display: flex;
        justify-content: center; /* Horizontally center the container */
        align-items: center; /* Vertically center the container */
        height: 100vh; /* Full viewport height */
        margin: 0; /* Remove default margin */
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-size: cover;
        background-position: top;
        width: 100%;
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
        border-radius: 20px;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        text-align: center;
        max-width: 400px;
        width: 100%;
        animation: fadeIn 1s ease-in-out;
        justify-content: center;
    }

    h2 {
        margin-bottom: 10px;
        font-size: 28px;
        color: #333;
    }

    form {
        display: flex;
        flex-direction: column;
    }

    input[type="email"] {
        padding: 14px;
        font-size: 16px;
        border: 2px solid #ddd;
        border-radius: 10px;
        margin-bottom: 20px;
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    input[type="email"]:focus {
        border-color: #5e60ce;
        box-shadow: 0 0 10px rgba(94, 96, 206, 0.4);
        outline: none;
    }

    button {
        background-color: #1E90FF;
        color: white;
        padding: 14px;
        font-size: 16px;
        border: none;
        border-radius: 15px;
        cursor: pointer;
        transition: background-color 0.3s, transform 0.2s;
    }

    button:hover {
        background-color: #4169E1;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(76, 81, 179, 0.3);
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

    a {
        display: inline-block;
        margin-top: 20px;
        color: #5e60ce;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s;
    }

    .instruction-text {
        font-size: 16px;
        color: #555;
        margin-bottom: 15px;
        font-weight: bold;
    }

    a:hover {
        color: #4c51b3;
        text-decoration: underline;
    }

    /* Animation */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
<body>
    <div class="container">
        <h2>Forgot Password</h2>
        <p class="instruction-text">Enter your email to reset your password:</p>
        <?php if ($error_message): ?>
            <p class="message error"><?php echo $error_message; ?></p>
        <?php elseif ($success_message): ?>
            <p class="message success"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit">Send Reset Link</button>
            <a href="login.php">Back to Login</a>
        </form>
    </div>
</body>
</html>
