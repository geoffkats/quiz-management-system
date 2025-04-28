<?php
require_once '../config/config.php';

// Ensure user is logged in as admin
Session::checkAdminAuth();

// Get filter parameters
$division = isset($_GET['division']) ? strtoupper($_GET['division']) : 'JUNIOR';
$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;

if (!in_array($division, ['JUNIOR', 'SENIOR'])) {
    $division = 'JUNIOR';
}

// Get all categories for filter dropdown
try {
    $stmt = $pdo->prepare("SELECT id, name, division FROM categories ORDER BY name ASC");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch categories";
    $categories = [];
}

// Get questions with category information
try {
    $params = [$division];
    $categoryFilter = '';
    if ($categoryId) {
        $categoryFilter = 'AND q.category_id = ?';
        $params[] = $categoryId;
    }

    $stmt = $pdo->prepare("
        SELECT q.*, c.name as category_name 
        FROM questions q 
        LEFT JOIN categories c ON q.category_id = c.id
        WHERE q.division = ? $categoryFilter
        ORDER BY q.id DESC
    ");
    $stmt->execute($params);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch questions";
    $questions = [];
}

$successMessage = isset($_GET['success']) ? "Question updated successfully!" : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSC Quiz Arena - Questions</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/admin/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <nav class="admin-nav">
            <h1>NSC Quiz Arena</h1>
            <div class="admin-nav-right">
                <a href="/admin/" class="btn-secondary">Dashboard</a>
                <a href="/admin/add-question.php" class="btn-primary">Add New Question</a>
                <a href="/admin/logout.php" class="btn-secondary">Logout</a>
            </div>
        </nav>

        <div class="admin-content">
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($successMessage): ?>
                <div class="success-message"><?php echo htmlspecialchars($successMessage); ?></div>
            <?php endif; ?>

            <div class="filter-section">
                <div class="filter-group">
                    <label for="division">Division:</label>
                    <select id="division" onchange="updateFilters()">
                        <option value="JUNIOR" <?php echo $division === 'JUNIOR' ? 'selected' : ''; ?>>Junior</option>
                        <option value="SENIOR" <?php echo $division === 'SENIOR' ? 'selected' : ''; ?>>Senior</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="category">Category:</label>
                    <select id="category" onchange="updateFilters()">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <?php if ($category['division'] === 'BOTH' || $category['division'] === $division): ?>
                                <option value="<?php echo $category['id']; ?>" 
                                        <?php echo $categoryId == $category['id'] ? 'selected' : ''; ?>
                                        data-division="<?php echo $category['division']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="questions-list">
                <?php if (empty($questions)): ?>
                    <p class="no-items">No questions found.</p>
                <?php else: ?>
                    <?php foreach ($questions as $question): ?>
                        <div class="question-item">
                            <div class="question-header">
                                <div class="question-meta">
                                    <span class="question-id">#<?php echo $question['id']; ?></span>
                                    <?php if ($question['category_name']): ?>
                                        <span class="question-category"><?php echo htmlspecialchars($question['category_name']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="question-actions">
                                    <a href="/admin/edit-question.php?id=<?php echo $question['id']; ?>" 
                                       class="btn-secondary">Edit</a>
                                </div>
                            </div>
                            <p class="question-text"><?php echo htmlspecialchars($question['question_text']); ?></p>
                            <div class="question-options">
                                <div class="option<?php echo $question['correct_answer'] === 'A' ? ' correct' : ''; ?>">
                                    <span class="option-label">A:</span>
                                    <?php echo htmlspecialchars($question['option_a']); ?>
                                </div>
                                <div class="option<?php echo $question['correct_answer'] === 'B' ? ' correct' : ''; ?>">
                                    <span class="option-label">B:</span>
                                    <?php echo htmlspecialchars($question['option_b']); ?>
                                </div>
                                <div class="option<?php echo $question['correct_answer'] === 'C' ? ' correct' : ''; ?>">
                                    <span class="option-label">C:</span>
                                    <?php echo htmlspecialchars($question['option_c']); ?>
                                </div>
                                <div class="option<?php echo $question['correct_answer'] === 'D' ? ' correct' : ''; ?>">
                                    <span class="option-label">D:</span>
                                    <?php echo htmlspecialchars($question['option_d']); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    function updateFilters() {
        const division = document.getElementById('division').value;
        const category = document.getElementById('category').value;
        const url = new URL(window.location.href);
        
        url.searchParams.set('division', division);
        if (category) {
            url.searchParams.set('category', category);
        } else {
            url.searchParams.delete('category');
        }
        
        window.location.href = url.toString();
    }

    // Update category options based on selected division
    document.getElementById('division').addEventListener('change', function() {
        const categorySelect = document.getElementById('category');
        const selectedDivision = this.value;
        
        Array.from(categorySelect.options).forEach(option => {
            if (option.value === '') return; // Skip "All Categories" option
            const categoryDivision = option.getAttribute('data-division');
            option.style.display = (categoryDivision === 'BOTH' || categoryDivision === selectedDivision) ? '' : 'none';
        });
        
        // Reset category selection if it's no longer valid for the selected division
        const selectedOption = categorySelect.options[categorySelect.selectedIndex];
        if (selectedOption.style.display === 'none') {
            categorySelect.value = '';
        }
    });
    </script>
</body>
</html>