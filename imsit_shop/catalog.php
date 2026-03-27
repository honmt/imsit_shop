<?php
require_once __DIR__ . '/includes/functions.php';
$db = getDB();

$catSlug = $_GET['category'] ?? '';
$search  = trim($_GET['q'] ?? '');
$sort    = $_GET['sort'] ?? 'newest';
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 12;
$offset  = ($page - 1) * $perPage;

// Build query
$where = ["p.is_active = 1"];
$params = [];

if ($catSlug) {
    $where[] = "c.slug = ?";
    $params[] = $catSlug;
}
if ($search) {
    $where[] = "(p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$whereStr = implode(' AND ', $where);
$orderBy  = match($sort) {
    'price_asc'  => 'p.price ASC',
    'price_desc' => 'p.price DESC',
    'name'       => 'p.name ASC',
    default      => 'p.id DESC',
};

// Count total
$countStmt = $db->prepare("SELECT COUNT(*) FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE $whereStr");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$totalPages = ceil($total / $perPage);

// Fetch products
$stmt = $db->prepare("
    SELECT p.*, c.name as cat_name, c.slug as cat_slug
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE $whereStr
    ORDER BY $orderBy
    LIMIT $perPage OFFSET $offset
");
$stmt->execute($params);
$products = $stmt->fetchAll();

// Categories for sidebar
$categories = $db->query("
    SELECT c.*, COUNT(p.id) as product_count
    FROM categories c
    LEFT JOIN products p ON p.category_id = c.id AND p.is_active = 1
    GROUP BY c.id
")->fetchAll();

$cartCount = getCartCount();

function getCatIcon(string $slug): string {
    return match($slug) {
        'ruchki'   => '🖊', 'tetradi' => '📓', 'papki' => '📁',
        'bumaga'   => '📄', 'markery' => '🖌', 'steplery' => '📎',
        default    => '📦',
    };
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Каталог — ИМСИТ Shop</title>
<link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;600;700;900&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="public/css/style.css">
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
      <a href="catalog.php" class="nav-link active">Каталог</a>
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
      <?php endif; ?>
    </div>
  </div>
</header>

<div class="page-header">
  <div class="container">
    <h1>Каталог товаров</h1>
    <p>Фирменные канцтовары академии ИМСИТ</p>
  </div>
</div>

<div class="container">
  <div class="catalog-layout">

    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="sidebar-card">
        <h3>Поиск</h3>
        <form method="GET" action="catalog.php">
          <?php if($catSlug): ?><input type="hidden" name="category" value="<?= e($catSlug) ?>"><?php endif; ?>
          <div class="form-group" style="margin:0">
            <input type="text" name="q" placeholder="Название товара..." value="<?= e($search) ?>">
          </div>
          <button type="submit" class="btn-primary" style="width:100%;justify-content:center;margin-top:12px">Найти</button>
        </form>
      </div>

      <div class="sidebar-card">
        <h3>Категории</h3>
        <div class="filter-list">
          <a href="catalog.php" class="filter-item <?= !$catSlug ? 'active' : '' ?>">
            <span>📦 Все товары</span>
            <span class="filter-count"><?= array_sum(array_column($categories, 'product_count')) ?></span>
          </a>
          <?php foreach($categories as $cat): ?>
          <a href="catalog.php?category=<?= e($cat['slug']) ?>" class="filter-item <?= $catSlug === $cat['slug'] ? 'active' : '' ?>">
            <span><?= getCatIcon($cat['slug']) ?> <?= e($cat['name']) ?></span>
            <span class="filter-count"><?= $cat['product_count'] ?></span>
          </a>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="sidebar-card">
        <h3>Сортировка</h3>
        <div class="filter-list">
          <?php
          $sorts = ['newest'=>'Сначала новые','price_asc'=>'Цена: дешевле','price_desc'=>'Цена: дороже','name'=>'По названию'];
          foreach($sorts as $key=>$label):
            $url = '?' . http_build_query(array_merge($_GET, ['sort'=>$key]));
          ?>
          <a href="<?= $url ?>" class="filter-item <?= $sort===$key?'active':'' ?>">
            <?= $label ?>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
    </aside>

    <!-- Products -->
    <main>
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
        <p style="font-weight:600;color:var(--ink-muted)">Найдено товаров: <strong style="color:var(--ink)"><?= $total ?></strong></p>
      </div>

      <?php if(empty($products)): ?>
      <div style="text-align:center;padding:80px 0">
        <div style="font-size:64px;margin-bottom:16px">🔍</div>
        <h3 style="font-family:'Playfair Display',serif;font-size:24px;margin-bottom:8px">Ничего не найдено</h3>
        <p style="color:var(--ink-muted)">Попробуйте другой запрос или категорию</p>
        <a href="catalog.php" class="btn-primary" style="margin-top:20px;display:inline-flex">Все товары</a>
      </div>
      <?php else: ?>

      <div class="products-grid">
        <?php foreach($products as $p): ?>
        <div class="product-card">
          <div class="product-img">
            <?php if($p['image']): ?>
              <img src="public/images/<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>">
            <?php else: ?>
              <div class="product-img-placeholder"><?= getCatIcon($p['cat_slug'] ?? '') ?></div>
            <?php endif; ?>
            <?php if($p['is_featured']): ?><span class="badge-hot">ХИТ</span><?php endif; ?>
            <?php if($p['stock'] == 0): ?><span class="badge-hot" style="background:#6b6a7e">НЕТ</span><?php endif; ?>
          </div>
          <div class="product-info">
            <span class="product-cat"><?= e($p['cat_name'] ?? '') ?></span>
            <h3 class="product-name"><a href="product.php?id=<?= $p['id'] ?>"><?= e($p['name']) ?></a></h3>
            <div class="product-bottom">
              <span class="product-price"><?= formatPrice($p['price']) ?></span>
              <?php if($p['stock'] > 0): ?>
              <button class="btn-add-cart" onclick="addToCart(<?= $p['id'] ?>, this)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                В корзину
              </button>
              <?php else: ?>
              <span style="font-size:13px;color:var(--ink-muted);font-weight:600">Нет в наличии</span>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Pagination -->
      <?php if($totalPages > 1): ?>
      <div style="display:flex;justify-content:center;gap:8px;margin-top:40px">
        <?php for($i=1; $i<=$totalPages; $i++): ?>
        <a href="?<?= http_build_query(array_merge($_GET,['page'=>$i])) ?>"
           style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;border-radius:10px;font-weight:700;border:1.5px solid var(--border);background:<?= $i==$page?'var(--ink)':'var(--white)' ?>;color:<?= $i==$page?'var(--white)':'var(--ink)' ?>">
          <?= $i ?>
        </a>
        <?php endfor; ?>
      </div>
      <?php endif; ?>

      <?php endif; ?>
    </main>
  </div>
</div>

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

<div class="toast" id="toast"></div>
<script src="public/js/main.js"></script>
</body>
</html>
