<?php 
include 'db_connect.php'; 
session_start(); // Start the session

// Initialize variables
$username = '';
$password = '';
$error_message = '';

// Process the login form when submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch user data from the database
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Password is correct, set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: Home.php'); // Redirect to homepage after login
            exit;
        } else {
            $error_message = "Incorrect username or password!";
        }
    } catch (PDOException $e) {
        $error_message = "An error occurred: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
    /* Slider Container Animation */
        .slider-container {
            width: 100%;
            max-width: 500px;
            overflow: hidden;
            position: relative;
            height: auto;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: transparent;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transform: translateX(-100%);
            animation: slideInForm 1s ease-out forwards;
        }

        /* Main Container */
        .container {
            width: 100%;
            padding: 40px;
            text-align: center;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        /* Form Group Styling */
        .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            position: relative;
            opacity: 0;
            animation: fadeInFields 0.8s ease-out forwards;
            animation-delay: 0.2s;
        }

        /* Input Field Styling */
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

        .form-control:focus {
            border-color: #5e60ce;
            box-shadow: 0 0 8px rgba(94, 96, 206, 0.2);
        }

        /* Icon Styling */
        .icon {
            font-size: 22px;
            color: #888;
        }

        /* Toggle Password Eye Icon */
        .toggle-password {
            position: absolute;
            right: 10px;
            cursor: pointer;
            color: #888;
            font-size: 18px;
            transition: color 0.3s;
        }

        .toggle-password:hover {
            color: #5e60ce;
        }

        /* Button Styling */
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

        .btn:active {
            transform: translateY(0);
        }

        /* Link Styling */
        .link-container, .admin-link, .contact-link {
            margin-top: 20px;
        }

        a {
            color: #5e60ce;
            text-decoration: none;
            transition: color 0.3s;
        }

        a:hover {
            color: #4c51b3;
        }

        /* Animations */
        @keyframes slideInForm {
            from {
                transform: translateX(-100%);
            }
            to {
                transform: translateX(0);
            }
        }

        @keyframes fadeInFields {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="slider-container">
        <div class="container">
            <h1>Vinwo</h1>
            <h2>BY Anh Khoa</h2>
            <div class="tab-container">
                Login
            </div>
            
            <div id="login-form">
                <form action="login.php" method="POST">
                    <?php if (!empty($error_message)) : ?>
                        <div class="error-message"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <i class="fas fa-user icon"></i>
                        <input type="text" class="form-control" name="username" placeholder="Username" required>
                    </div>
                    
                    <div class="form-group">
                        <i class="fas fa-lock icon"></i>
                        <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                        <span class="toggle-password" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggle-icon"></i>
                        </span>
                    </div>
                    <div class="link-container">
                        <p>Forgot your password? <a href="forgot_password.php">Reset it here</a></p>
                    </div>
                    <button type="submit" class="btn">Login</button>
                </form>
            </div>
            <div class="link-container">
                <p>Don't have an account? <a href="register.php">Register Here</a></p>
            </div>
            <div class="admin-link">
                <a href="admin_login.php">Admin Login</a>
            </div>
            <div class="contact-link">
                <a href="mailto:huynhanhkhoa2707@gmail.com">Contact Admin</a>
            </div>
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
</body>
</html>

    <?php include 'templates/header.php'; ?>
</body>
</html>

