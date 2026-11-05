<?php
/**
 * 06_functions.php — Functions in PHP
 *
 * Concept: Declaring and calling functions, type declarations, default
 *          parameters, variadic functions, closures, arrow functions,
 *          recursion, and return types.
 * Expected output: Return values from each function demonstration.
 */

declare(strict_types=1);

// ── Basic function ────────────────────────────────────────────────────────────
echo "=== Basic function ===\n";

function greet(string $name, string $greeting = "Hello"): string
{
    return "$greeting, $name!";
}

echo greet("Alice") . "\n";
echo greet("Bob", "Welcome") . "\n";

// ── Type declarations ─────────────────────────────────────────────────────────
echo "\n=== Type-safe function ===\n";

function calculateCVSS(float $base, float $temporal, float $environmental): float
{
    // Simplified CVSS combination — not the official formula
    $combined = ($base * 0.6) + ($temporal * 0.2) + ($environmental * 0.2);
    return round(min($combined, 10.0), 1);
}

$score = calculateCVSS(9.8, 7.5, 8.0);
echo "CVSS Combined Score: $score\n";

// ── Variadic function ─────────────────────────────────────────────────────────
echo "\n=== Variadic function ===\n";

function logEvent(string $level, string ...$messages): void
{
    $timestamp = date('Y-m-d H:i:s');
    foreach ($messages as $msg) {
        echo "[$timestamp] [$level] $msg\n";
    }
}

logEvent('INFO', 'Application started', 'Database connected');
logEvent('WARN', 'Failed login attempt from 127.0.0.1');
logEvent('ERROR', 'SQL error on users table');

// ── Return types ──────────────────────────────────────────────────────────────
echo "\n=== Nullable return type ===\n";

function findUser(int $id): ?array
{
    $users = [
        1 => ['username' => 'alice', 'role' => 'admin'],
        2 => ['username' => 'bob',   'role' => 'user'],
    ];
    return $users[$id] ?? null;
}

$user = findUser(1);
if ($user !== null) {
    echo "Found: {$user['username']} ({$user['role']})\n";
}

$missing = findUser(99);
var_dump($missing);  // NULL

// ── Closures ──────────────────────────────────────────────────────────────────
echo "\n=== Closures ===\n";

$multiply = function(int $a, int $b): int {
    return $a * $b;
};
echo "3 × 7 = " . $multiply(3, 7) . "\n";

// Closure capturing external variable with 'use'
$prefix = "ALERT";
$formatAlert = function(string $msg) use ($prefix): string {
    return "[$prefix] $msg";
};
echo $formatAlert("XSS payload detected") . "\n";

// ── Arrow functions (PHP 7.4+) ────────────────────────────────────────────────
echo "\n=== Arrow functions ===\n";
$threshold = 7.0;
$findings = [
    ['name' => 'SQLi',   'cvss' => 9.8],
    ['name' => 'XSS',    'cvss' => 7.4],
    ['name' => 'CSRF',   'cvss' => 5.4],
    ['name' => 'LFI',    'cvss' => 7.5],
    ['name' => 'Info',   'cvss' => 2.1],
];
$highSeverity = array_filter($findings, fn($f) => $f['cvss'] >= $threshold);
echo "Findings >= $threshold CVSS:\n";
foreach ($highSeverity as $f) {
    echo "  {$f['name']}: {$f['cvss']}\n";
}

// ── Recursion ─────────────────────────────────────────────────────────────────
echo "\n=== Recursion ===\n";

function factorial(int $n): int
{
    if ($n <= 1) {
        return 1;
    }
    return $n * factorial($n - 1);
}

for ($i = 0; $i <= 10; $i++) {
    echo "$i! = " . factorial($i) . "\n";
}

// ── First-class callable syntax (PHP 8.1+) ────────────────────────────────────
echo "\n=== First-class callables ===\n";
$strlenFn = strlen(...);
$words     = ['security', 'web', 'vulnerability', 'XSS'];
$lengths   = array_map($strlenFn, $words);
echo "Word lengths: ";
print_r(array_combine($words, $lengths));
