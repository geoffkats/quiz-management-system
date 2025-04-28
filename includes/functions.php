<?php
function generateParticipantCode($length = 8) {
    $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[random_int(0, strlen($characters) - 1)];
    }
    return $code;
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function validateDivision($division) {
    return in_array(strtoupper($division), ['JUNIOR', 'SENIOR']);
}

function calculateScore($correctAnswers, $totalQuestions) {
    return ($correctAnswers / $totalQuestions) * 100;
}

function getDivisionAverage($division) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT IFNULL(AVG(score), 0) as average FROM quiz_results WHERE division = ?");
    $stmt->execute([$division]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return round($result['average'] ?? 0, 2);
}

function getRank($participantId, $division) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT COUNT(*) + 1 as participant_rank
        FROM quiz_results r1
        WHERE division = ? 
        AND score > (
            SELECT score 
            FROM quiz_results r2 
            WHERE r2.participant_id = ?
            AND r2.division = ? 
            ORDER BY score DESC 
            LIMIT 1
        )
    ");
    $stmt->execute([$division, $participantId, $division]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['participant_rank'] ?? 0;
}

function isQuizActive($division) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT active FROM quiz_sessions WHERE division = ? AND active = 1");
    $stmt->execute([$division]);
    return $stmt->rowCount() > 0;
}

function getReadyParticipantCount($division) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM participants WHERE division = ? AND ready = 1");
    $stmt->execute([$division]);
    return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
?>