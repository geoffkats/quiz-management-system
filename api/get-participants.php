<?php
require_once '../config/config.php';

if (!Session::isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$division = Session::get('division');

try {
    $stmt = $pdo->prepare("
        SELECT id, name, ready 
        FROM participants 
        WHERE division = ? 
        ORDER BY name ASC
    ");
    $stmt->execute([$division]);
    $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'participants' => $participants,
        'quizActive' => isQuizActive($division)
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?>