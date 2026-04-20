<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();

$user = 'u82369';
$pass_db = '4449825';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  $messages = array();
  if (!empty($_COOKIE['save'])) {
    setcookie('save', '', 100000);
    $messages[] = 'Результаты сохранены.';
    if (!empty($_COOKIE['pass'])) {
      $messages[] = sprintf('Войдите с логином <strong>%s</strong> и паролем <strong>%s</strong> для редактирования.',
        strip_tags($_COOKIE['login']), strip_tags($_COOKIE['pass']));
    }
  }

  $values = array();
  // Поля в соответствии с твоей таблицей
  $fields = ['name', 'phone', 'email', 'birthdate', 'gender', 'biography'];
  foreach ($fields as $f) { $values[$f] = ''; }

  // Если авторизован — берем данные из БД
  if (!empty($_SESSION['login'])) {
    try {
      $db = new PDO("mysql:host=localhost;dbname=$user", $user, $pass_db);
      $stmt = $db->prepare("SELECT * FROM application WHERE id = ?");
      $stmt->execute([$_SESSION['uid']]);
      $row = $stmt->fetch();
      foreach ($fields as $f) { $values[$f] = strip_tags($row[$f]); }
    } catch (PDOException $e) { echo 'Ошибка: ' . $e->getMessage(); exit(); }
  }

  include('form.php');
} else {
  // Простейшая проверка (добавь сюда свою валидацию из 4 лабы)
  $errors = empty($_POST['name']) || empty($_POST['phone']); 

  if ($errors) {
    header('Location: index.php');
    exit();
  }

  try {
    $db = new PDO("mysql:host=localhost;dbname=$user", $user, $pass_db);
    
    if (!empty($_SESSION['login'])) {
      // Обновление существующего
      $stmt = $db->prepare("UPDATE application SET name = ?, phone = ?, email = ?, birthdate = ?, gender = ?, biography = ? WHERE id = ?");
      $stmt->execute([$_POST['name'], $_POST['phone'], $_POST['email'], $_POST['birthdate'], $_POST['gender'], $_POST['biography'], $_SESSION['uid']]);
    } else {
      // Новый пользователь
      $login = 'user' . rand(1, 1000);
      $pass = substr(md5(uniqid()), 0, 8);
      setcookie('login', $login);
      setcookie('pass', $pass);

      $stmt = $db->prepare("INSERT INTO application (name, phone, email, birthdate, gender, biography, login, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
      $stmt->execute([$_POST['name'], $_POST['phone'], $_POST['email'], $_POST['birthdate'], $_POST['gender'], $_POST['biography'], $login, md5($pass)]);
    }
  } catch (PDOException $e) { echo 'Ошибка БД: ' . $e->getMessage(); exit(); }

  setcookie('save', '1');
  header('Location: index.php');
}
