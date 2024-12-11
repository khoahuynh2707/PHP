<?php 
session_start(); // Start the session
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit;
}

// Include the PDO connection file
include 'db_connect.php';

$user_id = $_SESSION['user_id'];
$error = ""; // Variable to store error messages

if (isset($_POST['update'])) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $current_password = $_POST['password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Retrieve current password from the database
    $query = "SELECT password FROM users WHERE id = :id";
    $stmt = $pdo->prepare($query); // Prepare the query
    $stmt->bindParam(':id', $user_id); // Bind the user ID
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the result
    $db_password = $result['password'];

    // Verify the current password
    if (!password_verify($current_password, $db_password)) {
        $error = "Your current password is incorrect."; // Error message if wrong password
    } else {
        // Check if new password and confirm password match
        if ($new_password === $confirm_password) {
            // Update user profile, including name, email, and password
            $new_password_hashed = password_hash($new_password, PASSWORD_BCRYPT);
            $update_query = "UPDATE users SET full_name = :full_name, email = :email, password = :password WHERE id = :id";
            $stmt = $conn->prepare($update_query);
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $new_password_hashed);
            $stmt->bindParam(':id', $user_id);
            $stmt->execute();
            header('Location: edit_profile.php'); // Redirect after successful update
            exit;
        } else {
            $error = "New password and confirm password do not match.";
        }
    }
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&amp;display=swap" rel="stylesheet">
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
        h1{
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Profile</h1>
        <form method="POST">
            <label for="full_name">Full Name:</label>
            <input type="text" name="full_name" required>
            <label for="email">Email:</label>
            <input type="email" name="email" required>
            <label for="password">Current Password:</label>
            <input type="password" name="password">
            <label for="new_password">New Password:</label>
            <input type="password" name="new_password">
            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" name="confirm_password">
            <div class="submit">
            <button type="submit"  name="update">Update Profile</button>
        </form>
        </div>
        <!-- Display error message if any -->
        <?php if (!empty($error)): ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>
       <div class="submit">
        <a href="Home.php" class="back">Back to Home</a>
    </div>

    <span id="PING_IFRAME_FORM_DETECTION" style="display: none;"></span>

<?php include 'templates/headercontent.php'; ?> <!-- Include header content -->
</body>
</html>