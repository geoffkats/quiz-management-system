<?php
require_once '../../config/config.php';

// Ensure user is logged in as admin
Session::checkAdminAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
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
    // Check if quiz is already active
    $stmt = $pdo->prepare("SELECT id FROM quiz_sessions WHERE division = ? AND active = 1");
    $stmt->execute([$division]);
    if ($stmt->rowCount() > 0) {
        jsonResponse(['error' => 'Quiz is already active for this division'], 400);
    }

    $pdo->beginTransaction();

    // Create new quiz session
    $stmt = $pdo->prepare("
        INSERT INTO quiz_sessions (division, active, started_at) 
        VALUES (?, 1, CURRENT_TIMESTAMP)
    ");
    $stmt->execute([$division]);
    $sessionId = $pdo->lastInsertId();

    // Reset any previous answers for this division
    $stmt = $pdo->prepare("
        DELETE pa FROM participant_answers pa 
        INNER JOIN participants p ON p.id = pa.participant_id 
        WHERE p.division = ?
    ");
    $stmt->execute([$division]);

    $pdo->commit();
    jsonResponse(['success' => true, 'session_id' => $sessionId]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    jsonResponse(['error' => 'Database error'], 500);
}
?>