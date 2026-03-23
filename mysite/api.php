<?php
require_once 'functions.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($action === 'register') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    if (strlen($login) < 3 || strlen($password) < 3) {
        echo json_encode(['success' => false, 'error' => 'Логин и пароль не менее 3 символов']);
        exit;
    }
    $userId = createUser($login, $password);
    if ($userId === false) {
        echo json_encode(['success' => false, 'error' => 'Пользователь уже существует']);
    } else {
        echo json_encode(['success' => true]);
    }
    exit;
}

if ($action === 'login') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $user = getUserByLogin($login);
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_login'] = $user['login'];
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Неверный логин или пароль']);
    }
    exit;
}

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Не авторизован']);
    exit;
}

$userId = $_SESSION['user_id'];

if ($action === 'list') {
    $notes = getUserNotes($userId);
    if (empty($notes)) {
        echo '<p class="empty-notes">Нет заметок. Создайте первую!</p>';
    } else {
        foreach ($notes as $note) {
            ?>
            <div class="note-card" data-id="<?= $note['id'] ?>">
                <h3><?= h($note['title']) ?></h3>
                <p><?= nl2br(h($note['content'])) ?></p>
                <small><?= h($note['created_at']) ?></small>
            </div>
            <?php
        }
    }
    exit;
}

if ($action === 'add') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    
    if (empty($title) || empty($content)) {
        echo json_encode(['success' => false, 'error' => 'Заполните поля']);
        exit;
    }
    
    if (mb_strlen($title) > 50) {
        echo json_encode(['success' => false, 'error' => 'Заголовок не более 50 символов']);
        exit;
    }
    
    if (mb_strlen($content) > 2000) {
        echo json_encode(['success' => false, 'error' => 'Содержание не более 2000 символов']);
        exit;
    }
    
    addNote($userId, $title, $content);
    echo json_encode(['success' => true]);
    exit;
}

if ($action === 'delete') {
    $noteId = (int)($_POST['id'] ?? 0);
    if (deleteNote($noteId, $userId)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Ошибка удаления']);
    }
    exit;
}

if ($action === 'update') {
    $noteId = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    
    if (mb_strlen($title) > 50) {
        echo json_encode(['success' => false, 'error' => 'Заголовок не более 50 символов']);
        exit;
    }
    
    if (mb_strlen($content) > 2000) {
        echo json_encode(['success' => false, 'error' => 'Содержание не более 2000 символов']);
        exit;
    }
    
    if (updateNote($noteId, $userId, $title, $content)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Ошибка обновления']);
    }
    exit;
}

if ($action === 'update_about') {
    $about = trim($_POST['about'] ?? '');
    
    if (mb_strlen($about) > 1000) {
        echo json_encode(['success' => false, 'error' => 'Не более 1000 символов']);
        exit;
    }
    
    $users = readJSON('users.json');
    
    foreach ($users as &$user) {
        if ($user['id'] == $_SESSION['user_id']) {
            $user['about'] = $about;
            writeJSON('users.json', $users);
            echo json_encode(['success' => true]);
            exit;
        }
    }
    
    echo json_encode(['success' => false, 'error' => 'Пользователь не найден']);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Неверное действие']);