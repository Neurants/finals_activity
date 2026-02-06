<?php
require_once "../db.php";

class UserService {
    public function getUsers() {
        global $pdo;
        try {
            $stmt = $pdo->query("SELECT id, username, role FROM users");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}

$server = new SoapServer(null, ['uri' => 'http://localhost/soap']);
$server->setClass(UserService::class);
$server->handle();
