<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['entry'])) {
    $title = trim($_POST['title']) ?: 'Untitled';
    $content = trim($_POST['entry']);

    if ($content) {
        $stmt = $pdo->prepare("INSERT INTO diary_entries (user_id, title, content) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $title, $content]);
        $success = "Diary entry saved!";
    } else {
        $error = "Entry content cannot be empty.";
    }
}

$stmt = $pdo->prepare("SELECT * FROM diary_entries WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$entries = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Diary</title>
    <link rel="stylesheet" href="style.css?ver=1.2">
</head>
<body class="diary-page">

<?php include 'menu.php'; ?>

<div class="diary-container">
    <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
    <h2>📔 My Diary</h2>

    <?php if ($success) echo "<p style='color:green;text-align:center;'>$success</p>"; ?>
    <?php if ($error) echo "<p style='color:red;text-align:center;'>$error</p>"; ?>

    <form method="POST">
        <input type="text" name="title" placeholder="Title of Entry">
        <textarea name="entry" placeholder="Write your diary entry..." required></textarea>
        <button type="submit">Save Entry</button>
    </form>

    <div class="entries">
        <?php if (empty($entries)): ?>
            <p>No diary entries yet.</p>
        <?php else: ?>
            <?php foreach ($entries as $note): ?>
                <details class="note">
                    <summary><?= htmlspecialchars($note['title']) ?></summary>
                    <p><?= nl2br(htmlspecialchars($note['content'])) ?></p>
                </details>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
