<?php
/**
 * 10_database.php — PDO Database Interaction and Prepared Statements
 *
 * Concept: Connecting to MySQL/MariaDB using PDO, enforcing UTF-8 charset,
 *          prepared statements to prevent SQL injection, and parameter binding.
 * Expected output: Text output illustrating connection status, prepared statement
 *                  query execution, safe parameter binding, and error handling.
 */

declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');

echo "=== PHP PDO Database Security Lab ===\n\n";

// In production, credentials come from environment variables (.env)
$db_host = getenv('APP_DB_HOST') ?: '127.0.0.1';
$db_name = getenv('APP_DB_NAME') ?: 'dvwa';
$db_user = getenv('APP_DB_USER') ?: 'dvwa';
$db_pass = getenv('APP_DB_PASSWORD') ?: 'change_me_dvwa';
$db_port = getenv('APP_DB_PORT') ?: '3306';

$dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false, // Forces native server prepared statements
];

try {
    echo "[*] Connecting to database {$db_host}:{$db_port}/{$db_name}...\n";
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
    echo "[+] Database connection established successfully via PDO.\n\n";

    // Example 1: Prepared Statement with Named Parameters
    echo "[*] Executing parameterized SELECT query with named placeholders:\n";
    $user_id_input = 1; // Simulated user input
    $stmt = $pdo->prepare("SELECT user_id, first_name, last_name, user FROM users WHERE user_id = :id LIMIT 1");
    $stmt->execute(['id' => $user_id_input]);
    $user = $stmt->fetch();

    if ($user) {
        echo "    User Found: ID={$user['user_id']}, Username={$user['user']}, Name={$user['first_name']} {$user['last_name']}\n";
    } else {
        echo "    No user found with ID={$user_id_input}.\n";
    }

    // Example 2: Parameterized query neutralizing SQL injection payload
    echo "\n[*] Simulating malicious SQL Injection payload: ' OR '1'='1\n";
    $malicious_input = "' OR '1'='1";
    $safe_stmt = $pdo->prepare("SELECT user_id, user FROM users WHERE user = :username");
    $safe_stmt->execute(['username' => $malicious_input]);
    $results = $safe_stmt->fetchAll();
    echo "    Results returned: " . count($results) . " (Expected: 0 because payload is treated as a literal string literal, not executable SQL).\n";

} catch (PDOException $e) {
    echo "[-] Database Error: " . $e->getMessage() . "\n";
    echo "    (Note: If running outside Docker without active MySQL instance, ensure docker compose is up)\n";
}

echo "\n[+] Database interaction demonstration complete.\n";

