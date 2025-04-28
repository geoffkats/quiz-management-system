<?php
require_once '../config/config.php';
require_once '../includes/Questions.php';

Session::checkAdminAuth();

$division = isset($_GET['division']) ? strtoupper($_GET['division']) : 'JUNIOR';
$questions = Questions::getQuestions($division);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Questions - NSC Quiz Arena</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold">Manage Quiz Questions</h1>
            <div class="space-x-4">
                <a href="?division=JUNIOR" class="px-4 py-2 rounded bg-blue-500 text-white">Junior Division</a>
                <a href="?division=SENIOR" class="px-4 py-2 rounded bg-green-500 text-white">Senior Division</a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4"><?php echo $division; ?> Division Questions</h2>
            <div class="space-y-4">
                <?php foreach ($questions as $question): ?>
                <div class="border p-4 rounded">
                    <p class="font-medium"><?php echo htmlspecialchars($question['question_text']); ?></p>
                    <div class="mt-2 grid grid-cols-2 gap-4">
                        <div class="text-sm">A: <?php echo htmlspecialchars($question['option_a']); ?></div>
                        <div class="text-sm">B: <?php echo htmlspecialchars($question['option_b']); ?></div>
                        <div class="text-sm">C: <?php echo htmlspecialchars($question['option_c']); ?></div>
                        <div class="text-sm">D: <?php echo htmlspecialchars($question['option_d']); ?></div>
                    </div>
                    <div class="mt-2 text-sm text-green-600">
                        Correct Answer: <?php echo $question['correct_answer']; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>
