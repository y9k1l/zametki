<?php
require_once 'functions.php';
if (isLoggedIn()) header('Location: /mysite/index.php');
?>
<?php include 'header.php'; ?>

<div class="auth-box">
    <h2>Регистрация</h2>
    <div id="register-message" class="message-box"></div>
    
    <form id="registerForm">
        <div class="form-group">
            <label>Логин (минимум 3 символа):</label>
            <input type="text" name="login" required>
        </div>
        
        <div class="form-group">
            <label>Пароль (минимум 3 символа):</label>
            <input type="password" name="password" required>
        </div>
        
        <button type="submit">Зарегистрироваться</button>
    </form>
    
    <p class="auth-link">Уже есть аккаунт? <a href="/mysite/login.php">Войдите</a></p>
</div>

<script>
document.getElementById('registerForm').onsubmit = async function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    let response = await fetch('/mysite/api.php?action=register', {
        method: 'POST',
        body: formData
    });
    let result = await response.json();
    let msgDiv = document.getElementById('register-message');
    
    if (result.success) {
        msgDiv.innerHTML = '<p style="color: green;">Регистрация успешна! Теперь можете войти.</p>';
        this.reset();
    } else {
        msgDiv.innerHTML = '<p style="color: red;">' + result.error + '</p>';
    }
};
</script>

</body>
</html>