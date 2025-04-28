<?php
require_once 'config/config.php';

// Ensure user is logged in
if (!Session::isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$participant_id = Session::get('participant_id');

try {
    // Get the latest quiz result
    $stmt = $pdo->prepare("
        SELECT * FROM quiz_results 
        WHERE participant_id = ? 
        ORDER BY completed_at DESC 
        LIMIT 1
    ");
    $stmt->execute([$participant_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        $_SESSION['error'] = 'No quiz results found';
        header('Location: index.php');
        exit();
    }

    // Display results
    $score = round($result['score'], 1);
    $minutes = floor($result['total_time'] / 60);
    $seconds = $result['total_time'] % 60;

    // Calculate statistics
    $avgScore = round($result['avg_score'], 1);
    $totalParticipants = $result['total_participants'];

    // Get participant's rank
    $stmt = $pdo->prepare("
        SELECT COUNT(*) + 1 as rank
        FROM quiz_results
        WHERE quiz_session_id = ? AND score > ?
    ");
    $stmt->execute([$result['quiz_session_id'], $result['score']]);
    $rank = $stmt->fetch(PDO::FETCH_ASSOC)['rank'];

} catch (PDOException $e) {
    error_log("Results error: " . $e->getMessage());
    $_SESSION['error'] = 'Error retrieving results';
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results - NSC Quiz Arena</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-gray-50 to-primary-50">
    <div class="max-w-4xl mx-auto px-4 py-12">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="p-8 text-center">
                <h1 class="text-3xl font-bold text-gray-900 mb-4">Quiz Results</h1>
                <div class="text-6xl font-extrabold text-primary-600 mb-4"><?php echo $score; ?>%</div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900">#<?php echo $rank; ?></div>
                        <p class="text-gray-600">Your Rank</p>
                        <p class="text-sm text-gray-500">of <?php echo $totalParticipants; ?> participants</p>
                    </div>

                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900"><?php echo $avgScore; ?>%</div>
                        <p class="text-gray-600">Division Average</p>
                    </div>

                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900">
                            <?php echo sprintf('%02d:%02d', $minutes, $seconds); ?>
                        </div>
                        <p class="text-gray-600">Time Taken</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 text-center">
            <a href="index.php" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                Return to Waiting Room
            </a>
        </div>
    </div>
</body>
</html>