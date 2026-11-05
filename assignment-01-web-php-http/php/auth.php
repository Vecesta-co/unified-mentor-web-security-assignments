<?php
require __DIR__.'/config.php';
if($_SERVER['REQUEST_METHOD']!=='POST'||!hash_equals($_SESSION['csrf'],$_POST['csrf']??'')){http_response_code(400);exit('Invalid request');}
$stmt=$pdo->prepare('SELECT id,password_hash FROM users WHERE username=:u');$stmt->execute(['u'=>$_POST['username']??'']);$u=$stmt->fetch(PDO::FETCH_ASSOC);
if(!$u||!password_verify($_POST['password']??'',$u['password_hash'])){http_response_code(401);exit('Invalid credentials');}
session_regenerate_id(true);$_SESSION['user_id']=$u['id'];header('Location: dashboard.php');
