<?php
require_once 'functions.php';
if (isLoggedIn()) header('Location: /mysite/index.php');
?>
<?php include 'header.php'; ?>

<div class="auth-box">
    <h2>Вход</h2>
    <div id="login-message" class="message-box"></div>
    
    <form id="loginForm">
        <div class="form-group">
            <label>Логин:</label>
            <input type="text" name="login" required>
        </div>
        
        <div class="form-group">
            <label>Пароль:</label>
            <input type="password" name="password" required>
        </div>
        
        <button type="submit">Войти</button>
    </form>
    
    <p class="auth-link">Нет аккаунта? <a href="/mysite/register.php">Зарегистрируйтесь</a></p>
</div>

<script>
document.getElementById('loginForm').onsubmit = async function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    let response = await fetch('/mysite/api.php?action=login', {
        method: 'POST',
        body: formData
    });
    let result = await response.json();
    let msgDiv = document.getElementById('login-message');
    
    if (result.success) {
        msgDiv.innerHTML = '<p style="color: green;">Успешно! Перенаправление...</p>';
        setTimeout(() => window.location.href = '/mysite/index.php', 1000);
    } else {
        msgDiv.innerHTML = '<p style="color: red;">' + result.error + '</p>';
    }
};
</script>

</body>
</html>