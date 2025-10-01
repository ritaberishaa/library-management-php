<?php
declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$path = $_ENV['SQLITE_PATH'] ?? __DIR__ . '/../database.sqlite';
$pdo = new PDO('sqlite:' . $path, null, null, [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
$pdo->exec('PRAGMA foreign_keys = ON;');
$sql = file_get_contents(__DIR__ . '/../sql/sqlite_schema.sql');
$pdo->exec($sql);
echo "Migration completed.\n";
