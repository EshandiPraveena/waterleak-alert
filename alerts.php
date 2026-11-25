<?php // GET ?device_key=... returns latest alerts; POST {id} resolves one.
require 'db.php'; $pdo = db();
$k = $_GET['device_key'] ?? ''; $p = $pdo->prepare('SELECT id FROM properties WHERE device_key=?'); $p->execute([$k]);
$prop = $p->fetchColumn(); if (!$prop) out(['error'=>'Unauthorized'],401);
if ($_SERVER['REQUEST_METHOD']==='POST') { $b=json_decode(file_get_contents('php://input'),true)??[];
  $pdo->prepare('UPDATE alerts SET resolved=1 WHERE id=? AND property_id=?')->execute([(int)($b['id']??0),$prop]); out(['ok'=>true]); }
$q = $pdo->prepare('SELECT id,message,resolved,created_at FROM alerts WHERE property_id=? ORDER BY id DESC LIMIT 50'); $q->execute([$prop]); out($q->fetchAll());
