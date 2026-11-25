<?php
// Edit these for your XAMPP/MySQL setup.
function db(): PDO {
  static $pdo = null;
  if (!$pdo) $pdo = new PDO('mysql:host=localhost;dbname=waterleak;charset=utf8mb4','root','',
    [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
  return $pdo;
}
function out($d,$c=200){ http_response_code($c); header('Content-Type: application/json'); echo json_encode($d); exit; }
