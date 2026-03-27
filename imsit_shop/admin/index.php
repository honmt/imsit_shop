<?php
require_once __DIR__ . '/../includes/functions.php';
if (!isAdmin()) redirect('../login.php');

$db = getDB();

// Stats
$totalOrders   = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalRevenue  = $db->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status != 'cancelled'")->fetchColumn();
$totalUsers    = $db->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn();
$totalProducts = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();

$recentOrders = $db->query("SELECT o.*, COALESCE(u.name,'Гость') as user_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 10")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Админ-панель — ИМСИТ Shop</title>
<link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;600;700;900&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../public/css/style.css">
</head>
<body>
<div class="admin-layout">

<!-- Sidebar -->
<aside class="admin-sidebar">
  <div class="admin-logo">
    <a href="../index.php" style="display:flex;align-items:center;gap:10px;color:var(--white)">
      <span style="font-size:18px;color:var(--accent)">✦</span>
      <div><div style="font-family:'Playfair Display',serif;font-weight:900;font-size:17px">ИМСИТ Shop</div><div style="font-size:11px;opacity:.4;letter-spacing:.1em">ADMIN PANEL</div></div>
    </a>
  </div>
  <nav class="admin-nav">
    <a href="index.php" class="admin-nav-link active">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
      Дашборд
    </a>
    <a href="orders.php" class="admin-nav-link">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
      Заказы
    </a>
    <a href="products.php" class="admin-nav-link">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
      Товары
    </a>
    <a href="users.php" class="admin-nav-link">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      Пользователи
    </a>
    <a href="../account.php" class="admin-nav-link" style="margin-top:20px;border-top:1px solid rgba(255,255,255,.08);padding-top:20px">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
  Личный кабинет
</a>
  </nav>
</aside>

<!-- Main -->
<main class="admin-content">
  <div class="admin-header">
    <h1>Дашборд</h1>
    <p>НАН ЧОУ ВО Академия ИМСИТ — Управление магазином</p>
  </div>

  <div class="stats-grid">
    <div class="stat-card">
      <div class="label">Заказов всего</div>
      <div class="value"><?= $totalOrders ?></div>
    </div>
    <div class="stat-card">
      <div class="label">Выручка</div>
      <div class="value" style="font-size:24px"><?= formatPrice((float)$totalRevenue) ?></div>
    </div>
    <div class="stat-card">
      <div class="label">Покупателей</div>
      <div class="value"><?= $totalUsers ?></div>
    </div>
    <div class="stat-card">
      <div class="label">Товаров</div>
      <div class="value"><?= $totalProducts ?></div>
    </div>
  </div>

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
    <h2 style="font-family:'Playfair Display',serif;font-size:22px;font-weight:700">Последние заказы</h2>
    <a href="orders.php" class="btn-primary" style="font-size:13px;padding:9px 18px">Все заказы</a>
  </div>

  <table class="data-table">
    <thead>
      <tr><th>№ Заказа</th><th>Покупатель</th><th>Email</th><th>Сумма</th><th>Способ оплаты</th><th>Статус</th><th>Дата</th><th>Действие</th></tr>
    </thead>
    <tbody>
      <?php if(empty($recentOrders)): ?>
      <tr><td colspan="8" style="text-align:center;color:var(--ink-muted);padding:32px">Заказов пока нет</td></tr>
      <?php else: ?>
      <?php foreach($recentOrders as $o): ?>
      <tr>
        <td><strong>#<?= str_pad($o['id'],5,'0',STR_PAD_LEFT) ?></strong></td>
        <td><?= e($o['full_name']) ?></td>
        <td style="color:var(--ink-muted)"><?= e($o['email']) ?></td>
        <td style="font-family:'Playfair Display',serif;font-weight:700"><?= formatPrice($o['total']) ?></td>
        <td><?= $o['payment_method']==='card'?'💳 Карта':'💵 Наличные' ?></td>
        <td>
          <?php
          $labels = ['pending'=>'Ожидает','processing'=>'Обработка','shipped'=>'Доставляется','delivered'=>'Доставлен','cancelled'=>'Отменён'];
          ?>
          <span class="badge badge-<?= $o['status'] ?>"><?= $labels[$o['status']] ?></span>
        </td>
        <td style="color:var(--ink-muted)"><?= date('d.m.Y H:i', strtotime($o['created_at'])) ?></td>
        <td><a href="order-detail.php?id=<?= $o['id'] ?>" style="font-weight:700;font-size:13px;color:var(--accent)">Детали →</a></td>
      </tr>
      <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</main>
</div>

<div class="toast" id="toast"></div>
<script src="../public/js/main.js"></script>
</body>
</html>
