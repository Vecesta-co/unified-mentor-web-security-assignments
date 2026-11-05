<?php
/**
 * 05_loops.php — Loops in PHP
 *
 * Concept: for, while, do-while, foreach loops; break and continue;
 *          generator functions; practical security use cases.
 * Expected output: Loop iterations and computed values.
 */

declare(strict_types=1);

// ── for loop ──────────────────────────────────────────────────────────────────
echo "=== for loop ===\n";
$total = 0;
for ($i = 1; $i <= 10; $i++) {
    $total += $i;
    echo "i=$i, running total=$total\n";
}
echo "Sum of 1-10: $total\n";

// ── while loop ────────────────────────────────────────────────────────────────
echo "\n=== while loop — Rate limiting simulation ===\n";
$attempts    = 0;
$maxAttempts = 5;
$success     = false;
$passwords   = ['wrong', 'wrong', 'wrong', 'correct', 'never_reached'];

while ($attempts < $maxAttempts && !$success) {
    $attempts++;
    $password = $passwords[$attempts - 1];
    if ($password === 'correct') {
        $success = true;
        echo "Attempt $attempts: SUCCESS\n";
    } else {
        echo "Attempt $attempts: FAILED (using '$password')\n";
    }
}

if (!$success) {
    echo "Account locked after $maxAttempts failed attempts.\n";
}

// ── do-while loop ─────────────────────────────────────────────────────────────
echo "\n=== do-while loop ===\n";
$count = 0;
do {
    echo "Executed $count times\n";
    $count++;
} while ($count < 3);

// ── foreach loop ──────────────────────────────────────────────────────────────
echo "\n=== foreach — Associative array ===\n";
$headers = [
    'Content-Security-Policy' => "default-src 'self'",
    'X-Frame-Options'         => 'DENY',
    'X-Content-Type-Options'  => 'nosniff',
    'Referrer-Policy'         => 'strict-origin-when-cross-origin',
    'Permissions-Policy'      => 'geolocation=()',
];
foreach ($headers as $headerName => $headerValue) {
    echo "$headerName: $headerValue\n";
}

// ── foreach with references ───────────────────────────────────────────────────
echo "\n=== foreach by reference ===\n";
$scores = [3, 7, 2, 9, 1, 5];
foreach ($scores as &$score) {
    $score = $score * 10;   // scale by 10
}
unset($score);  // important: unset the reference after the loop
print_r($scores);

// ── break and continue ────────────────────────────────────────────────────────
echo "\n=== break and continue ===\n";
for ($n = 0; $n < 20; $n++) {
    if ($n % 2 === 0) {
        continue;   // skip even numbers
    }
    if ($n > 11) {
        break;      // stop when above 11
    }
    echo "$n ";
}
echo "\n";

// ── Nested loops with break level ─────────────────────────────────────────────
echo "\n=== Nested loops (break 2) ===\n";
$found = false;
$needle = 7;
$matrix = [[1,2,3],[4,5,6],[7,8,9]];
foreach ($matrix as $row => $cols) {
    foreach ($cols as $col => $val) {
        if ($val === $needle) {
            echo "Found $needle at row=$row, col=$col\n";
            $found = true;
            break 2;  // break out of BOTH loops
        }
    }
}

// ── Generator function ────────────────────────────────────────────────────────
echo "\n=== Generator — memory-efficient range ===\n";
function xrange(int $start, int $end, int $step = 1): Generator
{
    for ($i = $start; $i <= $end; $i += $step) {
        yield $i;
    }
}
$result = [];
foreach (xrange(1, 100, 10) as $val) {
    $result[] = $val;
}
echo implode(', ', $result) . "\n";
