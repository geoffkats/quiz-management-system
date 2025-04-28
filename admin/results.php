<?php
require_once '../config/config.php';

// Ensure user is logged in as admin
Session::checkAdminAuth();

// Get division filter from query string
$division = isset($_GET['division']) ? strtoupper($_GET['division']) : null;
if ($division && !validateDivision($division)) {
    $division = null;
}

// Get session filter from query string
$sessionId = isset($_GET['session']) ? (int)$_GET['session'] : null;

try {
    // Get available quiz sessions
    $sessionQuery = "
        SELECT id, division, started_at, ended_at 
        FROM quiz_sessions 
        WHERE ended_at IS NOT NULL
    ";
    if ($division) {
        $sessionQuery .= " AND division = ?";
    }
    $sessionQuery .= " ORDER BY started_at DESC";
    
    $stmt = $pdo->prepare($sessionQuery);
    if ($division) {
        $stmt->execute([$division]);
    } else {
        $stmt->execute();
    }
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get quiz results
    $resultsQuery = "
        SELECT 
            r.id,
            r.participant_id,
            r.score,
            r.total_time,
            r.quiz_session_id,
            p.name as participant_name,
            p.division,
            s.started_at,
            s.ended_at
        FROM quiz_results r
        JOIN participants p ON p.id = r.participant_id
        JOIN quiz_sessions s ON s.id = r.quiz_session_id
        WHERE 1=1
    ";
    $params = [];

    if ($division) {
        $resultsQuery .= " AND p.division = ?";
        $params[] = $division;
    }

    if ($sessionId) {
        $resultsQuery .= " AND r.quiz_session_id = ?";
        $params[] = $sessionId;
    }

    $resultsQuery .= " ORDER BY r.score DESC, r.total_time ASC";

    $stmt = $pdo->prepare($resultsQuery);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate statistics
    $stats = [];
    if (!empty($results)) {
        $scores = array_column($results, 'score');
        $stats = [
            'average' => number_format(array_sum($scores) / count($scores), 1),
            'highest' => max($scores),
            'lowest' => min($scores),
            'participants' => count($results)
        ];
    }

} catch (PDOException $e) {
    $error = "Failed to fetch results";
    $results = [];
    $sessions = [];
    $stats = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSC Quiz Arena - Quiz Results</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/admin/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <nav class="admin-nav">
            <h1>NSC Quiz Arena</h1>
            <div class="admin-nav-right">
                <a href="/admin/" class="btn-secondary">Dashboard</a>
                <span>Welcome, <?php echo htmlspecialchars(Session::get('admin_username')); ?></span>
                <a href="/admin/logout.php" class="btn-secondary">Logout</a>
            </div>
        </nav>

        <div class="admin-content">
            <div class="page-header">
                <h2>Quiz Results</h2>
                <div class="header-actions">
                    <div class="division-selector">
                        <a href="?division=JUNIOR" class="btn-tab <?php echo $division === 'JUNIOR' ? 'active' : ''; ?>">
                            Junior Division
                        </a>
                        <a href="?division=SENIOR" class="btn-tab <?php echo $division === 'SENIOR' ? 'active' : ''; ?>">
                            Senior Division
                        </a>
                        <?php if ($division): ?>
                            <a href="/admin/results.php" class="btn-tab">All Divisions</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if (!empty($sessions)): ?>
            <div class="session-filter">
                <form method="GET" class="filter-form">
                    <?php if ($division): ?>
                        <input type="hidden" name="division" value="<?php echo htmlspecialchars($division); ?>">
                    <?php endif; ?>
                    <label for="session">Quiz Session:</label>
                    <select name="session" id="session" onchange="this.form.submit()">
                        <option value="">All Sessions</option>
                        <?php foreach ($sessions as $session): ?>
                            <option value="<?php echo $session['id']; ?>" 
                                    <?php echo $sessionId === $session['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($session['division']); ?> - 
                                <?php echo date('Y-m-d H:i', strtotime($session['started_at'])); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
            <?php endif; ?>

            <?php if (!empty($stats)): ?>
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-label">Average Score</span>
                    <span class="stat-value"><?php echo $stats['average']; ?>%</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Highest Score</span>
                    <span class="stat-value"><?php echo $stats['highest']; ?>%</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Lowest Score</span>
                    <span class="stat-value"><?php echo $stats['lowest']; ?>%</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Total Participants</span>
                    <span class="stat-value"><?php echo $stats['participants']; ?></span>
                </div>
            </div>
            <?php endif; ?>

            <div class="table-container">
                <table class="admin-table results-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Name</th>
                            <th>Division</th>
                            <th>Score</th>
                            <th>Time Taken</th>
                            <th>Session Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $rank = 1;
                        foreach ($results as $result): 
                        ?>
                        <tr>
                            <td><?php echo $rank++; ?></td>
                            <td><?php echo htmlspecialchars($result['participant_name']); ?></td>
                            <td><?php echo htmlspecialchars($result['division']); ?></td>
                            <td><?php echo number_format($result['score'], 1); ?>%</td>
                            <td><?php echo floor($result['total_time'] / 60); ?>m <?php echo $result['total_time'] % 60; ?>s</td>
                            <td><?php echo date('Y-m-d H:i', strtotime($result['started_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($results)): ?>
                        <tr>
                            <td colspan="6" class="no-results">No results found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>