<?php
namespace App\Core;
use PDO;
class Database {
  private static ?PDO $pdo = null;
  public static function init(array $cfg): void {
    $dsn = 'sqlite:' . $cfg['sqlite'];
    self::$pdo = new PDO($dsn, null, null, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    self::$pdo->exec('PRAGMA foreign_keys = ON;');
  }
  public static function pdo(): PDO {
    if (!self::$pdo) { throw new \RuntimeException('DB not initialized'); }
    return self::$pdo;
  }
}
