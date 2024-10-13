<?php
require_once __DIR__ . '/../src/php/functions.php';
require_once __DIR__ . '/../src/php/db.php';

session_start();
if (!isset($_SESSION['id'])) {
  header('Location: index.php');
  exit;
}
