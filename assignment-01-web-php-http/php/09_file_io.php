<?php
/**
 * 09_file_io.php — File System Operations and Path Traversal Mitigation
 *
 * Concept: Secure file creation, reading, appending, atomic locking,
 *          and path traversal protection using realpath verification.
 * Expected output: Text output demonstrating file write, read, directory listing,
 *                  and active prevention of path traversal attacks.
 */

declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');

echo "=== PHP File I/O & Path Security Lab ===\n\n";

$storage_dir = __DIR__ . '/../storage';
if (!is_dir($storage_dir)) {
    mkdir($storage_dir, 0755, true);
}

// 1. Safe File Writing
$sample_file = $storage_dir . '/audit_sample.log';
$log_entry = sprintf("[%s] Audit event logged from IP %s\n", date('Y-m-d H:i:s'), $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');

$bytes_written = file_put_contents($sample_file, $log_entry, FILE_APPEND | LOCK_EX);
echo "[+] Successfully appended {$bytes_written} bytes to: {$sample_file}\n";

// 2. Safe File Reading
if (file_exists($sample_file)) {
    echo "[+] Reading file content:\n---\n";
    echo file_get_contents($sample_file);
    echo "---\n";
}

// 3. Path Traversal Defense Demonstration
function safe_read_file(string $base_folder, string $user_filename): string {
    // Strip null bytes
    $clean_filename = str_replace(chr(0), '', $user_filename);
    $target_path = $base_folder . DIRECTORY_SEPARATOR . $clean_filename;
    
    $real_base = realpath($base_folder);
    $real_target = realpath($target_path);

    if ($real_base === false || $real_target === false) {
        return "[SECURITY ALERT] Target file does not exist or access denied.";
    }

    // Ensure target path strictly starts with base path
    if (strpos($real_target, $real_base) !== 0) {
        return "[BLOCKED] Path traversal attempt detected! Access forbidden outside sandbox.";
    }

    return file_get_contents($real_target) ?: "[EMPTY]";
}

echo "\n--- Path Traversal Simulation ---\n";
$normal_test = "audit_sample.log";
$traversal_test = "../../../etc/passwd";
$windows_traversal = "..\\..\\..\\Windows\\win.ini";

echo "Test 1 (Normal file): " . substr(safe_read_file($storage_dir, $normal_test), 0, 50) . "...\n";
echo "Test 2 (Unix Traversal): " . safe_read_file($storage_dir, $traversal_test) . "\n";
echo "Test 3 (Windows Traversal): " . safe_read_file($storage_dir, $windows_traversal) . "\n";

echo "\n[+] File I/O tests finished successfully.\n";

