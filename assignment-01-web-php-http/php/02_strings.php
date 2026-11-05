<?php
/**
 * 02_strings.php — String Functions in PHP
 *
 * Concept: String manipulation, searching, replacing, formatting,
 *          and security-relevant escaping functions.
 * Expected output: Demonstration of each string function's result.
 */

declare(strict_types=1);

$original = "  Hello, World! This is PHP.  ";

echo "=== Basic String Info ===\n";
echo "Original: '{$original}'\n";
echo "Length (strlen): " . strlen($original) . "\n";
echo "Trimmed: '" . trim($original) . "'\n";

echo "\n=== Case Functions ===\n";
$clean = trim($original);
echo "strtoupper: " . strtoupper($clean) . "\n";
echo "strtolower: " . strtolower($clean) . "\n";
echo "ucfirst:    " . ucfirst(strtolower($clean)) . "\n";
echo "ucwords:    " . ucwords(strtolower($clean)) . "\n";

echo "\n=== Searching ===\n";
echo "strpos('World'): " . strpos($clean, 'World') . "\n";
echo "stripos('world'): " . stripos($clean, 'world') . "\n";
echo "str_contains('PHP'): "; var_dump(str_contains($clean, 'PHP'));
echo "str_starts_with('Hello'): "; var_dump(str_starts_with($clean, 'Hello'));
echo "str_ends_with('PHP.'): "; var_dump(str_ends_with($clean, 'PHP.'));

echo "\n=== Replacing ===\n";
$replaced = str_replace('PHP', 'Python', $clean);
echo "str_replace: $replaced\n";
$irepl = str_ireplace('hello', 'Hi', $clean);
echo "str_ireplace: $irepl\n";

echo "\n=== Substrings ===\n";
echo "substr(0, 5): "   . substr($clean, 0, 5) . "\n";
echo "substr(-3): "     . substr($clean, -3) . "\n";
echo "substr_count('l'): " . substr_count($clean, 'l') . "\n";

echo "\n=== Splitting & Joining ===\n";
$words = explode(' ', $clean);
echo "explode(' '): ";
print_r($words);
$joined = implode(' | ', $words);
echo "implode(' | '): $joined\n";

echo "\n=== Formatting ===\n";
$price = 1234567.891;
echo "number_format: " . number_format($price, 2, '.', ',') . "\n";
$padded = str_pad("42", 8, "0", STR_PAD_LEFT);
echo "str_pad (zero-padded): $padded\n";
$repeated = str_repeat("ab", 4);
echo "str_repeat: $repeated\n";

echo "\n=== Security-Relevant Functions ===\n";
$userInput = "<script>alert('XSS')</script> & \"quoted\" & 'apostrophe'";
echo "Raw input:          $userInput\n";
echo "htmlspecialchars:   " . htmlspecialchars($userInput, ENT_QUOTES | ENT_HTML5, 'UTF-8') . "\n";
echo "htmlentities:       " . htmlentities($userInput, ENT_QUOTES | ENT_HTML5, 'UTF-8') . "\n";
echo "strip_tags:         " . strip_tags($userInput) . "\n";
echo "addslashes:         " . addslashes($userInput) . "\n";

// md5/sha1 are insecure for passwords — shown for awareness only
$text = "hello";
echo "\nhash('sha256', 'hello'): " . hash('sha256', $text) . "\n";
echo "password_hash demo: " . password_hash("MySecret123!", PASSWORD_BCRYPT) . "\n";
