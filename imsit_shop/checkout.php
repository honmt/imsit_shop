<?php
require_once __DIR__ . '/includes/functions.php';
if (!isLoggedIn()) redirect('login.php?redirect=checkout.php');

$items = getCart();
if (empty($items)) redirect('cart.php');

$db    = getDB();
$user  = currentUser();
$errors = [];
$cartCount = getCartCount();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['full_name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $payment = in_array($_POST['payment'] ?? '', ['card','cash']) ? $_POST['payment'] : 'cash';

    // Валидация
    if (!$name)
        $errors['full_name'] = 'Введите полное имя';

    if (!$email)
        $errors['email'] = 'Введите email';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors['email'] = 'Некорректный формат email (пример: user@mail.ru)';

    if (!$phone)
        $errors['phone'] = 'Введите телефон';
    elseif (!preg_match('/^\+[0-9\s\-\(\)]{6,20}$/', $phone))
        $errors['phone'] = 'Телефон должен начинаться с + (пример: +7 999 000-00-00)';

    if (!$address)
        $errors['address'] = 'Введите адрес доставки';
    elseif (mb_strlen($address) < 10)
        $errors['address'] = 'Укажите полный адрес (город, улица, дом)';

    if (empty($errors)) {
        $total = getCartTotal();
        $db->beginTransaction();
        try {
            // PDO prepare — защита от SQL-инъекций
            $stmt = $db->prepare(
                "INSERT INTO orders (user_id, full_name, email, phone, address, total, payment_method)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([$user['id'], $name, $email, $phone, $address, $total, $payment]);
            $orderId = $db->lastInsertId();

            foreach ($items as $item) {
                $db->prepare(
                    "INSERT INTO order_items (order_id, product_id, product_name, price, quantity)
                     VALUES (?, ?, ?, ?, ?)"
                )->execute([$orderId, $item['product_id'], $item['name'], $item['price'], $item['quantity']]);

                $db->prepare(
                    "UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?"
                )->execute([$item['quantity'], $item['product_id'], $item['quantity']]);
            }

            clearCart();
            $db->commit();
            redirect('order-success.php?id=' . $orderId);
        } catch (Exception $e) {
            $db->rollBack();
            $errors['global'] = 'Ошибка оформления заказа. Попробуйте ещё раз.';
        }
    }
}
$total = getCartTotal();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Оформление заказа — ИМСИТ Shop</title>
<link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;600;700;900&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="public/css/style.css">
<style>
.field-hint{font-size:12px;color:var(--ink-muted);margin-top:5px}
.field-error{font-size:12px;color:#c0392b;margin-top:5px;font-weight:600}
.input-error{border-color:#c0392b!important;box-shadow:0 0 0 3px rgba(192,57,43,.1)}
</style>
</head>
<body>

<header class="header">
  <div class="header-inner container">
    <a href="index.php" class="logo"><span class="logo-icon">✦</span><div class="logo-text"><span class="logo-main">ИМСИТ</span><span class="logo-sub">Shop</span></div></a>
    <div class="header-actions" style="margin-left:auto">
      <a href="cart.php" class="btn-outline">← Назад в корзину</a>
    </div>
  </div>
</header>

<div class="page-header">
  <div class="container">
    <h1>Оформление заказа</h1>
    <p>Заполните данные для доставки</p>
  </div>
</div>

<div class="container" style="padding:48px 24px">
  <?php if(isset($errors['global'])): ?>
  <div class="alert alert-error" style="max-width:700px;margin:0 auto 24px"><?= e($errors['global']) ?></div>
  <?php endif; ?>

  <div style="display:grid;grid-template-columns:1fr 360px;gap:32px;max-width:1000px;margin:0 auto">
    <div>
      <form method="POST" novalidate>
        <div style="background:var(--white);border-radius:var(--radius);border:1.5px solid var(--border);padding:32px;margin-bottom:24px">
          <h2 style="font-family:'Playfair Display',serif;font-size:22px;margin-bottom:24px">Данные получателя</h2>

          <div class="form-group">
            <label>Полное имя <span style="color:var(--accent)">*</span></label>
            <input type="text" name="full_name" autocomplete="name"
                   class="<?= isset($errors['full_name']) ? 'input-error' : '' ?>"
                   placeholder="Иван Иванович Иванов"
                   value="<?= e($user['name'] ?? $_POST['full_name'] ?? '') ?>">
            <div class="field-hint">Имя, отчество и фамилия получателя</div>
            <?php if(isset($errors['full_name'])): ?><div class="field-error">⚠ <?= e($errors['full_name']) ?></div><?php endif; ?>
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
            <div class="form-group">
              <label>Email <span style="color:var(--accent)">*</span></label>
              <input type="email" name="email" id="co-email" autocomplete="email"
                     class="<?= isset($errors['email']) ? 'input-error' : '' ?>"
                     placeholder="user@mail.ru"
                     value="<?= e($user['email'] ?? $_POST['email'] ?? '') ?>">
              <div class="field-hint">Для подтверждения заказа</div>
              <?php if(isset($errors['email'])): ?><div class="field-error">⚠ <?= e($errors['email']) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
              <label>Телефон <span style="color:var(--accent)">*</span></label>
              <input type="tel" name="phone" id="co-phone" autocomplete="tel"
                     class="<?= isset($errors['phone']) ? 'input-error' : '' ?>"
                     placeholder="+7 (999) 000-00-00"
                     value="<?= e($user['phone'] ?? $_POST['phone'] ?? '') ?>">
              <div class="field-hint">Начните с «+»: +7, +375, +380</div>
              <?php if(isset($errors['phone'])): ?><div class="field-error">⚠ <?= e($errors['phone']) ?></div><?php endif; ?>
            </div>
          </div>

          <div class="form-group">
            <label>Адрес доставки <span style="color:var(--accent)">*</span></label>
            <input type="text" name="address" autocomplete="street-address"
                   class="<?= isset($errors['address']) ? 'input-error' : '' ?>"
                   placeholder="Краснодар, ул. Зиповская, 5, кв. 10"
                   value="<?= e($user['address'] ?? $_POST['address'] ?? '') ?>">
            <div class="field-hint">Укажите город, улицу, номер дома и квартиры</div>
            <?php if(isset($errors['address'])): ?><div class="field-error">⚠ <?= e($errors['address']) ?></div><?php endif; ?>
          </div>
        </div>

        <div style="background:var(--white);border-radius:var(--radius);border:1.5px solid var(--border);padding:32px">
          <h2 style="font-family:'Playfair Display',serif;font-size:22px;margin-bottom:24px">Способ оплаты</h2>
          <div style="display:flex;flex-direction:column;gap:12px">
            <label style="display:flex;align-items:center;gap:14px;padding:16px;border-radius:12px;border:1.5px solid var(--border);cursor:pointer">
              <input type="radio" name="payment" value="card" style="width:18px;height:18px;accent-color:var(--ink)"
                     <?= (($_POST['payment'] ?? '') === 'card') ? 'checked' : '' ?>>
              <div><strong style="display:block">💳 Банковская карта</strong><span style="font-size:13px;color:var(--ink-muted)">Visa, MasterCard, МИР</span></div>
            </label>
            <label style="display:flex;align-items:center;gap:14px;padding:16px;border-radius:12px;border:1.5px solid var(--ink);cursor:pointer">
              <input type="radio" name="payment" value="cash" style="width:18px;height:18px;accent-color:var(--ink)"
                     <?= (($_POST['payment'] ?? 'cash') !== 'card') ? 'checked' : '' ?>>
              <div><strong style="display:block">💵 Наличные при получении</strong><span style="font-size:13px;color:var(--ink-muted)">Оплата курьеру</span></div>
            </label>
          </div>
        </div>

        <button type="submit" class="btn-primary" style="width:100%;justify-content:center;font-size:16px;padding:18px;border-radius:16px;margin-top:24px">
          Подтвердить заказ на <?= formatPrice($total) ?>
        </button>
      </form>
    </div>

    <!-- Состав заказа -->
    <div>
      <div style="background:var(--white);border-radius:var(--radius);border:1.5px solid var(--border);padding:28px;position:sticky;top:90px">
        <h3 style="font-family:'Playfair Display',serif;font-size:20px;margin-bottom:20px">Ваш заказ</h3>
        <?php foreach($items as $item): ?>
        <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:16px;padding-bottom:16px;border-bottom:1px solid var(--border)">
          <div style="flex:1">
            <div style="font-weight:700;font-size:14px;margin-bottom:2px"><?= e($item['name']) ?></div>
            <div style="font-size:13px;color:var(--ink-muted)"><?= $item['quantity'] ?> × <?= formatPrice($item['price']) ?></div>
          </div>
          <div style="font-family:'Playfair Display',serif;font-weight:700;font-size:16px"><?= formatPrice($item['price'] * $item['quantity']) ?></div>
        </div>
        <?php endforeach; ?>
        <div style="display:flex;justify-content:space-between;font-family:'Playfair Display',serif;font-size:22px;font-weight:700;padding-top:8px">
          <span>Итого</span><span><?= formatPrice($total) ?></span>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Телефон — только + в начале
var cop=document.getElementById('co-phone');
if(cop) cop.addEventListener('input',function(){
  if(this.value && !this.value.startsWith('+')){
    this.value='+'+this.value.replace(/^\++/,'');
  }
});
// Email валидация
var coe=document.getElementById('co-email');
if(coe) coe.addEventListener('blur',function(){
  var ok=/^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/.test(this.value);
  this.classList.toggle('input-error',!ok&&this.value!=='');
});
</script>
<script src="public/js/main.js"></script>
</body>
</html>
