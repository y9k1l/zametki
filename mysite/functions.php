<?php
session_start();
define('ROOT_DIR', __DIR__);

function readJSON($filename) {
    $path = ROOT_DIR . '/data/' . $filename;
    if (!file_exists($path)) return [];
    return json_decode(file_get_contents($path), true) ?? [];
}

function writeJSON($filename, $data) {
    $path = ROOT_DIR . '/data/' . $filename;
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));
}

function getUserByLogin($login) {
    foreach (readJSON('users.json') as $user) {
        if ($user['login'] === $login) return $user;
    }
    return null;
}

function getUserById($id) {
    foreach (readJSON('users.json') as $user) {
        if ($user['id'] == $id) return $user;
    }
    return null;
}

function createUser($login, $password) {
    $users = readJSON('users.json');
    if (getUserByLogin($login)) return false;
    $newId = count($users) > 0 ? max(array_column($users, 'id')) + 1 : 1;
    $users[] = [
        'id' => $newId,
        'login' => $login,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'created_at' => date('Y-m-d H:i:s')
    ];
    writeJSON('users.json', $users);
    return $newId;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: /mysite/login.php');
        exit;
    }
}

function getUserNotes($userId) {
    $result = [];
    foreach (readJSON('notes.json') as $note) {
        if ($note['user_id'] == $userId) $result[] = $note;
    }
    usort($result, fn($a, $b) => strtotime($b['created_at']) - strtotime($a['created_at']));
    return $result;
}

function addNote($userId, $title, $content) {
    $notes = readJSON('notes.json');
    $newId = count($notes) > 0 ? max(array_column($notes, 'id')) + 1 : 1;
    $notes[] = [
        'id' => $newId,
        'user_id' => $userId,
        'title' => trim($title),
        'content' => trim($content),
        'created_at' => date('Y-m-d H:i:s')
    ];
    writeJSON('notes.json', $notes);
    return $newId;
}

function updateNote($noteId, $userId, $title, $content) {
    $notes = readJSON('notes.json');
    foreach ($notes as &$note) {
        if ($note['id'] == $noteId && $note['user_id'] == $userId) {
            $note['title'] = trim($title);
            $note['content'] = trim($content);
            writeJSON('notes.json', $notes);
            return true;
        }
    }
    return false;
}

function deleteNote($noteId, $userId) {
    $notes = readJSON('notes.json');
    foreach ($notes as $key => $note) {
        if ($note['id'] == $noteId && $note['user_id'] == $userId) {
            unset($notes[$key]);
            writeJSON('notes.json', array_values($notes));
            return true;
        }
    }
    return false;
}

function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>