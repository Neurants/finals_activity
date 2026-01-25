<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = "";

$stmt = $pdo->prepare("SELECT * FROM profiles WHERE user_id = ?");
$stmt->execute([$user_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profile) {
    $stmt = $pdo->prepare(
        "INSERT INTO profiles (user_id, display_name) VALUES (?, ?)"
    );
    $stmt->execute([$user_id, $_SESSION['user']]);

    $stmt = $pdo->prepare("SELECT * FROM profiles WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $photo = $profile['photo'];

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $data = base64_encode(file_get_contents($_FILES['photo']['tmp_name']));
            $mime = $ext === 'png' ? 'png' : 'jpeg';
            $photo = "data:image/$mime;base64,$data";
        }
    }

    $stmt = $pdo->prepare(
        "UPDATE profiles SET photo = ? WHERE user_id = ?"
    );
    $stmt->execute([$photo, $user_id]);

    $profile['photo'] = $photo;
    $_SESSION['profile']['photo'] = $photo;

    $success = "Profile picture updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="diary-page">

<?php include "menu.php"; ?>

<div class="diary-container">
    <h2>My Profile</h2>

    <?php if ($success): ?>
        <p style="color:green;text-align:center;">
            <?= htmlspecialchars($success) ?>
        </p>
    <?php endif; ?>

    <div style="text-align:center;margin-bottom:20px;">
        <img
            src="<?= htmlspecialchars($profile['photo'] ?: 'default-avatar.png') ?>"
            class="profile-pic"
            alt="Profile Picture"
        >
    </div>

    <p style="text-align:center;font-size:18px;margin-bottom:20px;">
        <strong>Username:</strong> <?= htmlspecialchars($_SESSION['user']) ?>
    </p>

    <form method="POST" enctype="multipart/form-data">

        <label for="photo">Change Profile Picture</label>
        <input
            type="file"
            id="photo"
            name="photo"
            accept="image/jpeg,image/png"
        >

        <button type="submit">Update Picture</button>
    </form>
</div>

</body>
</html>
