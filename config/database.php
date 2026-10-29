<?php
$local = __DIR__ . '/database.local.php';
return file_exists($local) ? require $local : require __DIR__ . '/database.example.php';
