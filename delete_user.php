<?php
include 'db_connect.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $username = $_POST['username'];

    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE username = ?");
        $stmt->execute([$username]);

        $response['success'] = true;
        $response['message'] = "User deleted successfully.";
    } catch (PDOException $e) {
        $response['message'] = "An error occurred: " . $e->getMessage();
    }
} else {
    $response['message'] = "Invalid request.";
}

echo json_encode($response);
?>