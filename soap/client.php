<?php
require_once "../db.php";

class UserService {
    public function getUser($id) {
        global $pdo;

        $stmt = $pdo->prepare(
            "SELECT username, email, role FROM users WHERE id = ?"
        );
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

$options = [
    'uri' => 'http://localhost/FinalsActivity/soap/server.php'
];

$server = new SoapServer(null, $options);
$server->setClass('UserService');
$server->handle();
