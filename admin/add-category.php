<?php
require_once '../config/config.php';

// Ensure user is logged in as admin
Session::checkAdminAuth();

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
            // Check if category already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE name = ?");
            $stmt->execute([$name]);
            if ($stmt->fetchColumn() > 0) {
                $error = "A category with this name already exists";
            } else {
                // Insert new category
                $stmt = $pdo->prepare("
                    INSERT INTO categories (name, description, division)
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$name, $description, $division]);

                header('Location: /admin/categories.php?success=1');
                exit();
            }
        } catch (PDOException $e) {
            $error = "Failed to add category";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSC Quiz Arena - Add Category</title>
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
                <h2>Add New Category</h2>
                
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
                               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                    </div>

                    <div class="form-row">
                        <label for="description">Description:</label>
                        <textarea id="description" 
                                  name="description"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                    </div>

                    <div class="form-row">
                        <label for="division">Division:</label>
                        <select id="division" name="division" required>
                            <option value="">Select division</option>
                            <option value="BOTH" <?php echo isset($_POST['division']) && $_POST['division'] === 'BOTH' ? 'selected' : ''; ?>>Both Divisions</option>
                            <option value="JUNIOR" <?php echo isset($_POST['division']) && $_POST['division'] === 'JUNIOR' ? 'selected' : ''; ?>>Junior Only</option>
                            <option value="SENIOR" <?php echo isset($_POST['division']) && $_POST['division'] === 'SENIOR' ? 'selected' : ''; ?>>Senior Only</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Add Category</button>
                        <a href="/admin/categories.php" class="btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>