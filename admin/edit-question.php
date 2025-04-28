<?php
require_once '../config/config.php';

// Ensure user is logged in as admin
Session::checkAdminAuth();

// Get question ID from query string
if (!isset($_GET['id'])) {
    header('Location: /admin/questions.php');
    exit();
}

$questionId = (int)$_GET['id'];

// Get all categories
try {
    $stmt = $pdo->prepare("SELECT id, name, division FROM categories ORDER BY name ASC");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch categories";
    $categories = [];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $questionText = sanitizeInput($_POST['question_text']);
    $optionA = sanitizeInput($_POST['option_a']);
    $optionB = sanitizeInput($_POST['option_b']);
    $optionC = sanitizeInput($_POST['option_c']);
    $optionD = sanitizeInput($_POST['option_d']);
    $correctAnswer = strtoupper(sanitizeInput($_POST['correct_answer']));
    $division = strtoupper(sanitizeInput($_POST['division']));
    $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    
    // Validate inputs
    if (empty($questionText) || empty($optionA) || empty($optionB) || 
        empty($optionC) || empty($optionD) || empty($correctAnswer) || 
        !validateDivision($division)) {
        $error = "All fields are required";
    } else if (!in_array($correctAnswer, ['A', 'B', 'C', 'D'])) {
        $error = "Invalid correct answer";
    } else {
        try {
            // If category is selected, validate it exists and is compatible with question division
            if ($categoryId) {
                $stmt = $pdo->prepare("
                    SELECT division FROM categories WHERE id = ?
                ");
                $stmt->execute([$categoryId]);
                $category = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$category) {
                    throw new Exception("Selected category does not exist");
                }
                
                if ($category['division'] !== 'BOTH' && $category['division'] !== $division) {
                    throw new Exception("Selected category is not compatible with question division");
                }
            }

            // Update question
            $stmt = $pdo->prepare("
                UPDATE questions 
                SET question_text = ?, 
                    option_a = ?, 
                    option_b = ?, 
                    option_c = ?, 
                    option_d = ?, 
                    correct_answer = ?, 
                    division = ?, 
                    category_id = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $questionText, $optionA, $optionB, $optionC, $optionD, 
                $correctAnswer, $division, $categoryId, $questionId
            ]);

            header('Location: /admin/questions.php?division=' . urlencode($division) . '&success=1');
            exit();
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// Get question data
try {
    $stmt = $pdo->prepare("
        SELECT * FROM questions WHERE id = ?
    ");
    $stmt->execute([$questionId]);
    $question = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$question) {
        header('Location: /admin/questions.php');
        exit();
    }
} catch (PDOException $e) {
    $error = "Failed to fetch question";
    $question = null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSC Quiz Arena - Edit Question</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/admin/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <nav class="admin-nav">
            <h1>NSC Quiz Arena</h1>
            <div class="admin-nav-right">
                <a href="/admin/" class="btn-secondary">Dashboard</a>
                <a href="/admin/questions.php" class="btn-secondary">Back to Questions</a>
                <a href="/admin/logout.php" class="btn-secondary">Logout</a>
            </div>
        </nav>

        <div class="admin-content">
            <div class="admin-form question-form">
                <h2>Edit Question</h2>
                
                <?php if (isset($error)): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-row">
                        <label for="division">Division:</label>
                        <select id="division" name="division" required onchange="updateCategoryOptions()">
                            <option value="JUNIOR" <?php echo $question['division'] === 'JUNIOR' ? 'selected' : ''; ?>>Junior</option>
                            <option value="SENIOR" <?php echo $question['division'] === 'SENIOR' ? 'selected' : ''; ?>>Senior</option>
                        </select>
                    </div>

                    <div class="form-row">
                        <label for="category_id">Category:</label>
                        <select id="category_id" name="category_id">
                            <option value="">Uncategorized</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" 
                                        data-division="<?php echo $category['division']; ?>"
                                        <?php echo $question['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-row">
                        <label for="question_text">Question:</label>
                        <textarea id="question_text" 
                                  name="question_text" 
                                  required><?php echo htmlspecialchars($question['question_text']); ?></textarea>
                    </div>

                    <div class="form-grid">
                        <div class="form-row">
                            <label for="option_a">Option A:</label>
                            <input type="text" 
                                   id="option_a" 
                                   name="option_a" 
                                   required 
                                   value="<?php echo htmlspecialchars($question['option_a']); ?>">
                        </div>

                        <div class="form-row">
                            <label for="option_b">Option B:</label>
                            <input type="text" 
                                   id="option_b" 
                                   name="option_b" 
                                   required 
                                   value="<?php echo htmlspecialchars($question['option_b']); ?>">
                        </div>

                        <div class="form-row">
                            <label for="option_c">Option C:</label>
                            <input type="text" 
                                   id="option_c" 
                                   name="option_c" 
                                   required 
                                   value="<?php echo htmlspecialchars($question['option_c']); ?>">
                        </div>

                        <div class="form-row">
                            <label for="option_d">Option D:</label>
                            <input type="text" 
                                   id="option_d" 
                                   name="option_d" 
                                   required 
                                   value="<?php echo htmlspecialchars($question['option_d']); ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <label for="correct_answer">Correct Answer:</label>
                        <select id="correct_answer" name="correct_answer" required>
                            <option value="A" <?php echo $question['correct_answer'] === 'A' ? 'selected' : ''; ?>>Option A</option>
                            <option value="B" <?php echo $question['correct_answer'] === 'B' ? 'selected' : ''; ?>>Option B</option>
                            <option value="C" <?php echo $question['correct_answer'] === 'C' ? 'selected' : ''; ?>>Option C</option>
                            <option value="D" <?php echo $question['correct_answer'] === 'D' ? 'selected' : ''; ?>>Option D</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Update Question</button>
                        <a href="/admin/questions.php?division=<?php echo urlencode($question['division']); ?>" 
                           class="btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function updateCategoryOptions() {
        const divisionSelect = document.getElementById('division');
        const categorySelect = document.getElementById('category_id');
        const selectedDivision = divisionSelect.value;
        
        // Enable all options first
        for (let option of categorySelect.options) {
            if (option.value === '') continue; // Skip "Uncategorized" option
            
            const categoryDivision = option.getAttribute('data-division');
            option.disabled = categoryDivision !== 'BOTH' && categoryDivision !== selectedDivision;
            
            // If current selection becomes disabled, reset selection
            if (option.disabled && option.selected) {
                categorySelect.value = '';
            }
        }
    }

    // Run on page load
    updateCategoryOptions();
    </script>
</body>
</html>