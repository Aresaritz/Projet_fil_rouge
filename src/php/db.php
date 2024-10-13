<?php
$database_config = include __DIR__ . '/../config/database.php';

$host = $database_config['host'];
$dbname = $database_config['dbname'];
$user = $database_config['user'];
$password = $database_config['password'];
$passkey = $database_config['passkey'];

$db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);