<?php
require_once '../config/config.php';

if (!Session::isLoggedIn()) {
    jsonResponse(['error' => 'Unauthorized'], 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$participantId = Session::get('participant_id');
$questionId = Session::get('current_question_id');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['answer']) || !isset($input['responseTime'])) {
    jsonResponse(['error' => 'Invalid input'], 400);
}

$selectedAnswer = $input['answer'];
$responseTime = (int)$input['responseTime'];

try {
    // Get correct answer
    $stmt = $pdo->prepare("
        SELECT correct_answer 
        FROM questions 
        WHERE id = ?
    ");
    $stmt->execute([$questionId]);
    $question = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$question) {
        jsonResponse(['error' => 'Question not found'], 404);
    }

    $isCorrect = ($selectedAnswer === $question['correct_answer']);

    // Record answer
    $stmt = $pdo->prepare("
        INSERT INTO participant_answers 
        (participant_id, question_id, selected_answer, is_correct, response_time, quiz_session_id) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $participantId,
        $questionId,
        $selectedAnswer,
        $isCorrect,
        $responseTime,
        Session::get('quiz_session_id')
    ]);

    // Check if this was the last question
    $currentIndex = Session::get('current_question_index');
    $questions = Session::get('quiz_questions');
    
    if ($currentIndex >= count($questions) - 1) {
        // Calculate final score
        calculateAndSaveResults($participantId, Session::get('quiz_session_id'));
        
        // Clear quiz session data
        Session::remove('quiz_session_id');
        Session::remove('quiz_questions');
        Session::remove('current_question_index');
        Session::remove('quiz_start_time');
        
        echo json_encode([
            'complete' => true,
            'redirect' => 'results.php'
        ]);
        exit();
    }

    // Move to next question
    Session::set('current_question_index', $currentIndex + 1);
    
    echo json_encode([
        'success' => true,
        'correct' => $isCorrect,
        'correctAnswer' => $question['correct_answer']
    ]);

} catch (Exception $e) {
    error_log("Answer submission error: " . $e->getMessage());
    echo json_encode(['error' => 'Failed to submit answer']);
}
?>