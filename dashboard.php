<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$maintenance = $pdo->query("SELECT maintenance FROM site_settings WHERE id=1")->fetchColumn();
if ($maintenance && ($_SESSION['role'] ?? '') !== 'admin') {
    exit("Site under maintenance");
}

$stmt = $pdo->prepare("SELECT photo FROM profiles WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$profile = $stmt->fetch();

$display_name = $_SESSION['user'];
$photo = $profile['photo'] ?? 'default-avatar.png';

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'menu.php'; ?>

<div class="login-box">
    <img src="<?= $photo ?>" class="profile-pic" style="margin:0 auto 15px;">
    <h2>Welcome, <?= htmlspecialchars($display_name) ?> 🎉</h2>
    <p>You have successfully logged in.</p>

    <a href="diary.php" class="btn">Go to Diary</a>
    <a href="gallery.php" class="btn">Go to Gallery</a>

    <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="admin.php" class="btn" style="background:#e53e3e;">Admin Panel</a>
    <?php endif; ?>
</div>

</body>
</html>
