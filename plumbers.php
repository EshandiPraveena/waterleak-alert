<?php require 'db.php'; out(db()->query('SELECT id,name,area,skill,phone,rating FROM plumbers ORDER BY rating DESC')->fetchAll());
