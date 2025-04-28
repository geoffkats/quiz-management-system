<?php
require_once '../config/config.php';

if (!Session::isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$participantId = Session::get('participant_id');
$division = Session::get('division');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['ready'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Ready status is required']);
    exit;
}

$ready = (bool)$input['ready'];

try {
    // Check if quiz is already active
    if (isQuizActive($division)) {
        http_response_code(400);
        echo json_encode(['error' => 'Cannot change status while quiz is active']);
        exit;
    }

    // Update participant status
    $stmt = $pdo->prepare("
        UPDATE participants 
        SET ready = :ready 
        WHERE id = :id
    ");
    
    $result = $stmt->execute([
        ':ready' => $ready ? 1 : 0,
        ':id' => $participantId
    ]);

    if (!$result) {
        throw new Exception('Failed to update status');
    }

    // Get updated ready count
    $readyCount = getReadyParticipantCount($division);

    echo json_encode([
        'success' => true,
        'readyCount' => $readyCount
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>