<?php
require_once "../db.php";
header("Content-Type: application/json");

try {
    $stmt = $pdo->query("SELECT id, username, role, status FROM users");
    echo json_encode([
        "status" => "success",
        "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Unable to fetch users"
    ]);
}
