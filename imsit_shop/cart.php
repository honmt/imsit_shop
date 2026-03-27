<?php
require_once __DIR__ . '/includes/functions.php';
$items     = getCart();
$total     = getCartTotal();
$cartCount = getCartCount();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Корзина — ИМСИТ Shop</title>
<link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;600;700;900&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="public/css/style.css">
<style>
.guest-notice{background:#fff8e1;border:1.5px solid #f9a825;border-radius:var(--radius);padding:20px 24px;margin-bottom:20px;display:flex;align-items:flex-start;gap:14px}
.guest-notice-icon{font-size:28px;flex-shrink:0;margin-top:2px}
.guest-notice-text h4{font-size:16px;font-weight:700;margin-bottom:6px;color:var(--ink)}
.guest-notice-text p{font-size:14px;color:var(--ink-muted);margin:0 0 12px}
.guest-notice-btns{display:flex;gap:10px;flex-wrap:wrap}
</style>
</head>
<body>

<header class="header">
  <div class="header-inner container">
    <a href="index.php" class="logo">
      <span class="logo-icon">✦</span>
      <div class="logo-text"><span class="logo-main">ИМСИТ</span><span class="logo-sub">Shop</span></div>
    </a>
    <nav class="nav">
      <a href="index.php" class="nav-link">Главная</a>
      <a href="catalog.php" class="nav-link">Каталог</a>
      <a href="about.php" class="nav-link">О магазине</a>
    </nav>
    <div class="header-actions">
      <a href="cart.php" class="btn-icon cart-btn">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        <?php if($cartCount > 0): ?><span class="cart-badge"><?= $cartCount ?></span><?php endif; ?>
      </a>
      <?php if(isLoggedIn()): ?>
        <a href="account.php" class="btn-outline">Кабинет</a>
        <a href="logout.php" class="btn-ghost">Выйти</a>
      <?php else: ?>
        <a href="login.php" class="btn-outline">Войти</a>
        <a href="register.php" class="btn-primary">Регистрация</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<div class="page-header">
  <div class="container">
    <h1>Корзина</h1>
    <p id="cart-count"><?= $cartCount ?> товар(ов) на <?= formatPrice($total) ?></p>
  </div>
</div>

<div class="container">
  <?php if(empty($items)): ?>
  <div style="text-align:center;padding:80px 0">
    <div style="font-size:80px;margin-bottom:20px">🛒</div>
    <h2 style="font-family:'Playfair Display',serif;font-size:32px;margin-bottom:12px">Корзина пуста</h2>
    <p style="color:var(--ink-muted);margin-bottom:28px">Добавьте товары из каталога</p>
    <a href="catalog.php" class="btn-primary btn-lg">Перейти в каталог</a>
  </div>

  <?php else: ?>

  <div class="cart-layout">
    <div class="cart-items">
      <?php foreach($items as $item): ?>
      <div class="cart-item" id="cart-item-<?= $item['id'] ?>">
        <div class="cart-item-img">
          <?php if($item['image']): ?>
            <img src="public/images/<?= e($item['image']) ?>" alt="<?= e($item['name']) ?>">
          <?php else: ?>
            📦
          <?php endif; ?>
        </div>
        <div>
          <div class="cart-item-name"><?= e($item['name']) ?></div>
          <div class="cart-item-price"><?= formatPrice($item['price']) ?></div>
          <div class="qty-control">
            <button class="qty-btn" onclick="updateQty(<?= $item['id'] ?>, -1)">−</button>
            <span class="qty-num" id="qty-<?= $item['id'] ?>"><?= $item['quantity'] ?></span>
            <button class="qty-btn" onclick="updateQty(<?= $item['id'] ?>, 1)">+</button>
            <span style="color:var(--ink-muted);font-size:13px;margin-left:8px">
              = <strong><?= formatPrice($item['price'] * $item['quantity']) ?></strong>
            </span>
          </div>
        </div>
        <button class="btn-remove" onclick="removeCartItem(<?= $item['id'] ?>)" title="Удалить">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="cart-summary">
      <div class="summary-card">
        <h3>Итого</h3>
        <div class="summary-line"><span>Товаров</span><span><?= $cartCount ?> шт.</span></div>
        <div class="summary-line"><span>Доставка</span><span>Уточняется</span></div>
        <div class="summary-total">
          <span>К оплате</span>
          <span id="cart-total"><?= formatPrice($total) ?></span>
        </div>

        <?php if(isLoggedIn()): ?>
          <a href="checkout.php" class="btn-primary btn-checkout">Оформить заказ →</a>
        <?php else: ?>
          <!-- Уведомление для незарегистрированных -->
          <div class="guest-notice" style="margin-bottom:16px">
            <div class="guest-notice-icon">🔒</div>
            <div class="guest-notice-text">
              <h4>Нужен аккаунт для оформления</h4>
              <p>Доставка доступна только зарегистрированным пользователям. Войдите или создайте аккаунт — это займёт меньше минуты.</p>
              <div class="guest-notice-btns">
                <a href="login.php?redirect=checkout.php" class="btn-primary" style="font-size:14px;padding:10px 20px">Войти</a>
                <a href="register.php" class="btn-outline" style="font-size:14px;padding:10px 20px">Регистрация</a>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <a href="catalog.php" style="display:block;text-align:center;margin-top:12px;font-size:14px;color:var(--ink-muted);font-weight:600">← Продолжить покупки</a>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>

<div class="toast" id="toast"></div>
<script src="public/js/main.js"></script>
</body>
</html>
