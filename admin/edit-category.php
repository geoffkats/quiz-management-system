<?php
require_once '../config/config.php';

// Ensure user is logged in as admin
Session::checkAdminAuth();

// Get category ID from query string
if (!isset($_GET['id'])) {
    header('Location: /admin/categories.php');
    exit();
}

$categoryId = (int)$_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $description = sanitizeInput($_POST['description']);
    $division = strtoupper(sanitizeInput($_POST['division']));
    
    // Validate inputs
    if (empty($name)) {
        $error = "Category name is required";
    } else if (!in_array($division, ['JUNIOR', 'SENIOR', 'BOTH'])) {
        $error = "Invalid division";
    } else {
        try {
            // Check if another category with this name exists
            $stmt = $pdo->prepare("
                SELECT COUNT(*) 
                FROM categories 
                WHERE name = ? AND id != ?
            ");
            $stmt->execute([$name, $categoryId]);
            if ($stmt->fetchColumn() > 0) {
                $error = "A category with this name already exists";
            } else {
                // Update category
                $stmt = $pdo->prepare("
                    UPDATE categories 
                    SET name = ?, description = ?, division = ?
                    WHERE id = ?
                ");
                $stmt->execute([$name, $description, $division, $categoryId]);

                header('Location: /admin/categories.php?success=1');
                exit();
            }
        } catch (PDOException $e) {
            $error = "Failed to update category";
        }
    }
}

// Get category data
try {
    $stmt = $pdo->prepare("
        SELECT * FROM categories WHERE id = ?
    ");
    $stmt->execute([$categoryId]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$category) {
        header('Location: /admin/categories.php');
        exit();
    }
} catch (PDOException $e) {
    $error = "Failed to fetch category";
    $category = null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSC Quiz Arena - Edit Category</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/admin/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <nav class="admin-nav">
            <h1>NSC Quiz Arena</h1>
            <div class="admin-nav-right">
                <a href="/admin/" class="btn-secondary">Dashboard</a>
                <a href="/admin/categories.php" class="btn-secondary">Back to Categories</a>
                <a href="/admin/logout.php" class="btn-secondary">Logout</a>
            </div>
        </nav>

        <div class="admin-content">
            <div class="admin-form">
                <h2>Edit Category</h2>
                
                <?php if (isset($error)): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-row">
                        <label for="name">Category Name:</label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               required 
                               maxlength="100"
                               value="<?php echo htmlspecialchars($category['name']); ?>">
                    </div>

                    <div class="form-row">
                        <label for="description">Description:</label>
                        <textarea id="description" 
                                  name="description"><?php echo htmlspecialchars($category['description']); ?></textarea>
                    </div>

                    <div class="form-row">
                        <label for="division">Division:</label>
                        <select id="division" name="division" required>
                            <option value="BOTH" <?php echo $category['division'] === 'BOTH' ? 'selected' : ''; ?>>Both Divisions</option>
                            <option value="JUNIOR" <?php echo $category['division'] === 'JUNIOR' ? 'selected' : ''; ?>>Junior Only</option>
                            <option value="SENIOR" <?php echo $category['division'] === 'SENIOR' ? 'selected' : ''; ?>>Senior Only</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Update Category</button>
                        <a href="/admin/categories.php" class="btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>