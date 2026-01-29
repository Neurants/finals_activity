<?php
session_start();
require_once "db.php";

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT id, username, email, status FROM users");
$stmt->execute();
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex">

<div class="bg-gray-800 text-white w-64 h-screen p-6">
    <div class="text-2xl font-bold text-center mb-6">Admin Panel</div>
    <ul class="space-y-4">
        <li><a href="admin.php" class="hover:bg-gray-700 p-2 rounded">Dashboard</a></li>
        <li><a href="manage_users.php" class="hover:bg-gray-700 p-2 rounded">Manage Users</a></li>
        <li><a href="settings.php" class="hover:bg-gray-700 p-2 rounded">Settings</a></li>
        <li><a href="logout.php" class="hover:bg-gray-700 p-2 rounded">Logout</a></li>
    </ul>
</div>

<div class="flex-1 p-6">
    <header class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-semibold">Welcome, Admin</h1>
        <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Logout</a>
    </header>

    <section>
        <h2 class="text-2xl font-semibold mb-4">User Management</h2>
        <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-gray-700">Username</th>
                    <th class="px-6 py-3 text-left text-gray-700">Email</th>
                    <th class="px-6 py-3 text-left text-gray-700">Status</th>
                    <th class="px-6 py-3 text-left text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr class="border-t">
                        <td class="px-6 py-4"><?= htmlspecialchars($user['username']) ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($user['email']) ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($user['status']) ?></td>
                        <td class="px-6 py-4">
                            <a href="edit_user.php?id=<?= $user['id'] ?>" class="text-blue-500 hover:underline">Edit</a> |
                            <a href="delete_user.php?id=<?= $user['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?')" class="text-red-500 hover:underline">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</div>

</body>
</html>
