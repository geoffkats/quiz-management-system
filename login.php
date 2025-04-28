<?php
require_once 'config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $participantCode = sanitizeInput($_POST['participant_code']);
    
    $stmt = $pdo->prepare("SELECT id, name, division FROM participants WHERE participant_code = ?");
    $stmt->execute([$participantCode]);
    
    if ($participant = $stmt->fetch(PDO::FETCH_ASSOC)) {
        Session::set('participant_id', $participant['id']);
        Session::set('name', $participant['name']);
        Session::set('division', $participant['division']);
        
        header('Location: ' . BASE_URL . '/index.php');
        exit();
    } else {
        $error = "Invalid participant code. Please try again.";
    }
}

require_once 'includes/layout.php';
renderHeader('Login');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSC Quiz Arena - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/css/style.css">
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
<body class="bg-gradient-to-br from-primary-600 to-primary-800 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <div class="bg-white rounded-2xl shadow-2xl p-8 space-y-8">
                <div class="text-center">
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">NSC Quiz Arena</h1>
                    <h2 class="text-xl text-gray-600">Participant Login</h2>
                </div>

                <?php if (isset($error)): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700"><?php echo htmlspecialchars($error); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo BASE_URL; ?>/login.php" class="space-y-6">
                    <div>
                        <label for="participant_code" class="block text-sm font-medium text-gray-700">
                            Enter Your Participant Code
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="text" 
                                   id="participant_code" 
                                   name="participant_code" 
                                   required 
                                   pattern="[2-9A-HJ-NP-Z]{8}"
                                   class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 sm:text-sm uppercase tracking-widest"
                                   placeholder="Enter your 8-character code">
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            Please enter the 8-character code provided to you
                        </p>
                    </div>

                    <div class="flex flex-col space-y-4">
                        <button type="submit" 
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition duration-150 ease-in-out">
                            Enter Quiz Arena
                        </button>
                        
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-white text-gray-500">or</span>
                            </div>
                        </div>

                        <a href="<?php echo ADMIN_URL; ?>/login.php" 
                           class="text-center text-sm text-primary-600 hover:text-primary-700 font-medium transition duration-150 ease-in-out">
                            Administrator Login
                        </a>
                    </div>
                </form>
            </div>

            <div class="text-center mt-8">
                <p class="text-sm text-primary-100">
                    © <?php echo date('Y'); ?> NSC Quiz Arena. All rights reserved.
                </p>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('participant_code').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^2-9A-HJ-NP-Z]/g, '').substr(0, 8);
    });
    </script>
</body>
</html>

<?php renderFooter(); ?>
