<?php
/**
 * 08_forms.php — Form Handling and Input Validation
 *
 * Concept: Handling GET and POST requests, sanitization, validation,
 *          and CSRF token verification.
 * Expected output: HTML form with interactive processing, validation
 *                  errors on invalid inputs, and success response on valid submissions.
 */

declare(strict_types=1);

session_start();

// Initialize CSRF token if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrf_token = $_SESSION['csrf_token'];
$errors = [];
$success_msg = "";
$submitted_data = [];

function sanitize_output(string $data): string {
    return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        $errors[] = "CSRF verification failed: Token mismatch or expired.";
    } else {
        $username = trim((string)($_POST['username'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $category = trim((string)($_POST['category'] ?? ''));
        $message = trim((string)($_POST['message'] ?? ''));

        if ($username === '' || strlen($username) < 3 || strlen($username) > 30) {
            $errors[] = "Username must be between 3 and 30 characters.";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "A valid email address is required.";
        }
        $allowed_categories = ['general', 'bug', 'feature'];
        if (!in_array($category, $allowed_categories, true)) {
            $errors[] = "Invalid category selected.";
        }
        if (strlen($message) < 5 || strlen($message) > 1000) {
            $errors[] = "Message must be between 5 and 1000 characters.";
        }

        if (empty($errors)) {
            $success_msg = "Form submitted successfully!";
            $submitted_data = [
                'username' => $username,
                'email' => $email,
                'category' => $category,
                'message' => $message,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ];
            // Rotate token after valid submission
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $csrf_token = $_SESSION['csrf_token'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>08 - Secure Form Handling</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 2rem; background: #f9fafb; color: #111827; }
        .card { background: #fff; padding: 1.5rem; border-radius: 8px; max-width: 600px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .alert-error { background: #fee2e2; color: #991b1b; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; }
        .alert-success { background: #dcfce7; color: #166534; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; }
        label { display: block; margin-top: 0.75rem; font-weight: 600; font-size: 0.875rem; }
        input, select, textarea { width: 100%; padding: 0.5rem; margin-top: 0.25rem; border: 1px solid #d1d5db; border-radius: 4px; box-sizing: border-box; }
        button { margin-top: 1rem; background: #2563eb; color: #fff; padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #1d4ed8; }
        pre { background: #f3f4f6; padding: 0.75rem; border-radius: 4px; overflow-x: auto; font-size: 0.875rem; }
    </style>
</head>
<body>
<div class="card">
    <h2>Secure Form Processing (POST + CSRF Protection)</h2>
    <p>Demonstrates proper server-side sanitization, strict validation, and CSRF token verification.</p>

    <?php if (!empty($errors)): ?>
        <div class="alert-error">
            <strong>Errors:</strong>
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= sanitize_output($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success_msg): ?>
        <div class="alert-success"><?= sanitize_output($success_msg) ?></div>
        <h4>Received Data:</h4>
        <pre><?= sanitize_output(json_encode($submitted_data, JSON_PRETTY_PRINT)) ?></pre>
    <?php endif; ?>

    <form method="POST" action="08_forms.php">
        <input type="hidden" name="csrf_token" value="<?= sanitize_output($csrf_token) ?>">

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required minlength="3" maxlength="30" value="<?= sanitize_output($_POST['username'] ?? '') ?>">

        <label for="email">Email Address:</label>
        <input type="email" id="email" name="email" required value="<?= sanitize_output($_POST['email'] ?? '') ?>">

        <label for="category">Inquiry Category:</label>
        <select id="category" name="category">
            <option value="general" <?= (($_POST['category'] ?? '') === 'general') ? 'selected' : '' ?>>General Inquiry</option>
            <option value="bug" <?= (($_POST['category'] ?? '') === 'bug') ? 'selected' : '' ?>>Bug Report</option>
            <option value="feature" <?= (($_POST['category'] ?? '') === 'feature') ? 'selected' : '' ?>>Feature Request</option>
        </select>

        <label for="message">Message:</label>
        <textarea id="message" name="message" rows="4" required minlength="5" maxlength="1000"><?= sanitize_output($_POST['message'] ?? '') ?></textarea>

        <button type="submit">Submit Form</button>
    </form>
</div>
</body>
</html>

