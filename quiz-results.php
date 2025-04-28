<?php
require_once 'config/config.php';

// Check if quiz is completed
if (!Session::isLoggedIn() || !isset($_SESSION['quiz_completed'])) {
    header('Location: index.php');
    exit();
}

// Get completion data
$finalScore = $_SESSION['final_score'] ?? 0;
$timeTaken = $_SESSION['time_taken'] ?? 0;
$participant_name = Session::get('name');
$division = Session::get('division');

// Clear completion flags
unset($_SESSION['quiz_completed']);
unset($_SESSION['final_score']);
unset($_SESSION['time_taken']);

$minutes = floor($timeTaken / 60);
$seconds = $timeTaken % 60;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results - NSC Quiz Arena</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .gradient-text { background: linear-gradient(45deg, #3B82F6, #2563EB); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50">
    <div class="max-w-4xl mx-auto px-4 py-12">
        <!-- Congratulations Card -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden mb-8">
            <div class="relative pt-16 pb-10 px-8 text-center">
                <div class="absolute top-0 left-0 w-full h-3 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
                
                <h1 class="text-4xl font-bold mb-2 gradient-text">
                    Congratulations, <?php echo htmlspecialchars($participant_name); ?>!
                </h1>
                <p class="text-gray-600 mb-8">You've completed the quiz!</p>
                
                <div class="text-7xl font-bold text-blue-600 mb-4 animate-pulse">
                    <?php echo round(num: $finalScore); ?>%
                </div>
                
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-blue-50 text-blue-700 text-sm font-medium">
                    <?php echo htmlspecialchars(ucfirst(strtolower($division))); ?> Division
                </div>
            </div>
        </div>

        <!-- Statistics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Rank Card 
            <div class="bg-white rounded-2xl shadow-lg p-6 text-center transform hover:scale-105 transition-transform">
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-1">#N/A</div>
                <p class="text-sm text-gray-500">out of N/A participants</p>
            </div>
-->
            <!-- Average Score Card 
            <div class="bg-white rounded-2xl shadow-lg p-6 text-center transform hover:scale-105 transition-transform">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-1">N/A%</div>
                <p class="text-sm text-gray-500">Division Average</p>
            </div>
-->
            <!-- Time Card -->
            <div class="bg-white rounded-2xl shadow-lg p-6 text-center transform hover:scale-105 transition-transform">
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-1">
                    <?php printf("%02d:%02d", $minutes, $seconds); ?>
                </div>
                <p class="text-sm text-gray-500">Completion Time</p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="text-center space-y-4">
               
                <form method="POST" action="index.php" onsubmit="return confirm('Are you sure you want to exit the quiz? Your progress will be lost.');">
                    <button type="submit" name="exit_quiz" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Return to Waiting Room
                    </button>
                </form>
              
            </a>
        </div>
    </div>
</body>
</html>
