<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit("Access denied");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['action'])) {
    $status = $_POST['action'] === 'suspend' ? 'suspended' : 'active';
    $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->execute([$status, (int)$_POST['user_id']]);
}

$stmt = $pdo->query("SELECT id, username, role, status FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Panel</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<div class="max-w-7xl mx-auto mt-10 bg-white p-6 rounded-xl shadow-lg">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Admin Panel</h1>

        <div class="flex gap-3">
            <a href="dashboard.php"
               class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Exit Admin
            </a>
            <a href="logout.php"
               class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                Logout
            </a>
        </div>
    </div>

    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-gray-200 text-left">
                <th class="p-3">Username</th>
                <th class="p-3 text-center">Role</th>
                <th class="p-3 text-center">Status</th>
                <th class="p-3 text-center">Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr class="border-b hover:bg-gray-50">
                <td class="p-3"><?= htmlspecialchars($u['username']) ?></td>
                <td class="p-3 text-center"><?= $u['role'] ?></td>
                <td class="p-3 text-center">
                    <span class="<?= $u['status'] === 'active' ? 'text-green-600' : 'text-red-600' ?>">
                        <?= ucfirst($u['status']) ?>
                    </span>
                </td>
                <td class="p-3 text-center">
                    <?php if ($u['username'] !== $_SESSION['user']): ?>
                        <form method="POST" class="inline">
                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                            <?php if ($u['status'] === 'active'): ?>
                                <button name="action" value="suspend"
                                    class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                    Suspend
                                </button>
                            <?php else: ?>
                                <button name="action" value="activate"
                                    class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">
                                    Activate
                                </button>
                            <?php endif; ?>
                        </form>
                    <?php else: ?>
                        <span class="text-gray-400 italic">You</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</div>

</body>
</html>
