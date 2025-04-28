<?php
require_once '../config/config.php';
Session::checkAdminAuth();

$division = isset($_GET['division']) ? strtoupper($_GET['division']) : 'JUNIOR';

try {
    // Get quiz results with participant names
    $stmt = $pdo->prepare("
        SELECT 
            qr.score,
            qr.total_time,
            qr.completed_at,
            p.name,
            p.participant_code,
            p.division
        FROM quiz_results qr
        JOIN participants p ON qr.participant_id = p.id
        WHERE qr.division = ?
        ORDER BY qr.score DESC, qr.total_time ASC
    ");
    $stmt->execute([$division]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get division statistics
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_participants,
            AVG(score) as average_score,
            MAX(score) as highest_score
        FROM quiz_results
        WHERE division = ?
    ");
    $stmt->execute([$division]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold">Quiz Results</h1>
            <div class="space-x-4">
                <a href="?division=JUNIOR" class="px-4 py-2 rounded <?php echo $division === 'JUNIOR' ? 'bg-blue-600' : 'bg-blue-500'; ?> text-white">Junior Division</a>
                <a href="?division=SENIOR" class="px-4 py-2 rounded <?php echo $division === 'SENIOR' ? 'bg-green-600' : 'bg-green-500'; ?> text-white">Senior Division</a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500">Total Participants</div>
                <div class="text-2xl font-bold"><?php echo $stats['total_participants']; ?></div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500">Average Score</div>
                <div class="text-2xl font-bold"><?php echo round($stats['average_score'], 1); ?>%</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500">Highest Score</div>
                <div class="text-2xl font-bold"><?php echo round($stats['highest_score'], 1); ?>%</div>
            </div>
        </div>

        <!-- Results Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold"><?php echo $division; ?> Division Results</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rank</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Completed</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($results as $index => $result): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm"><?php echo $index + 1; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($result['name']); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($result['participant_code']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $result['score'] >= 50 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                        <?php echo round($result['score']); ?>%
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php 
                                    $minutes = floor($result['total_time'] / 60);
                                    $seconds = $result['total_time'] % 60;
                                    printf("%02d:%02d", $minutes, $seconds);
                                    ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('H:i:s', strtotime($result['completed_at'])); ?>
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
<?php
} catch (PDOException $e) {
    $_SESSION['error'] = 'Database error occurred';
    header('Location: index.php');
    exit();
}
?>
