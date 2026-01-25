<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit("Access denied");
}

if (isset($_POST['action'], $_POST['user_id'])) {
    $status = $_POST['action'] === 'suspend' ? 'suspended' : 'active';
    $stmt = $pdo->prepare("UPDATE users SET status=? WHERE id=?");
    $stmt->execute([$status, $_POST['user_id']]);
}

$users = $pdo->query("SELECT id, username, role, status FROM users")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin - Users</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="max-w-6xl mx-auto mt-10 bg-white p-6 rounded-xl shadow-lg">
    <h1 class="text-2xl font-bold mb-6">User Management</h1>

    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-3 text-left">Username</th>
                <th class="p-3">Role</th>
                <th class="p-3">Status</th>
                <th class="p-3">Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr class="border-b">
                <td class="p-3"><?= htmlspecialchars($u['username']) ?></td>
                <td class="p-3 text-center"><?= $u['role'] ?></td>
                <td class="p-3 text-center"><?= $u['status'] ?></td>
                <td class="p-3 text-center">
                    <?php if ($u['status'] === 'active'): ?>
                        <form method="POST" class="inline">
                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                            <button name="action" value="suspend"
                                class="bg-red-500 text-white px-3 py-1 rounded">
                                Suspend
                            </button>
                        </form>
                    <?php else: ?>
                        <form method="POST" class="inline">
                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                            <button name="action" value="activate"
                                class="bg-green-500 text-white px-3 py-1 rounded">
                                Activate
                            </button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
