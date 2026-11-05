<?php
// Configure via environment; demo rate limits should be added before deployment.
session_name('lab_session'); session_set_cookie_params(['httponly'=>true,'secure'=>!empty($_SERVER['HTTPS']),'samesite'=>'Strict']); session_start();
$dsn=getenv('LAB_DSN') ?: 'sqlite:' . __DIR__ . '/../db/lab.sqlite';
try { $pdo=new PDO($dsn,getenv('LAB_DB_USER') ?: null,getenv('LAB_DB_PASS') ?: null,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]); } catch (PDOException $e) { http_response_code(503); exit('Database unavailable; initialize schema first.'); }
if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32));
