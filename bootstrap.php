<?php
declare(strict_types=1);
session_start();

spl_autoload_register(function (string $class): void {
    $prefix = 'InProsper\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) return;
    $relative = substr($class, strlen($prefix));
    $file = __DIR__ . '/app/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) require $file;
});
require_once __DIR__ . '/app/helpers.php';
InProsper\Core\Database::configure(require __DIR__ . '/config/database.php');
