<?php
require_once 'config/config.php';

// If user is not logged in, redirect to login page
if (!Session::isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Handle quiz exit - clear quiz session data
if (isset($_POST['exit_quiz'])) {
    Session::remove('quiz_questions');
    Session::remove('current_question');
    Session::remove('quiz_started');
    Session::remove('score');
    $_SESSION['message'] = 'You have exited the quiz.';
}

$participant_id = Session::get('participant_id');
$division = Session::get('division');
$participant_name = Session::get('name');

// Check if quiz is active for the participant's division
if (isQuizActive($division)) {
    header('Location: quiz-session.php');
    exit();
}

// Get participants from the same division
$stmt = $pdo->prepare("
    SELECT id, name, ready 
    FROM participants 
    WHERE division = ? 
    ORDER BY name ASC
");
$stmt->execute([$division]);
$participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- ========== automatic refresh after 5 seconds ========== -->
    <meta http-equiv="refresh" content="5">
    <meta name="description" content="NSC Quiz Arena - Waiting Room">
    <meta name="author" content="NSC Quiz Team">
    <meta name="keywords" content="NSC, Quiz, Arena, Waiting Room">
    <meta name="robots" content="index, follow">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSC Quiz Arena - Waiting Room</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
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
                        },
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    }
                },
            },
        }
    </script>
</head>
<body class="min-h-screen bg-gradient-to-br from-gray-50 to-primary-50" data-participant-id="<?php echo htmlspecialchars($participant_id); ?>">
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-2xl font-bold text-primary-600">NSC Quiz Arena</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Welcome, <?php echo htmlspecialchars($participant_name); ?></span>
                    <button id="logoutBtn" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <a href="logout.php" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-primary-700 hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                            Logout
                        </a>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header Section -->
        <div class="text-center mb-12 animate__animated animate__fadeIn">
            <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                <span class="block">Welcome to</span>
                <span class="block text-primary-600">Quiz Arena</span>
            </h1>
            <p class="mt-3 max-w-md mx-auto text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl">
                <?php echo htmlspecialchars(ucfirst(strtolower($division))); ?> Division
            </p>
        </div>

        <!-- Status Card -->
        <div class="max-w-md mx-auto bg-white rounded-2xl shadow-xl overflow-hidden mb-12">
            <div class="p-8 text-center">
                <div class="animate-pulse-slow mb-6">
                    <div class="mx-auto w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Waiting for Quiz to Start</h2>
                <p class="text-gray-600 mb-6">Please wait while the administrator starts the quiz for your division.</p>
                <div class="flex justify-center">
                    <span class="inline-flex items-center px-4 py-2 bg-gray-100 rounded-full text-sm font-medium text-gray-800">
                        <svg class="w-4 h-4 mr-2 text-primary-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Quiz Starting Soon
                    </span>
                </div>
            </div>
        </div>

        <!-- Participants List -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <?php echo ucfirst(strtolower($division)); ?> Division Participants
                </h3>
            </div>
            <ul class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                <?php foreach ($participants as $participant): ?>
                <li class="px-6 py-4 hover:bg-gray-50 <?php echo $participant['id'] === $participant_id ? 'bg-blue-50' : ''; ?>">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0">
                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-primary-100">
                                    <span class="text-lg font-medium text-primary-700"><?php echo htmlspecialchars($participant['name'][0]); ?></span>
                                </span>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($participant['name']); ?>
                                    <?php echo $participant['id'] === $participant_id ? ' (You)' : ''; ?>
                                </p>
                                <p class="text-sm text-gray-500">
                                    <?php echo $participant['ready'] ? 'Ready' : 'Not Ready'; ?>
                                </p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $participant['ready'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                            <?php echo $participant['ready'] ? 'Ready' : 'Waiting'; ?>
                        </span>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <script src="/js/waiting-room.js"></script>
</body>
</html>