<?php
require_once __DIR__ . '/includes/functions.php';
if (isLoggedIn()) redirect('account.php');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name      = trim($_POST['name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $phone     = trim($_POST['phone'] ?? '');
    $confirm   = $_POST['confirm'] ?? '';
    $adminCode = trim($_POST['admin_code'] ?? '');

    if (!$name)
        $errors['name'] = 'Введите ваше имя';

    if (!$email)
        $errors['email'] = 'Введите email';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors['email'] = 'Некорректный формат email (пример: user@mail.ru)';

    if ($phone !== '' && !preg_match('/^\+[0-9\s\-\(\)]{6,20}$/', $phone))
        $errors['phone'] = 'Телефон должен начинаться с + (пример: +7 999 000-00-00)';

    if (!$password)
        $errors['password'] = 'Введите пароль';
    elseif (strlen($password) < 6)
        $errors['password'] = 'Пароль должен содержать минимум 6 символов';

    if ($password && $password !== $confirm)
        $errors['confirm'] = 'Пароли не совпадают';

    if (empty($errors)) {
        $result = register($name, $email, $password, $phone, $adminCode);
        if ($result === true) {
            login($email, $password);
            redirect('account.php');
        } else {
            $errors['email'] = $result;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Регистрация — ИМСИТ Shop</title>
<link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;600;700;900&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="public/css/style.css">
<style>
.field-hint{font-size:12px;color:var(--ink-muted);margin-top:5px}
.field-error{font-size:12px;color:#c0392b;margin-top:5px;font-weight:600}
.input-error{border-color:#c0392b!important;box-shadow:0 0 0 3px rgba(192,57,43,.1)}
.input-ok{border-color:#27ae60!important}
.strength-wrap{height:4px;border-radius:2px;margin-top:7px;background:var(--border);overflow:hidden}
.strength-bar{height:100%;border-radius:2px;transition:width .3s,background .3s;width:0}
</style>
</head>
<body>
<div class="form-page" style="background:var(--ink)">
  <div class="form-card">
    <a href="index.php" style="display:flex;align-items:center;gap:8px;margin-bottom:32px">
      <span style="font-size:18px;color:var(--accent)">✦</span>
      <span style="font-family:'Playfair Display',serif;font-weight:900;font-size:18px">ИМСИТ Shop</span>
    </a>
    <h1>Создать аккаунт</h1>
    <p class="subtitle">Зарегистрируйтесь и получите скидку</p>

    <form method="POST" novalidate>

      <div class="form-group">
        <label>Имя <span style="color:var(--accent)">*</span></label>
        <input type="text" name="name" placeholder="Иван Иванов" autocomplete="name"
               class="<?= isset($errors['name']) ? 'input-error' : '' ?>"
               value="<?= e($_POST['name'] ?? '') ?>">
        <div class="field-hint">Укажите имя и фамилию</div>
        <?php if(isset($errors['name'])): ?><div class="field-error">⚠ <?= e($errors['name']) ?></div><?php endif; ?>
      </div>

      <div class="form-group">
        <label>Email <span style="color:var(--accent)">*</span></label>
        <input type="email" name="email" id="f-email" placeholder="user@mail.ru" autocomplete="email"
               class="<?= isset($errors['email']) ? 'input-error' : '' ?>"
               value="<?= e($_POST['email'] ?? '') ?>">
        <div class="field-hint">Пример: ivanov@gmail.com · user@mail.ru · name@yandex.ru</div>
        <?php if(isset($errors['email'])): ?><div class="field-error">⚠ <?= e($errors['email']) ?></div><?php endif; ?>
      </div>

      <div class="form-group">
        <label>Телефон <span style="color:var(--ink-muted);font-size:12px">(обязательно)</span></label>
        <input type="tel" name="phone" id="f-phone" placeholder="+7 (999) 000-00-00" autocomplete="tel"
               class="<?= isset($errors['phone']) ? 'input-error' : '' ?>"
               value="<?= e($_POST['phone'] ?? '') ?>">
        <div class="field-hint">Обязательно начинайте с «+»: +7 для России, +375 для Беларуси и т.д.</div>
        <?php if(isset($errors['phone'])): ?><div class="field-error">⚠ <?= e($errors['phone']) ?></div><?php endif; ?>
      </div>

      <div class="form-group">
        <label>Пароль <span style="color:var(--accent)">*</span></label>
        <input type="password" name="password" id="f-pass" placeholder="Минимум 6 символов" autocomplete="new-password"
               class="<?= isset($errors['password']) ? 'input-error' : '' ?>">
        <div class="strength-wrap"><div class="strength-bar" id="sbar"></div></div>
        <div class="field-hint" id="stxt">Используйте буквы, цифры и спецсимволы</div>
        <?php if(isset($errors['password'])): ?><div class="field-error">⚠ <?= e($errors['password']) ?></div><?php endif; ?>
      </div>

      <div class="form-group">
        <label>Подтвердите пароль <span style="color:var(--accent)">*</span></label>
        <input type="password" name="confirm" id="f-confirm" placeholder="Повторите пароль" autocomplete="new-password"
               class="<?= isset($errors['confirm']) ? 'input-error' : '' ?>">
        <div class="field-hint" id="mhint">Введите пароль ещё раз</div>
        <?php if(isset($errors['confirm'])): ?><div class="field-error">⚠ <?= e($errors['confirm']) ?></div><?php endif; ?>
      </div>

      <div class="form-group">
        <label>Код администратора <span style="color:var(--accent);font-size:12px">(необязательно)</span></label>
        <input type="password" name="admin_code" placeholder="Только для сотрудников магазина">
        <div class="field-hint">Оставьте пустым, если вы обычный покупатель</div>
      </div>

      <button type="submit" class="btn-primary form-submit">Зарегистрироваться</button>
    </form>

    <p class="form-footer">Уже есть аккаунт? <a href="login.php">Войти</a></p>
    <p class="form-footer" style="margin-top:8px"><a href="index.php">← На главную</a></p>
  </div>
</div>
<script>
// Телефон — только + в начале
var ph=document.getElementById('f-phone');
ph.addEventListener('input',function(){
  if(this.value && !this.value.startsWith('+')){
    this.value='+'+this.value.replace(/^\++/,'');
  }
});
// Надёжность пароля
var fp=document.getElementById('f-pass'),sb=document.getElementById('sbar'),st=document.getElementById('stxt');
fp.addEventListener('input',function(){
  var v=this.value,s=0;
  if(v.length>=6)s++;if(v.length>=10)s++;
  if(/[A-Z]/.test(v))s++;if(/[0-9]/.test(v))s++;if(/[^A-Za-z0-9]/.test(v))s++;
  var L=[{w:'0%',c:'#ccc',t:'Введите пароль'},{w:'25%',c:'#e74c3c',t:'Слабый пароль'},
         {w:'50%',c:'#f39c12',t:'Средний пароль'},{w:'75%',c:'#3498db',t:'Хороший пароль'},
         {w:'100%',c:'#27ae60',t:'Надёжный пароль ✓'}];
  var l=L[Math.min(s,4)];sb.style.width=l.w;sb.style.background=l.c;st.textContent=l.t;
  chk();
});
// Совпадение паролей
var fc=document.getElementById('f-confirm'),mh=document.getElementById('mhint');
fc.addEventListener('input',chk);
function chk(){
  var p=fp.value,c=fc.value;
  if(!c){mh.textContent='Введите пароль ещё раз';mh.style.color='';return;}
  if(p===c){mh.textContent='✓ Пароли совпадают';mh.style.color='#27ae60';}
  else{mh.textContent='✗ Пароли не совпадают';mh.style.color='#e74c3c';}
}
// Email валидация при уходе с поля
document.getElementById('f-email').addEventListener('blur',function(){
  var ok=/^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/.test(this.value);
  this.classList.toggle('input-ok',ok&&this.value!=='');
  this.classList.toggle('input-error',!ok&&this.value!=='');
});
</script>
</body>
</html>
