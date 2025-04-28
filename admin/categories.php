<?php
require_once '../config/config.php';

// Ensure user is logged in as admin
Session::checkAdminAuth();

// Handle category deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$_POST['delete_id']]);
        header('Location: /admin/categories.php?success=1');
        exit();
    } catch (PDOException $e) {
        if ($e->getCode() == '23000') { // Foreign key constraint violation
            $error = "Cannot delete category that has questions assigned to it";
        } else {
            $error = "Failed to delete category";
        }
    }
}

// Get all categories with question counts
try {
    $stmt = $pdo->prepare("
        SELECT 
            c.*,
            COUNT(q.id) as question_count
        FROM categories c
        LEFT JOIN questions q ON q.category_id = c.id
        GROUP BY c.id
        ORDER BY c.name ASC
    ");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch categories";
    $categories = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSC Quiz Arena - Manage Categories</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/admin/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <nav class="admin-nav">
            <h1>NSC Quiz Arena</h1>
            <div class="admin-nav-right">
                <a href="/admin/" class="btn-secondary">Dashboard</a>
                <a href="/admin/questions.php" class="btn-secondary">Manage Questions</a>
                <a href="/admin/logout.php" class="btn-secondary">Logout</a>
            </div>
        </nav>

        <div class="admin-content">
            <div class="page-header">
                <h2>Manage Categories</h2>
                <div class="header-actions">
                    <a href="/admin/add-category.php" class="btn-primary">Add Category</a>
                </div>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="success-message">Operation completed successfully</div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div class="table-container">
                <table class="admin-table categories-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Division</th>
                            <th>Questions</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                            <td><?php echo htmlspecialchars($category['description']); ?></td>
                            <td>
                                <span class="division-badge division-<?php echo strtolower($category['division']); ?>">
                                    <?php echo $category['division']; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if ($category['question_count'] > 0): ?>
                                    <a href="/admin/questions.php?category=<?php echo $category['id']; ?>" 
                                       class="question-count">
                                        <?php echo $category['question_count']; ?> questions
                                    </a>
                                <?php else: ?>
                                    <span class="empty-count">No questions</span>
                                <?php endif; ?>
                            </td>
                            <td class="actions">
                                <a href="/admin/edit-category.php?id=<?php echo $category['id']; ?>" 
                                   class="btn-secondary">Edit</a>
                                <?php if ($category['question_count'] === '0'): ?>
                                    <form method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this category?')"
                                          style="display: inline;">
                                        <input type="hidden" name="delete_id" value="<?php echo $category['id']; ?>">
                                        <button type="submit" class="btn-warning">Delete</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="5" class="no-results">No categories found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>