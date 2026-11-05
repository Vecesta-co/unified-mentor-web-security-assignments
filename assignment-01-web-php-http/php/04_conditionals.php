<?php
/**
 * 04_conditionals.php — Conditional Statements in PHP
 *
 * Concept: if/elseif/else, switch, match, null coalescing,
 *          ternary operator, and security-relevant comparison pitfalls.
 * Expected output: Results of each conditional example.
 */

declare(strict_types=1);

// ── if / elseif / else ────────────────────────────────────────────────────────
echo "=== if / elseif / else ===\n";
$cvssScore = 7.8;

if ($cvssScore >= 9.0) {
    echo "Severity: Critical\n";
} elseif ($cvssScore >= 7.0) {
    echo "Severity: High\n";
} elseif ($cvssScore >= 4.0) {
    echo "Severity: Medium\n";
} elseif ($cvssScore > 0) {
    echo "Severity: Low\n";
} else {
    echo "Severity: Informational\n";
}

// ── switch ────────────────────────────────────────────────────────────────────
echo "\n=== switch ===\n";
$role = 'editor';

switch ($role) {
    case 'admin':
        echo "Access: Full system access\n";
        break;
    case 'editor':
        echo "Access: Content management\n";
        break;
    case 'viewer':
        echo "Access: Read-only\n";
        break;
    default:
        echo "Access: Denied\n";
}

// ── match expression (PHP 8.0+) ───────────────────────────────────────────────
echo "\n=== match ===\n";
$statusCode = 403;
$message = match($statusCode) {
    200     => 'OK',
    301     => 'Moved Permanently',
    302     => 'Found (Redirect)',
    400     => 'Bad Request',
    401     => 'Unauthorized',
    403     => 'Forbidden',
    404     => 'Not Found',
    500     => 'Internal Server Error',
    default => 'Unknown Status',
};
echo "HTTP $statusCode: $message\n";

// ── Ternary operator ──────────────────────────────────────────────────────────
echo "\n=== Ternary ===\n";
$isAuthenticated = true;
$userLabel = $isAuthenticated ? 'Logged In' : 'Guest';
echo "User: $userLabel\n";

// ── Null coalescing ───────────────────────────────────────────────────────────
echo "\n=== Null Coalescing ===\n";
$config = ['debug' => false, 'max_attempts' => 5];
$debug      = $config['debug'] ?? true;    // false (key exists)
$timeout    = $config['timeout'] ?? 30;    // 30 (key missing → default)
$attempts   = $config['max_attempts'] ?? 3;
echo "debug: ";    var_dump($debug);
echo "timeout: $timeout\n";
echo "attempts: $attempts\n";

// Null coalescing assignment
$config['log_level'] ??= 'warning';
echo "log_level (set by ??=): " . $config['log_level'] . "\n";

// ── Security Pitfall — loose vs strict comparison ─────────────────────────────
echo "\n=== SECURITY: loose vs strict comparison ===\n";
// PHP type juggling can cause authentication bypass!
$hash = "0e12345";   // looks like a "magic hash" in MD5

// DANGEROUS — loose comparison (==): "0e..." strings compare equal!
$dangerousEqual = ("0e12345" == "0e99999");
echo "LOOSE ==  (0e12345 == 0e99999): ";  var_dump($dangerousEqual);  // true!

// SAFE — strict comparison (===): compares type AND value
$safeEqual = ("0e12345" === "0e99999");
echo "STRICT === (0e12345 === 0e99999): "; var_dump($safeEqual);       // false

echo "\nSecurity rule: ALWAYS use === for security-critical comparisons.\n";
echo "Use password_verify() — never compare hashes with == or ===.\n";

// ── Spaceship operator ────────────────────────────────────────────────────────
echo "\n=== Spaceship Operator ===\n";
echo "(5 <=> 10): " . (5 <=> 10) . " (negative: left < right)\n";
echo "(10 <=> 5): " . (10 <=> 5) . " (positive: left > right)\n";
echo "(5 <=> 5): "  . (5 <=> 5)  . " (zero: equal)\n";
