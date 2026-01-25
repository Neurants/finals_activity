<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit("Access denied");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['toggle_maintenance'])) {
        $pdo->query("UPDATE site_settings SET maintenance = 1 - maintenance WHERE id = 1");
    }

    if (isset($_POST['toggle_user'])) {
        $stmt = $pdo->prepare("UPDATE users SET status = IF(status='active','suspended','active') WHERE id = ?");
        $stmt->execute([$_POST['user_id']]);
    }
}

$maintenance = $pdo->query("SELECT maintenance FROM site_settings WHERE id=1")->fetchColumn();
$users = $pdo->query("SELECT id, username, role, status FROM users")->fetchAll();
$complaints = $pdo->query("
    SELECT complaints.message, users.username, complaints.created_at
    FROM complaints
    JOIN users ON users.id = complaints.user_id
    ORDER BY complaints.created_at DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Panel</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">

<h1 class="text-3xl font-bold mb-6">Admin Panel</h1>

<div class="bg-white p-6 rounded shadow mb-8">
<form method="POST">
<button name="toggle_maintenance" class="px-4 py-2 bg-red-600 text-white rounded">
<?= $maintenance ? 'Disable Maintenance' : 'Enable Maintenance' ?>
</button>
</form>
</div>

<div class="bg-white p-6 rounded shadow mb-8">
<h2 class="text-xl font-bold mb-4">Users</h2>
<table class="w-full border">
<tr class="bg-gray-200">
<th class="p-2">Username</th>
<th class="p-2">Role</th>
<th class="p-2">Status</th>
<th class="p-2">Action</th>
</tr>

<?php foreach ($users as $u): ?>
<tr class="border-t">
<td class="p-2"><?= htmlspecialchars($u['username']) ?></td>
<td class="p-2"><?= $u['role'] ?></td>
<td class="p-2"><?= $u['status'] ?></td>
<td class="p-2">
<form method="POST">
<input type="hidden" name="user_id" value="<?= $u['id'] ?>">
<button name="toggle_user" class="px-3 py-1 bg-blue-600 text-white rounded">
Toggle
</button>
</form>
</td>
</tr>
<?php endforeach; ?>

</table>
</div>

<div class="bg-white p-6 rounded shadow">
<h2 class="text-xl font-bold mb-4">Complaints</h2>

<?php if (!$complaints): ?>
<p>No complaints</p>
<?php endif; ?>

<?php foreach ($complaints as $c): ?>
<div class="border-b py-2">
<p class="font-semibold"><?= htmlspecialchars($c['username']) ?></p>
<p><?= htmlspecialchars($c['message']) ?></p>
<p class="text-sm text-gray-500"><?= $c['created_at'] ?></p>
</div>
<?php endforeach; ?>
</div>

</body>
</html>
