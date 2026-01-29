<?php
session_start();
require_once "db.php";

$success = '';
$error = '';
$photos = [];

try {
    $pdo->query("SELECT 1");
} catch (Exception $e) {
    http_response_code(503);
    exit("Service temporarily unavailable.");
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = (int)$_POST['delete_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM gallery_images WHERE id = ? AND user_id = ?");
        $stmt->execute([$delete_id, $_SESSION['user_id']]);
        $success = "Photo deleted successfully.";
    } catch (Exception $e) {
        $error = "Unable to delete photo right now.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
    $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png'];

    if (!in_array($ext, $allowed_ext)) {
        $error = "Only JPG, JPEG, and PNG files are allowed.";
    } else {
        try {
            $data = base64_encode(file_get_contents($_FILES['photo']['tmp_name']));
            $image = "data:image/$ext;base64,$data";

            $stmt = $pdo->prepare("INSERT INTO gallery_images (user_id, filename) VALUES (?, ?)");
            $stmt->execute([$_SESSION['user_id'], $image]);

            $success = "Photo uploaded successfully.";
        } catch (Exception $e) {
            $error = "Upload failed. Please try again later.";
        }
    }
}

try {
    $stmt = $pdo->prepare("SELECT id, filename FROM gallery_images WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $photos = $stmt->fetchAll();
} catch (Exception $e) {
    $error = "Gallery unavailable right now.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gallery</title>
    <link rel="stylesheet" href="style.css?ver=2.0">
</head>
<body class="gallery-page">

<?php include "menu.php"; ?>

<div class="diary-container">
    <h2 class="page-title">My Gallery</h2>

    <?php if ($success): ?>
        <p class="success-msg"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="error-msg"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="upload-box">
        <input type="file" name="photo" required accept="image/jpeg,image/jpg,image/png">
        <button type="submit" class="btn">Upload Photo</button>
    </form>

    <?php if (!$photos): ?>
        <p class="empty-msg">No photos uploaded yet.</p>
    <?php else: ?>
        <div class="gallery-grid">
            <?php foreach ($photos as $photo): ?>
                <div class="gallery-item">
                    <img src="<?= $photo['filename'] ?>" alt="Photo">

                    <form method="POST">
                        <input type="hidden" name="delete_id" value="<?= $photo['id'] ?>">
                        <button class="delete-btn" onclick="return confirm('Delete this photo?')">
                            Delete
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

</body>
</html>
