<?php

ini_set('display_errors',1);
error_reporting(E_ALL);

$user = 'u82369';
$pass = '4449825';
$db = 'u82369';

try{
    $pdo = new PDO("mysql:host=localhost;dbname=$db;charset=utf8", $user, $pass);
}catch(PDOException $e){
    die("Ошибка подключения к БД");
}

/* Получение данных */

$name = $_POST['name'] ?? '';
$phone = $_POST['phone'] ?? '';
$email = $_POST['email'] ?? '';
$birthdate = $_POST['birthdate'] ?? '';
$gender = $_POST['gender'] ?? '';
$biography = $_POST['biography'] ?? '';
$languages = $_POST['languages'] ?? [];
$contract = isset($_POST['contract']) ? 1 : 0;


/* Валидация */

if(!preg_match("/^[a-zA-Zа-яА-Я ]{1,150}$/u",$name)){
    die("Ошибка: ФИО должно содержать только буквы и пробелы.");
}

if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
    die("Ошибка: некорректный email.");
}

if(empty($languages)){
    die("Ошибка: выберите хотя бы один язык.");
}

if($gender!='male' && $gender!='female'){
    die("Ошибка: выберите пол.");
}


/* Запись заявки */

$stmt = $pdo->prepare("INSERT INTO application
(name, phone, email, birthdate, gender, biography, contract)
VALUES (?, ?, ?, ?, ?, ?, ?)");

$stmt->execute([
$name,
$phone,
$email,
$birthdate,
$gender,
$biography,
$contract
]);

$app_id = $pdo->lastInsertId();


/* Запись языков */

$stmt = $pdo->prepare("INSERT INTO application_languages
(application_id, language_id)
VALUES (?, ?)");

foreach($languages as $lang){
    $stmt->execute([$app_id,$lang]);
}

echo "<h2>Данные успешно сохранены</h2>";

?>
