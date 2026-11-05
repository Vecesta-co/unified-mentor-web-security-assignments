<?php
/**
 * 07_includes.php — File Includes and Project Structure in PHP
 *
 * Concept: include, require, include_once, require_once; path handling;
 *          configuration files; security risks of dynamic includes (LFI).
 * Expected output: Demonstrates each inclusion type and prints config values.
 */

declare(strict_types=1);

echo "=== PHP File Includes Demonstration ===\n\n";

// ── Demonstrating inclusion concepts ──────────────────────────────────────────
// (We write inline since this is a standalone demo script)

// 1. Configuration array (would normally be in config.php)
$config = [
    'db_host'  => 'localhost',
    'db_name'  => 'webapp',
    'app_name' => 'Web Security Lab',
    'debug'    => false,
    'version'  => '1.0.0',
];

echo "1. Configuration Values:\n";
foreach ($config as $key => $value) {
    $display = is_bool($value) ? ($value ? 'true' : 'false') : $value;
    echo "   $key = $display\n";
}

// 2. Demonstrate include path functions
echo "\n2. PHP Include Path Information:\n";
echo "   __FILE__: " . __FILE__ . "\n";
echo "   __DIR__:  " . __DIR__ . "\n";
echo "   dirname(__FILE__): " . dirname(__FILE__) . "\n";

// 3. Demonstrate realpath for safe path resolution
$baseDir = __DIR__;
$safeFile = realpath($baseDir . '/06_functions.php');
if ($safeFile !== false && str_starts_with($safeFile, $baseDir)) {
    echo "\n3. Safe path resolved: $safeFile\n";
} else {
    echo "\n3. Path traversal attempt detected or file not found.\n";
}

// 4. Demonstrate the LFI risk of dynamic includes
echo "\n4. SECURITY WARNING — Dynamic Include Risk (LFI):\n";
echo "   VULNERABLE pattern:\n";
echo "   \$page = \$_GET['page'];\n";
echo "   include(\$page . '.php');  // DANGEROUS!\n";
echo "\n";
echo "   ATTACK: ?page=../../../etc/passwd%00  → reads /etc/passwd\n";
echo "   ATTACK: ?page=php://filter/convert.base64-encode/resource=config\n";
echo "\n";
echo "   SECURE pattern — use an allowlist:\n";
echo "   \$allowed = ['home', 'about', 'contact'];\n";
echo "   if (in_array(\$page, \$allowed, true)) {\n";
echo "       include(__DIR__ . '/pages/' . \$page . '.php');\n";
echo "   }\n";

// 5. Autoloading concept
echo "\n5. Autoloading (PSR-4 concept):\n";
echo "   spl_autoload_register(function(\$className) {\n";
echo "       \$file = __DIR__ . '/' . str_replace('\\\\', '/', \$className) . '.php';\n";
echo "       if (file_exists(\$file)) { require \$file; }\n";
echo "   });\n";

// 6. include vs require differences
echo "\n6. include vs require:\n";
echo "   include  — emits E_WARNING if file missing; script continues\n";
echo "   require  — emits E_ERROR if file missing; script halts\n";
echo "   include_once / require_once — prevents double-inclusion\n";
echo "   Security note: Use require_once for security-critical files (config, auth).\n";

echo "\nScript complete.\n";
