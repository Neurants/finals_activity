<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$success = "";
$error = "";

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM diary_entries WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
    $success = "Diary entry deleted.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $title = trim($_POST['title']) ?: 'Untitled';
    $content = trim($_POST['entry']);

    if ($content) {
        $stmt = $pdo->prepare("UPDATE diary_entries SET title = ?, content = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$title, $content, $id, $_SESSION['user_id']]);
        $success = "Diary entry updated!";
    } else {
        $error = "Entry content cannot be empty.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['entry']) && !isset($_POST['update'])) {
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

                    <form method="POST" class="edit-form">
                        <input type="hidden" name="id" value="<?= $note['id'] ?>">
                        <input type="text" name="title" value="<?= htmlspecialchars($note['title']) ?>">
                        <textarea name="entry"><?= htmlspecialchars($note['content']) ?></textarea>
                        <button type="submit" name="update">Update</button>
                    </form>

                    <a href="?delete=<?= $note['id'] ?>" 
                       onclick="return confirm('Delete this entry?')" 
                       class="delete-btn">
                       🗑 Delete
                    </a>
                </details>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
