<?php
require_once __DIR__ . '/includes/functions.php';
$cartCount = getCartCount();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>О магазине — ИМСИТ Shop</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;600;700;900&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="public/css/style.css">
<style>
  .about-hero {
    background: var(--ink);
    color: var(--white);
    padding: 80px 0 64px;
    position: relative;
    overflow: hidden;
  }
  .about-hero::before {
    content: '';
    position: absolute;
    top: -80px; right: -80px;
    width: 400px; height: 400px;
    border-radius: 50%;
    background: var(--accent);
    opacity: .08;
  }
  .about-hero::after {
    content: '';
    position: absolute;
    bottom: -60px; left: -60px;
    width: 300px; height: 300px;
    border-radius: 50%;
    background: var(--gold);
    opacity: .06;
  }
  .about-hero .container { position: relative; z-index: 1; }
  .about-tag {
    display: inline-flex; align-items: center; gap: 8px;
    background: rgba(255,255,255,.1);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 50px;
    padding: 6px 16px;
    font-size: 12px; font-weight: 700;
    letter-spacing: .08em; text-transform: uppercase;
    color: var(--gold);
    margin-bottom: 24px;
  }
  .about-hero h1 {
    font-family: 'Playfair Display', serif;
    font-size: clamp(36px, 5vw, 64px);
    font-weight: 900;
    line-height: 1.1;
    margin-bottom: 20px;
  }
  .about-hero h1 span { color: var(--accent); }
  .about-hero p {
    font-size: 18px;
    color: rgba(255,255,255,.7);
    max-width: 560px;
    line-height: 1.7;
  }

  /* Статы */
  .stats-bar {
    background: var(--white);
    border-bottom: 1.5px solid var(--border);
    padding: 36px 0;
  }
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0;
  }
  .stat-item {
    text-align: center;
    padding: 0 24px;
    border-right: 1.5px solid var(--border);
  }
  .stat-item:last-child { border-right: none; }
  .stat-num {
    font-family: 'Playfair Display', serif;
    font-size: 42px;
    font-weight: 900;
    color: var(--ink);
    line-height: 1;
    margin-bottom: 6px;
  }
  .stat-num span { color: var(--accent); }
  .stat-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--ink-muted);
    text-transform: uppercase;
    letter-spacing: .06em;
  }

  /* О нас */
  .about-section {
    padding: 80px 0;
  }
  .about-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 80px;
    align-items: center;
  }
  .about-label {
    display: inline-block;
    font-size: 11px; font-weight: 800;
    letter-spacing: .12em; text-transform: uppercase;
    color: var(--accent);
    margin-bottom: 16px;
  }
  .about-grid h2 {
    font-family: 'Playfair Display', serif;
    font-size: 38px;
    font-weight: 900;
    line-height: 1.2;
    margin-bottom: 20px;
  }
  .about-grid p {
    font-size: 16px;
    color: var(--ink-soft);
    line-height: 1.8;
    margin-bottom: 16px;
  }
  .about-visual {
    background: var(--ink);
    border-radius: var(--radius);
    padding: 48px;
    position: relative;
    overflow: hidden;
    min-height: 360px;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
  }
  .about-visual::before {
    content: '✦';
    position: absolute;
    top: 32px; right: 32px;
    font-size: 48px;
    color: var(--accent);
    opacity: .6;
  }
  .about-visual-deco {
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    width: 200px; height: 200px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(200,55,45,.2) 0%, transparent 70%);
  }
  .about-visual-text {
    position: relative; z-index: 1;
    font-family: 'Playfair Display', serif;
    font-size: 28px;
    font-weight: 900;
    color: var(--white);
    line-height: 1.3;
  }
  .about-visual-text span { color: var(--accent); }
  .about-visual-sub {
    position: relative; z-index: 1;
    font-size: 13px;
    color: rgba(255,255,255,.5);
    margin-top: 8px;
    font-weight: 600;
    letter-spacing: .04em;
  }

  /* Ценности */
  .values-section {
    background: var(--surface);
    padding: 80px 0;
  }
  .values-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
    margin-top: 48px;
  }
  .value-card {
    background: var(--white);
    border: 1.5px solid var(--border);
    border-radius: var(--radius);
    padding: 36px 28px;
    transition: all .25s;
  }
  .value-card:hover {
    border-color: var(--ink);
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
  }
  .value-icon {
    font-size: 36px;
    margin-bottom: 20px;
    display: block;
  }
  .value-card h3 {
    font-family: 'Playfair Display', serif;
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 12px;
  }
  .value-card p {
    font-size: 14px;
    color: var(--ink-muted);
    line-height: 1.7;
  }

  /* Команда / Академия */
  .academy-section {
    padding: 80px 0;
  }
  .academy-card {
    background: var(--ink);
    border-radius: var(--radius);
    padding: 64px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 64px;
    align-items: center;
    position: relative;
    overflow: hidden;
  }
  .academy-card::before {
    content: '';
    position: absolute;
    top: -100px; right: -100px;
    width: 400px; height: 400px;
    border-radius: 50%;
    background: var(--accent);
    opacity: .06;
  }
  .academy-card h2 {
    font-family: 'Playfair Display', serif;
    font-size: 36px;
    font-weight: 900;
    color: var(--white);
    line-height: 1.2;
    margin-bottom: 20px;
  }
  .academy-card h2 span { color: var(--accent); }
  .academy-card p {
    font-size: 15px;
    color: rgba(255,255,255,.65);
    line-height: 1.8;
    margin-bottom: 14px;
  }
  .academy-card .btn-primary {
    background: var(--accent);
    border-color: var(--accent);
    margin-top: 8px;
  }
  .academy-card .btn-primary:hover {
    background: var(--white);
    border-color: var(--white);
    color: var(--ink);
  }
  .academy-info {
    position: relative; z-index: 1;
  }
  .academy-list {
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 16px;
  }
  .academy-list li {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    color: rgba(255,255,255,.75);
    font-size: 15px;
    line-height: 1.6;
  }
  .academy-list li::before {
    content: '✦';
    color: var(--gold);
    font-size: 12px;
    margin-top: 4px;
    flex-shrink: 0;
  }

  /* Контакты */
  .contacts-section {
    background: var(--surface);
    padding: 80px 0;
  }
  .contacts-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
    margin-top: 48px;
  }
  .contact-card {
    background: var(--white);
    border: 1.5px solid var(--border);
    border-radius: var(--radius);
    padding: 32px 28px;
    text-align: center;
  }
  .contact-icon {
    font-size: 32px;
    margin-bottom: 16px;
    display: block;
  }
  .contact-card h4 {
    font-family: 'Playfair Display', serif;
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 10px;
  }
  .contact-card p {
    font-size: 14px;
    color: var(--ink-muted);
    line-height: 1.7;
  }
  .contact-card a {
    color: var(--accent);
    font-weight: 600;
  }

  .section-header-center {
    text-align: center;
    margin-bottom: 0;
  }
  .section-header-center h2 {
    font-family: 'Playfair Display', serif;
    font-size: 38px;
    font-weight: 900;
    margin-bottom: 12px;
  }
  .section-header-center p {
    font-size: 16px;
    color: var(--ink-muted);
    max-width: 480px;
    margin: 0 auto;
    line-height: 1.7;
  }

  @media (max-width: 768px) {
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
    .stat-item { border-right: none; border-bottom: 1.5px solid var(--border); padding: 16px; }
    .stat-item:nth-child(odd) { border-right: 1.5px solid var(--border); }
    .stat-item:last-child { border-bottom: none; }
    .about-grid, .academy-card { grid-template-columns: 1fr; gap: 40px; }
    .academy-card { padding: 36px 24px; }
    .values-grid, .contacts-grid { grid-template-columns: 1fr; }
  }
</style>
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
      <a href="index.php" class="nav-link">Главная</a>
      <a href="catalog.php" class="nav-link">Каталог</a>
      <a href="about.php" class="nav-link active">О магазине</a>
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
<section class="about-hero">
  <div class="container">
    <div class="about-tag">✦ О нас</div>
    <h1>Магазин<br>с <span>характером</span><br>академии</h1>
    <p>ИМСИТ Shop - официальный магазин канцелярских товаров НАН ЧОУ ВО Академии ИМСИТ. Мы создаём продукцию с душой и фирменным стилем для студентов, преподавателей и сотрудников.</p>
  </div>
</section>

<!-- ═══════════════ СТАТЫ ═══════════════ -->
<div class="stats-bar">
  <div class="container">
    <div class="stats-grid">
      <div class="stat-item">
        <div class="stat-num">50<span>+</span></div>
        <div class="stat-label">Товаров в каталоге</div>
      </div>
      <div class="stat-item">
        <div class="stat-num">6</div>
        <div class="stat-label">Категорий</div>
      </div>
      <div class="stat-item">
        <div class="stat-num">1<span>к+</span></div>
        <div class="stat-label">Довольных клиентов</div>
      </div>
      <div class="stat-item">
        <div class="stat-num">24<span>ч</span></div>
        <div class="stat-label">Быстрая доставка</div>
      </div>
    </div>
  </div>
</div>

<!-- ═══════════════ О НАС ═══════════════ -->
<section class="about-section">
  <div class="container">
    <div class="about-grid">
      <div>
        <span class="about-label">Наша история</span>
        <h2>Канцтовары, которые вдохновляют</h2>
        <p>ИМСИТ Shop был создан как удобный способ для студентов и сотрудников академии приобретать качественные канцелярские принадлежности с фирменной символикой ИМСИТ.</p>
        <p>Каждый товар в нашем магазине разработан с вниманием к деталям - от ручек и тетрадей до папок и маркеров. Мы гордимся тем, что наша продукция помогает учёбе и работе каждый день.</p>
        <p>Сегодня ИМСИТ Shop - это не просто магазин, это часть академической культуры ИМСИТ.</p>
        <a href="catalog.php" class="btn-primary" style="margin-top:8px">Перейти в каталог</a>
      </div>
      <div class="about-visual">
        <div class="about-visual-deco"></div>
        <div class="about-visual-text">НАН ЧОУ ВО<br>Академия <span>ИМСИТ</span></div>
        <div class="about-visual-sub">г. Краснодар, ул. Зиповская, 5</div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════ ЦЕННОСТИ ═══════════════ -->
<section class="values-section">
  <div class="container">
    <div class="section-header-center">
      <span class="about-label">Почему мы</span>
      <h2>Наши ценности</h2>
      <p>Мы стремимся к тому, чтобы каждый товар был достоин студентов и преподавателей академии</p>
    </div>
    <div class="values-grid">
      <div class="value-card">
        <span class="value-icon">🎯</span>
        <h3>Качество</h3>
        <p>Все товары проходят строгий контроль качества. Мы используем только надёжные материалы и проверенных производителей.</p>
      </div>
      <div class="value-card">
        <span class="value-icon">🚀</span>
        <h3>Быстрая доставка</h3>
        <p>Доставляем заказы в течение 24 часов по территории академии и в течение 3 дней по Краснодару.</p>
      </div>
      <div class="value-card">
        <span class="value-icon">💎</span>
        <h3>Фирменный стиль</h3>
        <p>Каждый товар выполнен в фирменном стиле академии ИМСИТ - с логотипом и фирменными цветами.</p>
      </div>
      <div class="value-card">
        <span class="value-icon">💰</span>
        <h3>Доступные цены</h3>
        <p>Мы работаем напрямую с поставщиками, чтобы предлагать лучшие цены для студентов и сотрудников.</p>
      </div>
      <div class="value-card">
        <span class="value-icon">🛡</span>
        <h3>Гарантия</h3>
        <p>На все товары действует гарантия. Если что-то пошло не так — мы вернём деньги или заменим товар.</p>
      </div>
      <div class="value-card">
        <span class="value-icon">🤝</span>
        <h3>Поддержка</h3>
        <p>Наша команда всегда готова помочь с выбором товара и ответить на любые вопросы по заказу.</p>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════ АКАДЕМИЯ ═══════════════ -->
<section class="academy-section">
  <div class="container">
    <div class="academy-card">
      <div style="position:relative;z-index:1">
        <span class="about-label" style="color:var(--gold)">Об академии</span>
        <h2>НАН ЧОУ ВО<br><span>Академия ИМСИТ</span></h2>
        <p>Академия ИМСИТ - одно из ведущих частных высших учебных заведений Краснодарского края. Основана с целью подготовки высококвалифицированных специалистов.</p>
        <p>Мы гордимся тем, что наш магазин является официальной частью инфраструктуры академии и служит интересам всего студенческого сообщества.</p>
        <a href="https://imsit.ru" target="_blank" class="btn-primary">Сайт академии →</a>
      </div>
      <div class="academy-info">
        <ul class="academy-list">
          <li>Более 20 лет на рынке образовательных услуг</li>
          <li>Тысячи выпускников по всей России</li>
          <li>Современный кампус в центре Краснодара</li>
          <li>Аккредитованные программы бакалавриата и магистратуры</li>
          <li>Активная студенческая жизнь и научная деятельность</li>
          <li>Партнёрство с ведущими компаниями России</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════ КОНТАКТЫ ═══════════════ -->
<section class="contacts-section">
  <div class="container">
    <div class="section-header-center">
      <span class="about-label">Связаться с нами</span>
      <h2>Контакты</h2>
      <p>Мы всегда рады ответить на ваши вопросы</p>
    </div>
    <div class="contacts-grid">
      <div class="contact-card">
        <span class="contact-icon">📍</span>
        <h4>Адрес</h4>
        <p>г. Краснодар<br>ул. Зиповская, 5<br>НАН ЧОУ ВО Академия ИМСИТ</p>
      </div>
      <div class="contact-card">
        <span class="contact-icon">📞</span>
        <h4>Телефон</h4>
        <p><a href="tel:+78612000000">+7 (952) 836-40-73</a><br><br>Пн–Пт: 9:00 – 18:00</p>
      </div>
      <div class="contact-card">
        <span class="contact-icon">✉️</span>
        <h4>Email</h4>
        <p><a href="mailto:shop@imsit.ru">shop@imsit.ru</a><br><br>Ответим в течение 24 часов</p>
      </div>
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

<div class="toast" id="toast"></div>
<script src="public/js/main.js"></script>
</body>
</html>
