<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();

if (!empty($_SESSION['login'])) {
    header('Location: ./');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
?>
<form action="" method="post">
  <input name="login" placeholder="Логин" />
  <input name="pass" placeholder="Пароль" />
  <input type="submit" value="Войти" />
</form>
<?php
} else {
    $user = 'u82369';
    $pass_db = '4449825';
    
    try {
        $db = new PDO("mysql:host=localhost;dbname=$user", $user, $pass_db, [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERR_MODE => PDO::ERR_MODE_EXCEPTION
        ]);

        // Защита SQLi: подготовленный запрос
        $stmt = $db->prepare("SELECT id, password FROM application WHERE login = ?");
        $stmt->execute([$_POST['login']]);
        $row = $stmt->fetch();

        if ($row && md5($_POST['pass']) == $row['password']) {
            $_SESSION['login'] = $_POST['login'];
            $_SESSION['uid'] = $row['id'];
            header('Location: ./');
        } else {
            echo "Неверный логин или пароль.";
        }
    } catch (PDOException $e) {
        // Защита Info Disclosure: логируем в файл, пользователю — общую фразу
        error_log("Login error: " . $e->getMessage());
        exit('Внутренняя ошибка сервера. Попробуйте позже.');
    }
}