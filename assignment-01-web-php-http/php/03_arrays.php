<?php
/**
 * 03_arrays.php — Arrays in PHP
 *
 * Concept: Indexed arrays, associative arrays, multi-dimensional arrays,
 *          array functions, and sorting.
 * Expected output: Array contents and operation results.
 */

declare(strict_types=1);

// ── Indexed Array ─────────────────────────────────────────────────────────────
echo "=== Indexed Array ===\n";
$fruits = ['apple', 'banana', 'cherry', 'date'];
print_r($fruits);
echo "Count: " . count($fruits) . "\n";
echo "First: " . $fruits[0] . "\n";
echo "Last:  " . $fruits[count($fruits) - 1] . "\n";

// Push and pop
$fruits[] = 'elderberry';
array_push($fruits, 'fig');
echo "After push: "; print_r($fruits);
$popped = array_pop($fruits);
echo "Popped: $popped\n";

// Shift and unshift
$shifted = array_shift($fruits);
echo "Shifted: $shifted\n";
array_unshift($fruits, 'avocado');
echo "After unshift: "; print_r($fruits);

// ── Associative Array ─────────────────────────────────────────────────────────
echo "\n=== Associative Array ===\n";
$user = [
    'username'   => 'alice',
    'email'      => 'alice@example.com',
    'role'       => 'admin',
    'login_count' => 42,
];
print_r($user);
echo "Username: " . $user['username'] . "\n";
echo "Keys: ";   print_r(array_keys($user));
echo "Values: "; print_r(array_values($user));

// isset and unset
var_dump(isset($user['email']));
var_dump(isset($user['password']));
unset($user['login_count']);
echo "After unset login_count: "; print_r($user);

// ── Multi-dimensional Array ───────────────────────────────────────────────────
echo "\n=== Multi-dimensional Array ===\n";
$vulnerabilities = [
    ['name' => 'SQLi',   'severity' => 'Critical', 'cvss' => 9.8],
    ['name' => 'XSS',    'severity' => 'High',     'cvss' => 7.4],
    ['name' => 'CSRF',   'severity' => 'Medium',   'cvss' => 5.4],
    ['name' => 'LFI',    'severity' => 'High',     'cvss' => 7.5],
];
foreach ($vulnerabilities as $v) {
    echo "{$v['name']} — {$v['severity']} (CVSS: {$v['cvss']})\n";
}

// ── Array Functions ───────────────────────────────────────────────────────────
echo "\n=== Array Functions ===\n";
$nums = [5, 3, 8, 1, 9, 2, 7, 4, 6];
echo "Original: "; print_r($nums);
sort($nums);
echo "sort(): "; print_r($nums);
rsort($nums);
echo "rsort(): "; print_r($nums);

$assoc = ['b' => 2, 'a' => 1, 'c' => 3];
asort($assoc);
echo "asort(): "; print_r($assoc);
ksort($assoc);
echo "ksort(): "; print_r($assoc);

echo "array_sum: "     . array_sum($nums) . "\n";
echo "array_product: " . array_product([1,2,3,4,5]) . "\n";
echo "array_unique: "; print_r(array_unique([1,2,2,3,3,3,4]));
echo "array_flip: ";   print_r(array_flip(['a','b','c']));
echo "array_reverse: "; print_r(array_reverse([1,2,3,4,5]));
echo "array_slice(1,3): "; print_r(array_slice($nums, 1, 3));
echo "in_array(9): "; var_dump(in_array(9, $nums));
echo "array_search(7): " . array_search(7, $nums) . "\n";

$evens = array_filter($nums, fn($n) => $n % 2 === 0);
echo "array_filter (even): "; print_r($evens);
$doubled = array_map(fn($n) => $n * 2, $nums);
echo "array_map (double): "; print_r($doubled);
$sum = array_reduce($nums, fn($carry, $n) => $carry + $n, 0);
echo "array_reduce (sum): $sum\n";
