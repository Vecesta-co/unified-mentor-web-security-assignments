<?php
/** Concept: forms and file I/O. Expected output: a short demonstration of forms and file I/O. */
if ($_SERVER["REQUEST_METHOD"] === "POST") { echo htmlspecialchars((string)($_POST["name"] ?? ""), ENT_QUOTES, "UTF-8"); }
?>
