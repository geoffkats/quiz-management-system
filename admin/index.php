<?php
require_once '../config/config.php';

// Ensure user is logged in as admin
Session::checkAdminAuth();

// Get division statistics
$stmt = $pdo->prepare("
    SELECT 
        division,
        COUNT(*) as total_participants,
        SUM(ready) as ready_participants
    FROM participants 
    GROUP BY division
");
$stmt->execute();
$divisionStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check active quiz sessions
$stmt = $pdo->prepare("
    SELECT division, started_at 
    FROM quiz_sessions 
    WHERE active = 1
");
$stmt->execute();
$activeSessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSC Quiz Arena - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/admin/css/admin.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#2c3e50',
                            700: '#1e3a8a',
                            800: '#1e40af',
                            900: '#1e3a8a',
                            950: '#172554',
                        },
                    },
                },
            },
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="min-h-screen">
        <nav class="bg-primary-600 text-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold">NSC Quiz Arena</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm">Welcome, <?php echo htmlspecialchars(Session::get('admin_username')); ?></span>
                        <a href="/admin/logout.php" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-primary-700 hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
                    <?php 
                    echo htmlspecialchars($_SESSION['error']);
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4">
                    <?php 
                    echo htmlspecialchars($_SESSION['success']);
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <?php foreach ($divisionStats as $stat): ?>
                <div class="bg-white rounded-lg shadow-md p-6" data-division="<?php echo htmlspecialchars($stat['division']); ?>">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        <?php echo htmlspecialchars(ucfirst(strtolower($stat['division']))); ?> Division
                    </h2>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600">Total Participants:</span>
                            <span class="font-semibold total-participants"><?php echo $stat['total_participants']; ?></span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600">Ready Participants:</span>
                            <span class="font-semibold ready-participants"><?php echo $stat['ready_participants']; ?></span>
                        </div>
                    </div>
                    <div class="mt-6 flex space-x-4">
                        <?php 
                        $isActive = false;
                        foreach ($activeSessions as $session) {
                            if ($session['division'] === $stat['division']) {
                                $isActive = true;
                                break;
                            }
                        }
                        if ($isActive): 
                        ?>
                            <form method="POST" action="handle-quiz.php" class="flex-1">
                                <input type="hidden" name="action" value="end">
                                <input type="hidden" name="division" value="<?php echo htmlspecialchars($stat['division']); ?>">
                                <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                                    <span class="flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        End Quiz
                                    </span>
                                </button>
                            </form>
                        <?php else: ?>
                            <form method="POST" action="handle-quiz.php" class="flex-1">
                                <input type="hidden" name="action" value="start">
                                <input type="hidden" name="division" value="<?php echo htmlspecialchars($stat['division']); ?>">
                                <button type="submit" class="w-full bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors">
                                    <span class="flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Start Quiz
                                    </span>
                                </button>
                            </form>
                        <?php endif; ?>
                        <a href="participants.php?division=<?php echo urlencode($stat['division']); ?>" 
                           class="flex-1 bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors text-center">
                            Manage Participants
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Quick Actions</h2>
                <div class="flex flex-wrap gap-4">
                    <a href="add-participant.php" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        Add New Participant
                    </a>
                    <a href="manage-questions.php" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                        Manage Questions
                    </a>
                    <a href="quiz-results.php" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        View Quiz Results
                    </a>
                </div>
            </div>

            <?php if (!empty($activeSessions)): ?>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Active Quiz Sessions</h2>
                <div class="divide-y divide-gray-200">
                    <?php foreach ($activeSessions as $session): ?>
                    <div class="flex justify-between items-center py-4">
                        <span class="text-gray-900 font-medium">
                            <?php echo htmlspecialchars(ucfirst(strtolower($session['division']))); ?> Division
                        </span>
                        <span class="text-gray-500">
                            Started: <?php echo date('H:i:s', strtotime($session['started_at'])); ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="/admin/js/dashboard.js"></script>
</body>
</html>