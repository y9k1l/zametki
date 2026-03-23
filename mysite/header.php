<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личная страница</title>
    <link rel="stylesheet" href="/mysite/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Мой сайт</h1>
            <nav>
                <a href="/mysite/index.php">Главная</a> |
                <a href="/mysite/about.php">О себе</a> |
                <?php if (isLoggedIn()): ?>
                    <a href="/mysite/cabinet.php">Кабинет</a> |
                    <a href="/mysite/logout.php">Выход (<?= h($_SESSION['user_login']) ?>)</a>
                <?php else: ?>
                    <a href="/mysite/login.php">Вход</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main>
        <div class="container">