<?php
if (!isset($_SESSION['user_id'])) {
    return;
}

$stmt = $pdo->prepare("SELECT photo FROM profiles WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$profile = $stmt->fetch();

$photo = $profile['photo'] ?? 'default-avatar.png';
?>

<div class="menu-bar">
    <div class="menu-left">
        <div class="site-name">
            <a href="dashboard.php">LifeCanvas</a>
        </div>
    </div>

    <div class="menu-right">
        <img src="<?= $photo ?>" class="menu-profile-pic" alt="Profile Picture">

        <div class="dropdown">
            <button class="dropbtn">▼</button>
            <div class="dropdown-content">
                <a href="profile.php">Profile</a>

                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                    <a href="admin.php">Admin Panel</a>
                <?php endif; ?>

                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</div>
