<?php 
include 'db_connect.php';

// Initialize variables
$fullname = '';
$username = '';
$email = '';
$password = '';
$success_message = '';
$error_message = '';

// Process the registration form when submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (fullname, username, email, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$fullname, $username, $email, $hashed_password]);
        $success_message = "Registration successful!";
        // Reset fields after successful submission
        $fullname = '';
        $username = '';
        $email = '';
    } catch (PDOException $e) {
        $error_message = "An error occurred: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .container { 
            width: 100%; 
            max-width: 400px; 
            padding: 40px; 
            margin: auto; 
            text-align: center; 
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            animation: slideInRight 1s ease-out;
        }
        .form-group { 
            display: flex; 
            align-items: center; 
            margin-bottom: 25px; 
            position: relative;
            opacity: 0;
            opacity: 0; 
            animation: slideInRight 0.8s ease-out forwards;
            animation-delay: 0.2s; /* Delay for staggered effect */
        }
        .form-control { 
            flex: 1; 
            padding: 12px; 
            font-size: 16px; 
            border: 2px solid #ddd; 
            border-radius: 8px; 
            margin-left: 10px; 
            width: calc(100% - 30px); 
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .icon { 
            font-size: 22px; 
            color: #888; 
        }
        .toggle-password { 
            position: absolute; 
            right: 10px; 
            cursor: pointer; 
            color: #888; 
            font-size: 18px; 
        }
        .btn { 
            display: inline-block; 
            padding: 12px 25px; 
            font-size: 16px; 
            color: #fff; 
            background-color: #5e60ce; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            margin-top: 15px; 
            width: 100%; 
            transition: background-color 0.3s, transform 0.2s;
        }
        .btn:hover { 
            background-color: #4c51b3; 
            transform: translateY(-3px); 
        }
        .success-message { 
            color: green; 
            margin-bottom: 15px; 
        }
        .error-message { 
            color: red; 
            margin-bottom: 15px; 
        }
        /* Reverse Animation Keyframes */
        @keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(100%); /* Start off-screen to the right */
    }
    to {
        opacity: 1;
        transform: translateX(0); /* End at normal position */
    }
}
    </style>
</head>
<body>
<div class="container">
    <h1>Vinwo</h1>
    <h2>BY Anh Khoa</h2>
    <div class="tab-container">
        Register
    </div>
    
    <div id="register-form">
        <form action="register.php" method="POST">
            <?php if (!empty($success_message)) : ?>
                <div class="success-message"><?php echo $success_message; ?></div>
            <?php elseif (!empty($error_message)) : ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <div class="form-group">
                <i class="fas fa-user icon"></i>
                <input type="text" class="form-control" name="fullname" placeholder="Full Name" required value="<?php echo htmlspecialchars($fullname); ?>">
            </div>
            
            <div class="form-group">
                <i class="fas fa-user icon"></i>
                <input type="text" class="form-control" name="username" placeholder="Username" required value="<?php echo htmlspecialchars($username); ?>">
            </div>
            
            <div class="form-group">
                <i class="fas fa-envelope icon"></i>
                <input type="email" class="form-control" name="email" placeholder="Email" required value="<?php echo htmlspecialchars($email); ?>">
            </div>
            
            <div class="form-group">
                <i class="fas fa-lock icon"></i>
                <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                <span class="toggle-password" onclick="togglePassword()">
                    <i class="fas fa-eye" id="toggle-icon"></i>
                </span>
            </div>
            
            <button type="submit" class="btn">Register</button>
        </form>
    </div>
    
    <div class="link-container">
        <p>Already have an account? <a href="login.php">Login Here</a></p>
    </div>
    <div class="admin-link">
        <p>Are you an admin? <a href="admin_login.php">Admin Login Here</a></p>
    </div>
    <div class="contact-link">
        <a href="mailto:huynhanhkhoa2707@gmail.com">Contact Admin</a>
    </div>
</div>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById("password");
        const toggleIcon = document.getElementById("toggle-icon");
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            toggleIcon.classList.replace("fa-eye-slash", "fa-eye");
        } else {
            passwordInput.type = "password";
            toggleIcon.classList.replace("fa-eye", "fa-eye-slash");
        }
    }
</script>
<?php include 'templates/header.php'; ?>
</body>
</html>
