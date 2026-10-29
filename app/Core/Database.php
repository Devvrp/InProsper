<?php
namespace InProsper\Core;
use PDO;
use PDOException;

final class Database {
    private static ?PDO $connection = null;
    private static array $config = [];
    public static function configure(array $config): void { self::$config = $config; }
    public static function connection(): PDO {
        if (self::$connection instanceof PDO) return self::$connection;
        $c = self::$config;
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $c['host'], $c['port'] ?? '3306', $c['database'], $c['charset'] ?? 'utf8mb4');
        self::$connection = new PDO($dsn, $c['username'], $c['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return self::$connection;
    }
}
