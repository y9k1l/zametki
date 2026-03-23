<?php
require_once 'functions.php';
if (isLoggedIn()) header('Location: /mysite/cabinet.php');
?>
<?php include 'header.php'; ?>

<div id="auth-message"></div>

<div id="login-box">
    <h2>Вход</h2>
    <form id="loginForm">
        <input type="text" name="login" placeholder="Логин" required><br>
        <input type="password" name="password" placeholder="Пароль" required><br>
        <button type="submit">Войти</button>
    </form>
</div>

<div id="register-box">
    <h2>Регистрация</h2>
    <form id="registerForm">
        <input type="text" name="login" placeholder="Логин" required><br>
        <input type="password" name="password" placeholder="Пароль" required><br>
        <button type="submit">Зарегистрироваться</button>
    </form>
</div>

<script>
document.getElementById('loginForm').onsubmit = async function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    let response = await fetch('/mysite/api.php?action=login', { method: 'POST', body: formData });
    let result = await response.json();
    let msgDiv = document.getElementById('auth-message');
    if (result.success) {
        msgDiv.innerHTML = '<p style="color:green;">Успешно! Перенаправление...</p>';
        setTimeout(() => location.href = '/mysite/cabinet.php', 1000);
    } else {
        msgDiv.innerHTML = '<p style="color:red;">' + result.error + '</p>';
    }
};

document.getElementById('registerForm').onsubmit = async function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    let response = await fetch('/mysite/api.php?action=register', { method: 'POST', body: formData });
    let result = await response.json();
    let msgDiv = document.getElementById('auth-message');
    if (result.success) {
        msgDiv.innerHTML = '<p style="color:green;">Регистрация успешна! Теперь войдите.</p>';
        this.reset();
    } else {
        msgDiv.innerHTML = '<p style="color:red;">' + result.error + '</p>';
    }
};
</script>

<?php include 'footer.php'; ?>