<?php
require_once 'config/config.php';

// Clear any ready status before logout
if (Session::isLoggedIn()) {
    $participantId = Session::get('participant_id');
    try {
        $stmt = $pdo->prepare("UPDATE participants SET ready = 0 WHERE id = ?");
        $stmt->execute([$participantId]);
    } catch (PDOException $e) {
        // Continue with logout even if update fails
    }
}

// Destroy the session
Session::destroy();

// Redirect to login page
header('Location: ' . BASE_URL . '/login.php');
// Optionally, you can set a message to inform the user about successful logout

exit();
?>