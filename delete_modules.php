<?php
// delete_modules.php
include 'db_connect.php'; // Ensure db_connect.php connects to your database

// Check if module IDs are provided for deletion
if (isset($_POST['module_ids']) && is_array($_POST['module_ids'])) {
    try {
        // Begin a transaction for bulk deletion
        $pdo->beginTransaction();

        // Prepare the delete statement
        $stmt = $pdo->prepare("DELETE FROM modules WHERE id = :id");

        // Execute delete for each selected module
        foreach ($_POST['module_ids'] as $id) {
            $stmt->execute(['id' => $id]);
        }

        // Commit the transaction
        $pdo->commit();

        // Redirect back to manage_modules.php with a success message
        header("Location: manage_modules.php?deleted=1");
        exit;
    } catch (PDOException $e) {
        // Rollback the transaction if an error occurs
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
} else {
    // Redirect to manage_modules.php if no module IDs are provided
    header("Location: manage_modules.php");
    exit;
}
?>
