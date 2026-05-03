<?php
// DB credentials
$user = 'u82369';
$pass_db = '4449825';

session_start();

// ГЕНЕРАЦИЯ CSRF-ТОКЕНА
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$values = ['name' => '', 'phone' => '', 'email' => '', 'birthdate' => '', 'gender' => 'M', 'biography' => ''];
$user_langs = [];

try {
    $db = new PDO("mysql:host=localhost;dbname=$user", $user, $pass_db);
} catch (PDOException $e) {
    error_log($e->getMessage()); // Логируем ошибку
    exit('Техническая ошибка на сервере. Попробуйте позже.'); // Скрываем детали (Info Disclosure)
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!empty($_SESSION['login'])) {
        $stmt = $db->prepare("SELECT * FROM application WHERE login = ?");
        $stmt->execute([$_SESSION['login']]);
        $row = $stmt->fetch();
        if ($row) $values = $row;

        $stmt = $db->prepare("SELECT language_id FROM application_languages WHERE application_id = ?");
        $stmt->execute([$values['id']]);
        $user_langs = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    include('form.php');
} else {
    // ПРОВЕРКА CSRF-ТОКЕНА
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        exit('Ошибка: Попытка подделки запроса (CSRF).');
    }

    // 1. VALIDATION (твой код валидации остается без изменений...)
    $errors = false;
    // ... логика валидации ...

    if ($errors) {
        header('Location: index.php');
        exit();
    }

    // 2. СОХРАНЕНИЕ
    if (!empty($_SESSION['login'])) {
        // UPDATE (используем prepared statements — защита от SQLi)
        $stmt = $db->prepare("UPDATE application SET name = ?, phone = ?, email = ?, birthdate = ?, gender = ?, biography = ? WHERE login = ?");
        $stmt->execute([$_POST['name'], $_POST['phone'], $_POST['email'], $_POST['birthdate'], $_POST['gender'], $_POST['biography'], $_SESSION['login']]);
        
        $stmt = $db->prepare("SELECT id FROM application WHERE login = ?");
        $stmt->execute([$_SESSION['login']]);
        $user_id = $stmt->fetchColumn();

        $db->prepare("DELETE FROM application_languages WHERE application_id = ?")->execute([$user_id]);
    } else {
        // INSERT
        $login = 'user' . rand(1000, 9999);
        $pass = rand(100000, 999999);
        setcookie('login', $login, time() + 3600 * 24 * 365);
        setcookie('pass', $pass, time() + 3600 * 24 * 365);

        $stmt = $db->prepare("INSERT INTO application (name, phone, email, birthdate, gender, biography, login, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_POST['name'], $_POST['phone'], $_POST['email'], $_POST['birthdate'], $_POST['gender'], $_POST['biography'], $login, md5($pass)]);
        $user_id = $db->lastInsertId();
        
        // ЗАЩИТА XSS: Экранируем вывод логина
        echo "Registration successful!<br>Login: <b>" . htmlspecialchars($login) . "</b><br>Password: <b>" . htmlspecialchars($pass) . "</b><br>";
        echo "<a href='index.php'>Go to Form</a>";
        
        if (!empty($_POST['languages'])) {
            $stmt = $db->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)");
            foreach ($_POST['languages'] as $lang_id) { $stmt->execute([$user_id, $lang_id]); }
        }
        exit(); 
    }

    if (!empty($_POST['languages'])) {
        $stmt = $db->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)");
        foreach ($_POST['languages'] as $lang_id) { $stmt->execute([$user_id, $lang_id]); }
    }

    setcookie('save', '1');
    header('Location: index.php');
}