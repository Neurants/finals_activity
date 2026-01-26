<?php
session_start();
require_once "db.php";

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $email === '' || $password === '') {
        $error = "All fields are required.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);

            if ($stmt->fetch()) {
                $error = "Username already exists.";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare(
                    "INSERT INTO users (username, email, password_hash, role, status)
                     VALUES (?, ?, ?, 'user', 'active')"
                );
                $stmt->execute([$username, $email, $hash]);

                $success = "Account created successfully.";
            }
        } catch (Exception $e) {
            $error = "Signup failed.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="login-box">
    <h2>Sign Up</h2>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color:green;margin-bottom:15px;">
            <?= htmlspecialchars($success) ?>
        </p>
        <a href="index.php" class="btn">Back to Login</a>
    <?php else: ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Create Account</button>
        </form>

        <a href="index.php" class="btn" style="background:#eee;color:#333;">
            Back to Login
        </a>
    <?php endif; ?>
</div>

</body>
</html>
