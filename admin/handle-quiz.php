<?php
require_once '../config/config.php';
Session::checkAdminAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Invalid request method';
    header('Location: index.php');
    exit();
}

$action = $_POST['action'] ?? '';
$division = $_POST['division'] ?? '';

if (!in_array($division, ['JUNIOR', 'SENIOR'])) {
    $_SESSION['error'] = 'Invalid division';
    header('Location: index.php');
    exit();
}

try {
    if ($action === 'start') {
        // End any existing active sessions for this division
        $stmt = $pdo->prepare("
            UPDATE quiz_sessions 
            SET active = 0, ended_at = CURRENT_TIMESTAMP 
            WHERE division = ? AND active = 1
        ");
        $stmt->execute([$division]);

        // Create new quiz session
        $stmt = $pdo->prepare("
            INSERT INTO quiz_sessions (division, active, started_at) 
            VALUES (?, 1, CURRENT_TIMESTAMP)
        ");
        $stmt->execute([$division]);

        $_SESSION['success'] = "Quiz started for {$division} division";

    } elseif ($action === 'end') {
        // End active session
        $stmt = $pdo->prepare("
            UPDATE quiz_sessions 
            SET active = 0, ended_at = CURRENT_TIMESTAMP 
            WHERE division = ? AND active = 1
        ");
        $stmt->execute([$division]);

        $_SESSION['success'] = "Quiz ended for {$division} division";
    }

} catch (PDOException $e) {
    error_log("Quiz handler error: " . $e->getMessage());
    $_SESSION['error'] = 'Database error occurred';
}

header('Location: index.php');
exit();
