<?php
require_once '../config/config.php';
Session::checkAdminAuth();

$division = isset($_GET['division']) ? strtoupper($_GET['division']) : 'JUNIOR';

try {
    // Get latest quiz session for division
    $stmt = $pdo->prepare("
        SELECT id, started_at 
        FROM quiz_sessions 
        WHERE division = ? 
        ORDER BY started_at DESC 
        LIMIT 1
    ");
    $stmt->execute([$division]);
    $session = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get all participant scores
    $stmt = $pdo->prepare("
        SELECT 
            p.name,
            p.participant_code,
            qr.score,
            qr.total_time,
            qr.completed_at
        FROM participants p
        LEFT JOIN quiz_results qr ON p.id = qr.participant_id
        WHERE p.division = ?
        AND (qr.quiz_session_id = ? OR qr.quiz_session_id IS NULL)
        ORDER BY qr.score DESC
    ");
    $stmt->execute([$division, $session['id'] ?? null]);
    $scores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = 'Database error occurred';
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Scores - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold">Student Scores</h1>
            <div class="space-x-4">
                <a href="?division=JUNIOR" class="px-4 py-2 rounded bg-blue-500 text-white">Junior Division</a>
                <a href="?division=SENIOR" class="px-4 py-2 rounded bg-green-500 text-white">Senior Division</a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold">
                    <?php echo htmlspecialchars($division); ?> Division Results
                    <?php if ($session): ?>
                        <span class="text-sm text-gray-500">
                            (Session: <?php echo date('Y-m-d H:i', strtotime($session['started_at'])); ?>)
                        </span>
                    <?php endif; ?>
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($scores as $score): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($score['name']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo htmlspecialchars($score['participant_code']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo $score['score'] ? round($score['score']) . '%' : '-'; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php 
                                    if ($score['total_time']) {
                                        $minutes = floor($score['total_time'] / 60);
                                        $seconds = $score['total_time'] % 60;
                                        echo sprintf("%02d:%02d", $minutes, $seconds);
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($score['score']): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Completed
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Not Started
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
