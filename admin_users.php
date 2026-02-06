<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit("Access denied");
}

$error = "";
$success = "";

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['user_id'])) {
        $status = $_POST['action'] === 'suspend' ? 'suspended' : 'active';
        $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->execute([$status, (int)$_POST['user_id']]);
        $success = "User updated successfully";
    }

    $stmt = $pdo->query("SELECT id, username, role, status FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Something went wrong. Please try again.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Panel</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="max-w-6xl mx-auto mt-10 bg-white p-6 rounded-xl shadow-lg">
    <h1 class="text-2xl font-bold mb-6">User Management</h1>

    <?php if ($success): ?>
        <p class="text-green-600 mb-4"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="text-red-600 mb-4"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-3 text-left">Username</th>
                <th class="p-3 text-center">Role</th>
                <th class="p-3 text-center">Status</th>
                <th class="p-3 text-center">Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr class="border-b">
                <td class="p-3"><?= htmlspecialchars($u['username']) ?></td>
                <td class="p-3 text-center"><?= htmlspecialchars($u['role']) ?></td>
                <td class="p-3 text-center"><?= htmlspecialchars($u['status']) ?></td>
                <td class="p-3 text-center">
                    <form method="POST">
                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                        <button
                            name="action"
                            value="<?= $u['status'] === 'active' ? 'suspend' : 'activate' ?>"
                            class="<?= $u['status'] === 'active' ? 'bg-red-500' : 'bg-green-500' ?> text-white px-3 py-1 rounded"
                        >
                            <?= $u['status'] === 'active' ? 'Suspend' : 'Activate' ?>
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
