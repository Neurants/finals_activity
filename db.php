<?php
$host = "localhost";
$db   = "finals_activity_db";
$user = "root";
$pass = "";

$maintenance_mode = false;

if ($maintenance_mode) {
    http_response_code(503);
    include "maintenance.php";
    exit();
}

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
