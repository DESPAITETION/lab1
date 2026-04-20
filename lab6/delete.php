<?php
$user = 'u82369';
$pass_db = '4449825';
$db = new PDO("mysql:host=localhost;dbname=$user", $user, $pass_db);

// 1. Простая авторизация для безопасности (как в admin.php)
if (empty($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER'] != 'admin') {
    header('HTTP/1.1 401 Unauthorized');
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // СНАЧАЛА удаляем связанные записи из таблицы языков
    $stmt1 = $db->prepare("DELETE FROM application_languages WHERE application_id = ?");
    $stmt1->execute([$id]);
    
    // ЗАТЕМ удаляем самого пользователя из основной таблицы
    $stmt2 = $db->prepare("DELETE FROM application WHERE id = ?");
    $stmt2->execute([$id]);
}

// Возвращаемся в админку
header('Location: admin.php');
