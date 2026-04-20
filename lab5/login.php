<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();

// Если пользователь уже авторизован, отправляем на форму
if (!empty($_SESSION['login'])) {
  header('Location: ./');
  exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
?>
  <form action="" method="post">
    Логин: <input name="login" /><br />
    Пароль: <input name="pass" type="password" /><br />
    <input type="submit" value="Войти" />
  </form>
<?php
} else {
  $user = 'u82369';
  $pass_db = '4449825';
  
  try {
    $db = new PDO("mysql:host=localhost;dbname=$user", $user, $pass_db);
    $stmt = $db->prepare("SELECT id, password FROM application WHERE login = ?");
    $stmt->execute([$_POST['login']]);
    $row = $stmt->fetch();

    if ($row && md5($_POST['pass']) === $row['password']) {
      $_SESSION['login'] = $_POST['login'];
      $_SESSION['uid'] = $row['id'];
      header('Location: ./');
    } else {
      echo "Неверный логин или пароль.";
    }
  } catch (PDOException $e) { echo 'Ошибка БД: ' . $e->getMessage(); exit(); }
}
