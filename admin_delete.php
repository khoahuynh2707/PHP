<?php
session_start();
// Database connection
include 'db_connect.php'; // Ensure this file contains your database connection setup

if (isset($_GET['id'])) {
    $postId = $_GET['id'];

    // Prepare and execute deletion query
    try {
        $deleteQuery = "DELETE FROM posts WHERE id = :postId";
        $deleteStmt = $pdo->prepare($deleteQuery);
        $deleteStmt->bindValue(':postId', $postId);
        $deleteStmt->execute();

        // Redirect back to manage posts page with success message
        $_SESSION['message'] = "Post deleted successfully!";
        header("Location: manage_posts.php");
        exit();
    } catch (PDOException $e) {
        // Handle error
        $_SESSION['error'] = "Error deleting post: " . $e->getMessage();
        header("Location: manage_posts.php");
        exit();
    }
} else {
    // If no ID is provided, redirect to manage posts page
    $_SESSION['error'] = "No post ID specified for deletion.";
    header("Location: manage_posts.php");
    exit();
}
?>
