<?php
/**
 * db.php — Database Connection Module for Cloud Deployment
 * Assignment 02 — Web Server Hosting Lab (Task 2)
 */

declare(strict_types=1);

function get_db_connection(): ?PDO {
    // Read from environment variables if set, or fall back to provider values
    $host = getenv('CLOUD_DB_HOST') ?: '';
    $dbname = getenv('CLOUD_DB_NAME') ?: '';
    $user = getenv('CLOUD_DB_USER') ?: '';
    $pass = getenv('CLOUD_DB_PASSWORD') ?: '';

    // If variables not set via environment, return null for graceful warning
    if (empty($host) || empty($dbname) || empty($user)) {
        return null;
    }

    $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        return null;
    }
}
