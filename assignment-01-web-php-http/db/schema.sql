-- schema.sql — Web Security Lab Portfolio database schema
-- Creates tables for the custom PHP application (not DVWA)
-- Run via: mysql -u webapp -p webapp < schema.sql
-- Or: mounted as /docker-entrypoint-initdb.d/01-schema.sql in Docker

-- ── Create application database ───────────────────────────────────────────────
CREATE DATABASE IF NOT EXISTS webapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE webapp;

-- ── Users table ───────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(30)  NOT NULL UNIQUE,
    email       VARCHAR(254) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,  -- bcrypt hash via password_hash()
    first_name  VARCHAR(50)  NOT NULL DEFAULT '',
    last_name   VARCHAR(50)  NOT NULL DEFAULT '',
    role        ENUM('user','admin') NOT NULL DEFAULT 'user',
    is_active   TINYINT(1)   NOT NULL DEFAULT 1,
    login_attempts INT UNSIGNED NOT NULL DEFAULT 0,
    locked_until   DATETIME     DEFAULT NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB;

-- ── Sessions table (server-side session store) ────────────────────────────────
CREATE TABLE IF NOT EXISTS sessions (
    session_id  VARCHAR(128) NOT NULL PRIMARY KEY,
    user_id     INT UNSIGNED NOT NULL,
    ip_address  VARCHAR(45)  NOT NULL,
    user_agent  VARCHAR(500) NOT NULL DEFAULT '',
    csrf_token  VARCHAR(64)  NOT NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expires_at  DATETIME     NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB;

-- ── Seed demo user ────────────────────────────────────────────────────────────
-- Password: Demo@Password123!
-- Hash generated with: password_hash('Demo@Password123!', PASSWORD_BCRYPT)
-- CHANGE THIS PASSWORD in any real deployment.
INSERT INTO users (username, email, password, first_name, last_name, role)
VALUES (
    'demo_user',
    'demo@lab.localhost',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Demo',
    'User',
    'user'
) ON DUPLICATE KEY UPDATE username = username;

-- Admin seed (password: Admin@Secret456!)
INSERT INTO users (username, email, password, first_name, last_name, role)
VALUES (
    'admin',
    'admin@lab.localhost',
    '$2y$10$TKh8H1.PfNqajCRGTblZ.eHPmAbTbqc1gHQlpw5UFBM9yXdIE4X2.',
    'Admin',
    'User',
    'admin'
) ON DUPLICATE KEY UPDATE username = username;

-- ── Comments table (for XSS stored demo) ─────────────────────────────────────
CREATE TABLE IF NOT EXISTS comments (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED DEFAULT NULL,
    name        VARCHAR(100) NOT NULL,
    body        TEXT         NOT NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_created (created_at)
) ENGINE=InnoDB;
