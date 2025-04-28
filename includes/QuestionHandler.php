<?php
class QuestionHandler {
    private $pdo;
    private $division;
    private $sessionId;

    public function __construct($pdo, $division, $sessionId) {
        $this->pdo = $pdo;
        $this->division = $division;
        $this->sessionId = $sessionId;
    }

    public function getRandomQuestions($limit = 10) {
        $stmt = $this->pdo->prepare("
            SELECT id, question_text, option_a, option_b, option_c, option_d, correct_answer 
            FROM questions 
            WHERE division = ? 
            ORDER BY RAND() 
            LIMIT ?
        ");
        $stmt->execute([$this->division, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function recordAnswer($participantId, $questionId, $selectedAnswer, $isCorrect, $responseTime) {
        $stmt = $this->pdo->prepare("
            INSERT INTO participant_answers 
            (participant_id, question_id, selected_answer, is_correct, response_time, quiz_session_id)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $participantId,
            $questionId,
            $selectedAnswer,
            $isCorrect,
            $responseTime,
            $this->sessionId
        ]);
    }

    public function saveQuizResult($participantId, $score, $totalTime) {
        $stmt = $this->pdo->prepare("
            INSERT INTO quiz_results 
            (participant_id, quiz_session_id, division, score, total_time)
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $participantId,
            $this->sessionId,
            $this->division,
            $score,
            $totalTime
        ]);
    }
}
