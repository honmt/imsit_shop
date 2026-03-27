<?php
require_once __DIR__ . '/includes/functions.php';
if (isLoggedIn()) redirect('account.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (login($email, $password)) {
        redirect(isAdmin() ? 'admin/index.php' : 'account.php');
    } else {
        $error = 'Неверный email или пароль';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Вход — ИМСИТ Shop</title>
<link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;600;700;900&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="public/css/style.css">
</head>
<body>
<div class="form-page" style="background:var(--ink)">
  <div class="form-card">
    <a href="index.php" style="display:flex;align-items:center;gap:8px;margin-bottom:32px">
      <span style="font-size:18px;color:var(--accent)">✦</span>
      <span style="font-family:'Playfair Display',serif;font-weight:900;font-size:18px">ИМСИТ Shop</span>
    </a>
    <h1>Добро пожаловать</h1>
    <p class="subtitle">Войдите в свой аккаунт</p>

    <?php if($error): ?>
    <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" placeholder="your@email.com" required value="<?= e($_POST['email'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Пароль</label>
        <input type="password" name="password" placeholder="••••••••" required>
      </div>
      <button type="submit" class="btn-primary form-submit">Войти</button>
    </form>

    <p class="form-footer">Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
    <p class="form-footer" style="margin-top:8px"><a href="index.php">← На главную</a></p>
  </div>
</div>
</body>
</html>
