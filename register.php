<?php require 'db.php';
$b = json_decode(file_get_contents('php://input'), true) ?? [];
$email = filter_var($b['email'] ?? '', FILTER_VALIDATE_EMAIL);
if (!$email || strlen($b['password'] ?? '') < 6) out(['error'=>'Valid email and 6+ char password required'],422);
$pdo = db();
try {
  $pdo->prepare('INSERT INTO users(name,email,pass_hash) VALUES(?,?,?)')
      ->execute([$b['name'] ?? '', $email, password_hash($b['password'], PASSWORD_DEFAULT)]);
  $uid = $pdo->lastInsertId();
  $key = bin2hex(random_bytes(16));
  $pdo->prepare('INSERT INTO properties(user_id,name,device_key) VALUES(?,?,?)')->execute([$uid, $b['property'] ?? 'My Home', $key]);
  out(['ok'=>true,'device_key'=>$key],201);
} catch (PDOException $e) { out(['error'=>'Email already registered'],409); }
