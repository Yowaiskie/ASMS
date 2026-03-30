<?php
require 'app/config/config.php';
require 'app/core/Database.php';
$db = App\Core\Database::getInstance();
$db->query("DESCRIBE servers");
foreach($db->resultSet() as $r) echo $r->Field . " (" . $r->Type . ")\n";
