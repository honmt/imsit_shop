<?php
require_once __DIR__ . '/includes/functions.php';
$db = getDB();
$id = (int)($_GET['id'] ?? 0);
$product = $db->prepare("SELECT p.*, c.name as cat_name, c.slug as cat_slug FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ? AND p.is_active = 1");
$product->execute([$id]);
$p = $product->fetch();
if (!$p) { header('Location: catalog.php'); exit; }

$related = $db->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? AND is_active = 1 LIMIT 4");
$related->execute([$p['category_id'], $p['id']]);
$related = $related->fetchAll();
$cartCount = getCartCount();

function getCatIcon(string $slug): string {
    return match($slug) {
        'ruchki'=>'🖊','tetradi'=>'📓','papki'=>'📁','bumaga'=>'📄','markery'=>'🖌','steplery'=>'📎',default=>'📦'
    };
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($p['name']) ?> — ИМСИТ Shop</title>
<link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;600;700;900&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="public/css/style.css">
</head>
<body>

<header class="header">
  <div class="header-inner container">
    <a href="index.php" class="logo"><span class="logo-icon">✦</span><div class="logo-text"><span class="logo-main">ИМСИТ</span><span class="logo-sub">Shop</span></div></a>
     <nav class="nav">
      <a href="index.php" class="nav-link active">Главная</a>
      <a href="catalog.php" class="nav-link">Каталог</a>
      <a href="about.php" class="nav-link">О магазине</a>
    </nav>
    <div class="header-actions">
      <a href="cart.php" class="btn-icon cart-btn">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        <?php if($cartCount > 0): ?><span class="cart-badge"><?= $cartCount ?></span><?php endif; ?>
      </a>
      <?php if(isLoggedIn()): ?><a href="account.php" class="btn-outline">Кабинет</a><?php else: ?><a href="login.php" class="btn-outline">Войти</a><?php endif; ?>
    </div>
  </div>
</header>

<div class="container" style="padding:48px 24px">
  <!-- Breadcrumb -->
  <div style="display:flex;gap:8px;font-size:13px;color:var(--ink-muted);margin-bottom:32px;flex-wrap:wrap">
    <a href="index.php" style="color:var(--ink-muted)">Главная</a> /
    <a href="catalog.php" style="color:var(--ink-muted)">Каталог</a> /
    <a href="catalog.php?category=<?= e($p['cat_slug']) ?>" style="color:var(--ink-muted)"><?= e($p['cat_name']) ?></a> /
    <span style="color:var(--ink)"><?= e($p['name']) ?></span>
  </div>

  <!-- Product -->
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:48px;align-items:start;margin-bottom:64px">
    <div style="background:var(--surface);border-radius:20px;aspect-ratio:1;display:flex;align-items:center;justify-content:center;overflow:hidden;border:1.5px solid var(--border)">
      <?php if($p['image']): ?>
        <img src="public/images/<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>" style="width:100%;height:100%;object-fit:cover">
      <?php else: ?>
        <div style="font-size:120px"><?= getCatIcon($p['cat_slug'] ?? '') ?></div>
      <?php endif; ?>
    </div>

    <div>
      <span style="display:inline-block;background:var(--surface);border:1.5px solid var(--border);border-radius:50px;padding:6px 14px;font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;margin-bottom:16px">
        <?= e($p['cat_name'] ?? 'Канцтовары') ?>
      </span>
      <h1 style="font-family:'Playfair Display',serif;font-size:clamp(24px,3vw,38px);font-weight:900;line-height:1.15;margin-bottom:16px"><?= e($p['name']) ?></h1>
      <div style="font-family:'Playfair Display',serif;font-size:42px;font-weight:700;color:var(--ink);margin-bottom:24px"><?= formatPrice($p['price']) ?></div>

      <?php if($p['description']): ?>
      <p style="font-size:16px;color:var(--ink-soft);line-height:1.75;margin-bottom:28px"><?= nl2br(e($p['description'])) ?></p>
      <?php endif; ?>

      <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px">
        <span style="font-size:14px;font-weight:700;color:<?= $p['stock']>0?'#2a9d5c':'var(--accent)' ?>">
          <?= $p['stock']>0 ? "✓ В наличии ({$p['stock']} шт.)" : "✗ Нет в наличии" ?>
        </span>
      </div>

      <?php if($p['stock'] > 0): ?>
      <div style="display:flex;gap:12px;flex-wrap:wrap">
        <button class="btn-primary btn-lg" onclick="addToCart(<?= $p['id'] ?>, this)" style="flex:1;justify-content:center;min-width:200px">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
          В корзину
        </button>
        <a href="cart.php" class="btn-outline btn-lg">Перейти в корзину</a>
      </div>
      <?php endif; ?>

      <div style="margin-top:32px;padding-top:24px;border-top:1.5px solid var(--border);display:flex;gap:24px">
        <div style="text-align:center"><div style="font-size:24px;margin-bottom:4px">🚚</div><div style="font-size:12px;font-weight:700;color:var(--ink-muted)">Быстрая<br>доставка</div></div>
        <div style="text-align:center"><div style="font-size:24px;margin-bottom:4px">🛡</div><div style="font-size:12px;font-weight:700;color:var(--ink-muted)">Гарантия<br>качества</div></div>
        <div style="text-align:center"><div style="font-size:24px;margin-bottom:4px">↩</div><div style="font-size:12px;font-weight:700;color:var(--ink-muted)">Обмен и<br>возврат</div></div>
      </div>
    </div>
  </div>

  <!-- Related -->
  <?php if(!empty($related)): ?>
  <div>
    <h2 style="font-family:'Playfair Display',serif;font-size:28px;font-weight:900;margin-bottom:24px">Похожие товары</h2>
    <div class="products-grid" style="grid-template-columns:repeat(auto-fill,minmax(220px,1fr))">
      <?php foreach($related as $r): ?>
      <div class="product-card">
        <div class="product-img">
          <?php if($r['image']): ?><img src="public/images/<?= e($r['image']) ?>" alt="<?= e($r['name']) ?>">
          <?php else: ?><div class="product-img-placeholder"><?= getCatIcon($p['cat_slug'] ?? '') ?></div><?php endif; ?>
        </div>
        <div class="product-info">
          <h3 class="product-name"><a href="product.php?id=<?= $r['id'] ?>"><?= e($r['name']) ?></a></h3>
          <div class="product-bottom">
            <span class="product-price"><?= formatPrice($r['price']) ?></span>
            <button class="btn-add-cart" onclick="addToCart(<?= $r['id'] ?>, this)">+ В корзину</button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<div class="toast" id="toast"></div>
<script src="public/js/main.js"></script>
</body>
</html>
