<?php
require_once __DIR__ . '/includes/functions.php';
$db = getDB();

// Featured products
$featured = $db->query("SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.is_featured=1 AND p.is_active=1 LIMIT 6")->fetchAll();

// All categories
$categories = $db->query("SELECT * FROM categories")->fetchAll();

// Popular products
$popular = $db->query("SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.is_active=1 ORDER BY p.id DESC LIMIT 8")->fetchAll();

$cartCount = getCartCount();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ИМСИТ Shop — Канцтовары академии</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;600;700;900&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="public/css/style.css">
</head>
<body>

<!-- ═══════════════ HEADER ═══════════════ -->
<header class="header">
  <div class="header-inner container">
    <a href="index.php" class="logo">
      <span class="logo-icon">✦</span>
      <div class="logo-text">
        <span class="logo-main">ИМСИТ</span>
        <span class="logo-sub">Shop</span>
      </div>
    </a>

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
      <?php if(isLoggedIn()): ?>
        <a href="account.php" class="btn-outline">Кабинет</a>
      <?php else: ?>
        <a href="login.php" class="btn-outline">Войти</a>
        <a href="register.php" class="btn-primary">Регистрация</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<!-- ═══════════════ HERO ═══════════════ -->
<section class="hero">
  <div class="hero-bg">
    <div class="hero-shape s1"></div>
    <div class="hero-shape s2"></div>
    <div class="hero-shape s3"></div>
    <div class="hero-dots"></div>
  </div>
  <div class="container hero-content">
    <div class="hero-tag">НАН ЧОУ ВО Академия ИМСИТ</div>
    <h1 class="hero-title">Канцтовары<br><span class="accent">с душой</span><br>академии</h1>
    <p class="hero-desc">Фирменные канцелярские принадлежности с логотипом ИМСИТ. Для студентов, преподавателей и сотрудников академии.</p>
    <div class="hero-btns">
      <a href="catalog.php" class="btn-primary btn-lg">Перейти в каталог</a>
      <a href="#featured" class="btn-ghost btn-lg">Популярное ↓</a>
    </div>
    <div class="hero-stats">
      <div class="stat"><strong>50+</strong><span>товаров</span></div>
      <div class="stat"><strong>6</strong><span>категорий</span></div>
      <div class="stat"><strong>Быстрая</strong><span>доставка</span></div>
    </div>
  </div>
  <div class="hero-visual">
    <div class="pencil-wrap">
      <div class="pencil p1"></div>
      <div class="pencil p2"></div>
      <div class="pencil p3"></div>
      <div class="notebook"></div>
    </div>
  </div>
</section>

<!-- ═══════════════ CATEGORIES ═══════════════ -->
<section class="section categories-section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Категории</h2>
      <a href="catalog.php" class="see-all">Все товары →</a>
    </div>
    <div class="categories-grid">
      <?php foreach($categories as $cat): ?>
      <a href="catalog.php?category=<?= e($cat['slug']) ?>" class="cat-card">
        <div class="cat-icon"><?= getCatIcon($cat['slug']) ?></div>
        <span><?= e($cat['name']) ?></span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ═══════════════ FEATURED ═══════════════ -->
<section class="section featured-section" id="featured">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Популярные товары</h2>
      <a href="catalog.php" class="see-all">Смотреть все →</a>
    </div>
    <div class="products-grid">
      <?php foreach($featured as $p): ?>
      <div class="product-card" data-id="<?= $p['id'] ?>">
        <div class="product-img">
          <?php if($p['image']): ?>
            <img src="public/images/<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>">
          <?php else: ?>
            <div class="product-img-placeholder"><?= getCatIcon(getSlugById($p['category_id'], $categories)) ?></div>
          <?php endif; ?>
          <?php if($p['is_featured']): ?><span class="badge-hot">ХИТ</span><?php endif; ?>
        </div>
        <div class="product-info">
          <span class="product-cat"><?= e($p['cat_name'] ?? '') ?></span>
          <h3 class="product-name"><a href="product.php?id=<?= $p['id'] ?>"><?= e($p['name']) ?></a></h3>
          <div class="product-bottom">
            <span class="product-price"><?= formatPrice($p['price']) ?></span>
            <button class="btn-add-cart" onclick="addToCart(<?= $p['id'] ?>, this)">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              В корзину
            </button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ═══════════════ BANNER ═══════════════ -->
<section class="promo-banner">
  <div class="container promo-inner">
    <div class="promo-text">
      <h2>Студентам ИМСИТ — скидка 10%</h2>
      <p>Зарегистрируйтесь с корпоративной почтой @imsit.ru и получите постоянную скидку на все товары</p>
      <a href="register.php" class="btn-primary">Зарегистрироваться</a>
    </div>
    <div class="promo-deco">
      <div class="deco-circle c1"></div>
      <div class="deco-circle c2"></div>
      <div class="deco-star">✦</div>
    </div>
  </div>
</section>

<!-- ═══════════════ NEW ARRIVALS ═══════════════ -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Все товары</h2>
      <a href="catalog.php" class="see-all">Каталог →</a>
    </div>
    <div class="products-grid">
      <?php foreach($popular as $p): ?>
      <div class="product-card" data-id="<?= $p['id'] ?>">
        <div class="product-img">
          <?php if($p['image']): ?>
            <img src="public/images/<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>">
          <?php else: ?>
            <div class="product-img-placeholder"><?= getCatIcon(getSlugById($p['category_id'], $categories)) ?></div>
          <?php endif; ?>
        </div>
        <div class="product-info">
          <span class="product-cat"><?= e($p['cat_name'] ?? '') ?></span>
          <h3 class="product-name"><a href="product.php?id=<?= $p['id'] ?>"><?= e($p['name']) ?></a></h3>
          <div class="product-bottom">
            <span class="product-price"><?= formatPrice($p['price']) ?></span>
            <button class="btn-add-cart" onclick="addToCart(<?= $p['id'] ?>, this)">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              В корзину
            </button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ═══════════════ FOOTER ═══════════════ -->
<footer class="footer">
  <div class="container footer-inner">
    <div class="footer-brand">
      <a href="index.php" class="logo">
        <span class="logo-icon">✦</span>
        <div class="logo-text">
          <span class="logo-main">ИМСИТ</span>
          <span class="logo-sub">Shop</span>
        </div>
      </a>
      <p>Официальный магазин канцтоваров<br>НАН ЧОУ ВО Академия ИМСИТ</p>
    </div>
    <div class="footer-links">
      <h4>Магазин</h4>
      <a href="catalog.php">Каталог</a>
      <a href="cart.php">Корзина</a>
    </div>
    <div class="footer-links">
      <h4>Информация</h4>
      <a href="about.php">О магазине</a>
      <a href="delivery.php">Доставка и оплата</a>
      <a href="contacts.php">Контакты</a>
    </div>
    <div class="footer-contact">
      <h4>Контакты</h4>
      <p>г. Краснодар, ул. Зиповская, 5</p>
      <p>+7 (861) 200-00-00</p>
      <p>shop@imsit.ru</p>
    </div>
  </div>
  <div class="footer-bottom">
    <p>© <?= date('Y') ?> ИМСИТ Shop. Все права защищены.</p>
  </div>
</footer>

<!-- Toast -->
<div class="toast" id="toast"></div>

<script src="public/js/main.js"></script>
</body>
</html>

<?php
function getCatIcon(string $slug): string {
    return match($slug) {
        'ruchki'    => '🖊',
        'tetradi'   => '📓',
        'papki'     => '📁',
        'bumaga'    => '📄',
        'markery'   => '🖌',
        'steplery'  => '📎',
        default     => '📦',
    };
}
function getSlugById(?int $id, array $cats): string {
    foreach($cats as $c) if($c['id'] == $id) return $c['slug'];
    return '';
}
?>
