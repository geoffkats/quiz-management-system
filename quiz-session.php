<?php
require_once 'config/config.php';
require_once 'includes/Questions.php';

if (!Session::isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$participant_id = Session::get('participant_id');
$division = Session::get('division');

try {
    // Get or initialize quiz session
    $questions = $_SESSION['quiz_questions'] ?? null;
    $currentQuestion = $_SESSION['current_question'] ?? 0;
    $score = $_SESSION['score'] ?? 0;

    if (!$questions) {
        // Get 30 random questions for the quiz
        $questions = Questions::getRandomQuestions($division);
        if (empty($questions)) {
            $_SESSION['error'] = 'No questions available for your division';
            header('Location: index.php');
            exit();
        }
        
        $_SESSION['quiz_questions'] = $questions;
        $_SESSION['current_question'] = 0;
        $_SESSION['score'] = 0;
        $_SESSION['start_time'] = time();
        $currentQuestion = 0;
    }

    $totalQuestions = count($questions);

    // Handle answer submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answer'])) {
        $selectedAnswer = $_POST['answer'];
        if ($selectedAnswer === $questions[$currentQuestion]['correct_answer']) {
            $_SESSION['score']++;
        }

        // Check if this was the last question
        if ($currentQuestion >= count($questions) - 1) {
            // Calculate final score - round to whole number
            $finalScore = round(($_SESSION['score'] / count($questions)) * 100);
            $timeTaken = time() - $_SESSION['start_time'];

            // Save quiz result
            $stmt = $pdo->prepare("
                INSERT INTO quiz_results 
                (participant_id, division, score, total_time) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$participant_id, $division, $finalScore, $timeTaken]);

            // Set completion data for results page
            $_SESSION['quiz_completed'] = true;
            $_SESSION['final_score'] = $finalScore;
            $_SESSION['time_taken'] = $timeTaken;

            // Clear quiz session
            unset($_SESSION['quiz_questions']);
            unset($_SESSION['current_question']);
            unset($_SESSION['score']);
            unset($_SESSION['start_time']);

            // Redirect to results page
            header('Location: quiz-results.php');
            exit();
        }

        // Move to next question
        $_SESSION['current_question']++;
        header('Location: quiz-session.php');
        exit();
    }

    // Get current question data
    $question = $questions[$currentQuestion] ?? null;
    if (!$question) {
        $_SESSION['error'] = 'Error loading question';
        header('Location: index.php');
        exit();
    }

} catch (Exception $e) {
    error_log("Quiz error: " . $e->getMessage());
    $_SESSION['error'] = 'Quiz error occurred';
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSC Quiz Arena - Question <?php echo $currentQuestion + 1; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-gray-50 to-primary-50">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <!-- Progress Header -->
            <div class="mb-6">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold text-gray-900">Question <?php echo $currentQuestion + 1; ?> of <?php echo $totalQuestions; ?></h1>
                    <div class="text-sm text-gray-500">Score: <?php echo $score; ?>/<?php echo $totalQuestions; ?></div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-4">
                    <div class="bg-primary-600 h-2 rounded-full transition-all" 
                         style="width: <?php echo ($currentQuestion / $totalQuestions) * 100; ?>%"></div>
                </div>
            </div>

            <!-- Question -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6"><?php echo htmlspecialchars($question['question_text'] ?? ''); ?></h2>
                <form method="POST" class="space-y-4">
                    <?php
                    $options = [
                        'A' => $question['option_a'] ?? '',
                        'B' => $question['option_b'] ?? '',
                        'C' => $question['option_c'] ?? '',
                        'D' => $question['option_d'] ?? ''
                    ];
                    foreach ($options as $key => $text): ?>
                        <button type="submit" name="answer" value="<?php echo $key; ?>" 
                                class="w-full text-left p-4 border-2 border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <span class="font-semibold"><?php echo $key; ?>.</span>
                            <?php echo htmlspecialchars($text); ?>
                        </button>
                    <?php endforeach; ?>
                </form>
            </div>

            <!-- Navigation Buttons -->
            <div class="flex justify-between items-center mt-8">
                <form method="POST" action="index.php" onsubmit="return confirm('Are you sure you want to exit the quiz? Your progress will be lost.');">
                    <button type="submit" name="exit_quiz" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Return to Waiting Room
                    </button>
                </form>
                <div class="text-sm text-gray-500">
                    Question <?php echo $currentQuestion + 1; ?> of <?php echo $totalQuestions; ?>
                </div>
            </div>

        </div>
    </div>
</body>
</html>
