<?php // ESP32 posts here: {"device_key":"...","label":"Kitchen sink","type":"leak|flow","value":1}
require 'db.php';
$b = json_decode(file_get_contents('php://input'), true) ?? [];
$pdo = db();
$p = $pdo->prepare('SELECT id FROM properties WHERE device_key=?'); $p->execute([$b['device_key'] ?? '']);
$prop = $p->fetchColumn(); if (!$prop) out(['error'=>'Unknown device'],401);
$type = ($b['type'] ?? '') === 'flow' ? 'flow' : 'leak'; $label = substr($b['label'] ?? 'Sensor',0,60); $val = (float)($b['value'] ?? 0);
$s = $pdo->prepare('SELECT id FROM sensors WHERE property_id=? AND label=?'); $s->execute([$prop,$label]);
$sid = $s->fetchColumn();
if (!$sid) { $pdo->prepare('INSERT INTO sensors(property_id,label,type) VALUES(?,?,?)')->execute([$prop,$label,$type]); $sid = $pdo->lastInsertId(); }
$pdo->prepare('INSERT INTO readings(sensor_id,value) VALUES(?,?)')->execute([$sid,$val]);
if ($type === 'leak' && $val >= 1)
  $pdo->prepare('INSERT INTO alerts(property_id,sensor_id,message) VALUES(?,?,?)')->execute([$prop,$sid,"Possible water leak detected by the $label sensor. Please check the area immediately."]);
out(['ok'=>true]);
