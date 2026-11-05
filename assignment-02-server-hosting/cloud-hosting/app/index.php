<?php
/**
 * index.php — Benign Cloud Hosting Demonstration Application
 * Assignment 02 — Web Server Hosting Lab (Task 2)
 *
 * Demonstrates clean PHP execution, safe output encoding,
 * and database integration on remote shared hosting.
 */

declare(strict_types=1);

require_once __DIR__ . '/db.php';

$pdo = get_db_connection();
$db_status = "Disconnected";
$records = [];
$error_message = "";

if ($pdo !== null) {
    $db_status = "Connected via PDO (MySQL)";
    try {
        $stmt = $pdo->query("SELECT id, author, note, created_at FROM guest_notes ORDER BY id DESC LIMIT 10");
        $records = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error_message = "Table query notice: Ensure guest_notes table is created via phpMyAdmin.";
    }
} else {
    $db_status = "Database configuration pending in db.php";
}

function esc(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cloud Hosting Lab — Benign Demonstration App</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #f8fafc; color: #1e293b; margin: 2rem; }
        .card { background: white; padding: 2rem; border-radius: 8px; max-width: 700px; margin: 0 auto; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; }
        .badge-ok { background: #dcfce7; color: #166534; }
        .badge-warn { background: #fef3c7; color: #92400e; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { text-align: left; padding: 0.75rem; border-bottom: 1px solid #e2e8f0; }
        th { background: #f1f5f9; font-weight: 600; }
        footer { margin-top: 2rem; text-align: center; font-size: 0.875rem; color: #64748b; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Cloud Hosting Lab Demonstration</h1>
        <p>This benign application proves live deployment, PHP execution, and relational database connectivity on remote free-tier hosting.</p>
        
        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 1.5rem 0;">

        <h3>Server Environment & Status:</h3>
        <ul>
            <li><strong>PHP Version:</strong> <?= esc(PHP_VERSION) ?></li>
            <li><strong>Server Software:</strong> <?= esc($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown Web Server') ?></li>
            <li><strong>Database Status:</strong> <span class="badge <?= ($pdo !== null) ? 'badge-ok' : 'badge-warn' ?>"><?= esc($db_status) ?></span></li>
        </ul>

        <?php if ($error_message): ?>
            <p style="color: #b45309; background: #fffbeb; padding: 0.75rem; border-radius: 4px;"><?= esc($error_message) ?></p>
        <?php endif; ?>

        <?php if (!empty($records)): ?>
            <h3>Records Retrieved from Remote MySQL:</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Author</th>
                        <th>Note</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $r): ?>
                        <tr>
                            <td><?= esc((string)$r['id']) ?></td>
                            <td><?= esc($r['author']) ?></td>
                            <td><?= esc($r['note']) ?></td>
                            <td><?= esc($r['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <footer>
            Assignment 02 — Web Server Hosting | Built for Educational Evaluation
        </footer>
    </div>
</body>
</html>
