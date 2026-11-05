<?php
/**
 * 01_variables.php — Variables and Data Types in PHP
 *
 * Concept: PHP variable declaration, type juggling, type checking,
 *          type casting, and constants.
 * Expected output: Demonstrates each data type with var_dump() output.
 */

declare(strict_types=1);

// ── Integers ──────────────────────────────────────────────────────────────────
$age = 25;
$negative = -42;
$hex = 0x1A;    // hexadecimal → 26
$octal = 0755;  // octal → 493
$binary = 0b1010; // binary → 10

echo "=== Integers ===\n";
var_dump($age, $negative, $hex, $octal, $binary);

// ── Floats ────────────────────────────────────────────────────────────────────
$price = 19.99;
$scientific = 1.5e3;   // 1500.0
$tiny = 2.4e-4;        // 0.00024

echo "\n=== Floats ===\n";
var_dump($price, $scientific, $tiny);

// ── Strings ───────────────────────────────────────────────────────────────────
$name = "Alice";
$greeting = "Hello, {$name}!";       // double-quoted with interpolation
$raw = 'No interpolation: $name';    // single-quoted — literal

echo "\n=== Strings ===\n";
var_dump($name, $greeting, $raw);

// ── Booleans ──────────────────────────────────────────────────────────────────
$isLoggedIn = true;
$isAdmin = false;

echo "\n=== Booleans ===\n";
var_dump($isLoggedIn, $isAdmin);

// ── Null ──────────────────────────────────────────────────────────────────────
$nothing = null;
echo "\n=== Null ===\n";
var_dump($nothing);

// ── Type Checking ─────────────────────────────────────────────────────────────
echo "\n=== Type Checking ===\n";
echo "gettype(\$age): " . gettype($age) . "\n";
echo "is_int(\$age): ";       var_dump(is_int($age));
echo "is_string(\$name): ";   var_dump(is_string($name));
echo "is_float(\$price): ";   var_dump(is_float($price));
echo "is_null(\$nothing): ";  var_dump(is_null($nothing));

// ── Type Casting ──────────────────────────────────────────────────────────────
$numStr = "42abc";
$casted = (int) $numStr;   // → 42 (stops at non-numeric)
$floatStr = "3.14";
$asFloat = (float) $floatStr;

echo "\n=== Type Casting ===\n";
echo "\"42abc\" cast to int: ";    var_dump($casted);
echo "\"3.14\" cast to float: ";   var_dump($asFloat);
echo "(bool) 0: ";                 var_dump((bool) 0);
echo "(bool) 1: ";                 var_dump((bool) 1);
echo "(bool) \"\": ";              var_dump((bool) "");
echo "(bool) \"hello\": ";         var_dump((bool) "hello");

// ── Constants ─────────────────────────────────────────────────────────────────
define('APP_VERSION', '1.0.0');
const DB_HOST = 'localhost';

echo "\n=== Constants ===\n";
echo "APP_VERSION: " . APP_VERSION . "\n";
echo "DB_HOST: "     . DB_HOST     . "\n";
