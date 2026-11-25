<?php // GET ?device_key=...&range=daily|weekly|monthly  -> litres per bucket from flow sensors
require 'db.php'; $pdo = db();
$p = $pdo->prepare('SELECT id FROM properties WHERE device_key=?'); $p->execute([$_GET['device_key'] ?? '']);
$prop = $p->fetchColumn(); if (!$prop) out(['error'=>'Unauthorized'],401);
$r = ['weekly'=>['%a',7],'monthly'=>['Wk %u',30]][$_GET['range'] ?? ''] ?? ['%H:00',1];
$q = $pdo->prepare("SELECT DATE_FORMAT(r.created_at,?) AS label, ROUND(SUM(r.value),1) AS litres FROM readings r JOIN sensors s ON s.id=r.sensor_id WHERE s.property_id=? AND s.type='flow' AND r.created_at>=NOW()-INTERVAL ? DAY GROUP BY label ORDER BY MIN(r.created_at)");
$q->execute([$r[0],$prop,$r[1]]); out($q->fetchAll());
