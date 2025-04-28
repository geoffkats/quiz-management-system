<?php
require_once '../../config/config.php';

// Ensure admin is logged in
if (!Session::isAdmin()) {
    jsonResponse(['error' => 'Unauthorized'], 401);
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['division'])) {
    jsonResponse(['error' => 'Division is required'], 400);
}

$division = strtoupper($input['division']);
if (!validateDivision($division)) {
    jsonResponse(['error' => 'Invalid division'], 400);
}

try {
    // Check if there's an active quiz for this division
    $stmt = $pdo->prepare("
        SELECT id 
        FROM quiz_sessions 
        WHERE division = ? AND active = 1
    ");
    $stmt->execute([$division]);
    $session = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$session) {
        jsonResponse(['error' => 'No active quiz found for this division'], 400);
    }

    $pdo->beginTransaction();

    // Mark quiz session as ended
    $stmt = $pdo->prepare("
        UPDATE quiz_sessions 
        SET active = 0, ended_at = CURRENT_TIMESTAMP 
        WHERE id = ?
    ");
    $stmt->execute([$session['id']]);

    // Reset ready status for all participants in this division
    $stmt = $pdo->prepare("
        UPDATE participants 
        SET ready = 0 
        WHERE division = ?
    ");
    $stmt->execute([$division]);

    $pdo->commit();
    jsonResponse(['success' => true]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    jsonResponse(['error' => 'Database error'], 500);
}
?>