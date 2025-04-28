<?php
require_once '../config/config.php';

// Ensure user is logged in as admin
Session::checkAdminAuth();

// Get division from query string
$division = isset($_GET['division']) ? strtoupper($_GET['division']) : 'JUNIOR';
if (!validateDivision($division)) {
    header('Location: add-participant.php?division=JUNIOR');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $division = strtoupper(sanitizeInput($_POST['division']));
    
    if (empty($name)) {
        $error = "Name is required";
    } else if (!validateDivision($division)) {
        $error = "Invalid division";
    } else {
        try {
            do {
                $code = generateParticipantCode();
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM participants WHERE participant_code = ?");
                $stmt->execute([$code]);
            } while ($stmt->fetchColumn() > 0);

            $stmt = $pdo->prepare("
                INSERT INTO participants (name, participant_code, division)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$name, $code, $division]);

            header('Location: participants.php?division=' . urlencode($division) . '&success=1');
            exit();
        } catch (PDOException $e) {
            $error = "Failed to add participant";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSC Quiz Arena - Add Participant</title>
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
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-primary-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold">NSC Quiz Arena</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="text-white hover:text-white/80">Dashboard</a>
                    <a href="participants.php" class="text-white hover:text-white/80">All Participants</a>
                    <span>Welcome, <?php echo htmlspecialchars(Session::get('admin_username')); ?></span>
                    <a href="logout.php" 
                       class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-primary-700 hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Add New Participant</h2>
            </div>

            <?php if (isset($error)): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-4 m-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700"><?php echo htmlspecialchars($error); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" class="p-6 space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Participant Name
                    </label>
                    <div class="mt-1">
                        <input type="text" 
                               id="name" 
                               name="name" 
                               required 
                               maxlength="100"
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                               placeholder="Enter participant's name">
                    </div>
                </div>

                <div>
                    <label for="division" class="block text-sm font-medium text-gray-700">
                        Division
                    </label>
                    <div class="mt-1">
                        <select id="division" 
                                name="division" 
                                required
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            <option value="">Select division</option>
                            <option value="JUNIOR" <?php echo $division === 'JUNIOR' ? 'selected' : ''; ?>>Junior Division</option>
                            <option value="SENIOR" <?php echo $division === 'SENIOR' ? 'selected' : ''; ?>>Senior Division</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-4">
                    <a href="participants.php?division=<?php echo urlencode($division); ?>"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Add Participant
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-8 bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Note</h3>
            </div>
            <div class="p-6">
                <p class="text-sm text-gray-600">
                    A unique participant code will be automatically generated when you add the participant. 
                    The code will be displayed in the participants list, where you can copy and share it with the participant.
                </p>
            </div>
        </div>
    </div>
</body>
</html>