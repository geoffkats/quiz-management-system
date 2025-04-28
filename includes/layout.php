<?php
function renderHeader($title, $additionalClasses = '') {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?> - NSC Quiz Arena</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
</head>
<body class="min-h-screen bg-gray-100">
    <div class="<?php echo 'container mx-auto px-4 py-8 ' . $additionalClasses; ?>">
<?php
}

function renderFooter() {
?>
    </div>
</body>
</html>
<?php
}

function renderNavbar($isAdmin = false) {
?>
<nav class="bg-primary-600 text-white shadow-lg mb-8">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <h1 class="text-xl font-bold">NSC Quiz Arena</h1>
            <div class="flex items-center space-x-4">
                <?php if ($isAdmin): ?>
                    <a href="<?php echo ADMIN_URL; ?>/" class="hover:bg-primary-700 px-3 py-2 rounded">Dashboard</a>
                    <a href="<?php echo ADMIN_URL; ?>/questions.php" class="hover:bg-primary-700 px-3 py-2 rounded">Questions</a>
                    <a href="<?php echo ADMIN_URL; ?>/participants.php" class="hover:bg-primary-700 px-3 py-2 rounded">Participants</a>
                    <a href="<?php echo ADMIN_URL; ?>/logout.php" class="hover:bg-primary-700 px-3 py-2 rounded">Logout</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/logout.php" class="hover:bg-primary-700 px-3 py-2 rounded">Logout</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<?php
}

function renderCard($title, $content, $actions = null) {
?>
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <?php if ($title): ?>
        <h2 class="text-xl font-semibold mb-4"><?php echo htmlspecialchars($title); ?></h2>
    <?php endif; ?>
    
    <div class="space-y-4">
        <?php echo $content; ?>
    </div>
    
    <?php if ($actions): ?>
        <div class="mt-6 flex justify-end space-x-4">
            <?php echo $actions; ?>
        </div>
    <?php endif; ?>
</div>
<?php
}

function renderButton($text, $href = null, $type = 'primary', $attributes = '') {
    $baseClasses = 'px-4 py-2 rounded font-medium transition-colors';
    $typeClasses = [
        'primary' => 'bg-primary-600 hover:bg-primary-700 text-white',
        'secondary' => 'bg-gray-600 hover:bg-gray-700 text-white',
        'danger' => 'bg-red-600 hover:bg-red-700 text-white',
    ];
    
    $classes = $baseClasses . ' ' . ($typeClasses[$type] ?? $typeClasses['primary']);
    
    if ($href) {
        return sprintf('<a href="%s" class="%s" %s>%s</a>', 
            $href, $classes, $attributes, htmlspecialchars($text));
    } else {
        return sprintf('<button class="%s" %s>%s</button>', 
            $classes, $attributes, htmlspecialchars($text));
    }
}

function renderAlert($message, $type = 'success') {
    $typeClasses = [
        'success' => 'bg-green-100 text-green-700 border-green-500',
        'error' => 'bg-red-100 text-red-700 border-red-500',
        'warning' => 'bg-yellow-100 text-yellow-700 border-yellow-500',
        'info' => 'bg-blue-100 text-blue-700 border-blue-500',
    ];
    
    $classes = 'p-4 mb-6 rounded-lg border-l-4 ' . ($typeClasses[$type] ?? $typeClasses['info']);
    
    return sprintf('<div class="%s">%s</div>', $classes, htmlspecialchars($message));
}
?>