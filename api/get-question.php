<?php
require_once '../config/config.php';

if (!Session::isLoggedIn()) {
    jsonResponse(['error' => 'Unauthorized'], 401);
}

$participantId = Session::get('participant_id');
$division = Session::get('division');

try {
    // Check if quiz is still active
    if (!isQuizActive($division)) {
        jsonResponse(['quizComplete' => true]);
    }

    // Get count of answered questions
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as answered
        FROM participant_answers
        WHERE participant_id = ?
    ");
    $stmt->execute([$participantId]);
    $answered = $stmt->fetch(PDO::FETCH_ASSOC)['answered'];

    // Check if all questions are answered
    if ($answered >= 10) {
        // End quiz participation
        jsonResponse(['quizComplete' => true]);
    }

    // Get next unanswered question
    $stmt = $pdo->prepare("
        SELECT q.id, q.question_text, q.option_a, q.option_b, q.option_c, q.option_d
        FROM questions q
        WHERE q.division = ?
        AND q.id NOT IN (
            SELECT question_id 
            FROM participant_answers 
            WHERE participant_id = ?
        )
        ORDER BY RAND()
        LIMIT 1
    ");
    $stmt->execute([$division, $participantId]);
    
    if ($question = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Store question ID in session for validation during answer submission
        Session::set('current_question_id', $question['id']);
        
        // Remove correct answer before sending to client
        unset($question['correct_answer']);
        jsonResponse($question);
    } else {
        jsonResponse(['quizComplete' => true]);
    }
} catch (PDOException $e) {
    jsonResponse(['error' => 'Database error'], 500);
}
?>