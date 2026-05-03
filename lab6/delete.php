<?php
session_start();
$user = 'u82369';
$pass_db = '4449825';

try {
    $db = new PDO("mysql:host=localhost;dbname=$user", $user, $pass_db);
} catch (PDOException $e) {
    exit('Техническая ошибка.');
}

if (empty($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER'] != 'admin') {
    header('HTTP/1.1 401 Unauthorized');
    exit;
}

// ПРОВЕРКА CSRF-ТОКЕНА В GET-ЗАПРОСЕ
if (!isset($_GET['token']) || $_GET['token'] !== $_SESSION['csrf_token']) {
    exit('Ошибка: неверный CSRF-токен!');
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt1 = $db->prepare("DELETE FROM application_languages WHERE application_id = ?");
    $stmt1->execute([$id]);
    
    $stmt2 = $db->prepare("DELETE FROM application WHERE id = ?");
    $stmt2->execute([$id]);
}

header('Location: admin.php');