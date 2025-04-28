<?php
require_once '../../config/config.php';

// Ensure admin is logged in
if (!Session::isAdmin()) {
    jsonResponse(['error' => 'Unauthorized'], 401);
}

try {
    // Get division statistics
    $stmt = $pdo->prepare("
        SELECT 
            division,
            COUNT(*) as total,
            SUM(ready) as ready
        FROM participants 
        GROUP BY division
    ");
    $stmt->execute();
    $divisionStats = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $divisionStats[$row['division']] = [
            'total' => (int)$row['total'],
            'ready' => (int)$row['ready']
        ];
    }

    // Get active sessions
    $stmt = $pdo->prepare("
        SELECT division, started_at 
        FROM quiz_sessions 
        WHERE active = 1
    ");
    $stmt->execute();
    $activeSessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    jsonResponse([
        'divisionStats' => $divisionStats,
        'activeSessions' => $activeSessions
    ]);

} catch (PDOException $e) {
    jsonResponse(['error' => 'Database error'], 500);
}
?>