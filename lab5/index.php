<?php
// DB credentials
$user = 'u82369';
$pass_db = '4449825';

session_start();

$values = ['name' => '', 'phone' => '', 'email' => '', 'birthdate' => '', 'gender' => 'M', 'biography' => ''];
$user_langs = [];

try {
    $db = new PDO("mysql:host=localhost;dbname=$user", $user, $pass_db);
} catch (PDOException $e) {
    exit('DB Error: ' . $e->getMessage());
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
    // 1. VALIDATION
    $errors = false;
    if (empty($_POST['name']) || !preg_match('/^[a-zA-Zа-яёА-ЯЁ\s\-]+$/u', $_POST['name'])) {
        setcookie('name_error', '1', time() + 24 * 3600);
        $errors = true;
    }
    if (empty($_POST['phone']) || !preg_match('/^\+?[0-9]+$/', $_POST['phone'])) {
        setcookie('phone_error', '1', time() + 24 * 3600);
        $errors = true;
    }
    if (empty($_POST['languages'])) {
        setcookie('lang_error', '1', time() + 24 * 3600);
        $errors = true;
    }

    if ($errors) {
        header('Location: index.php');
        exit();
    }

    // 2. SAVING
    try {
        if (!empty($_SESSION['login'])) {
            // Update existing user
            $stmt = $db->prepare("UPDATE application SET name = ?, phone = ?, email = ?, birthdate = ?, gender = ?, biography = ? WHERE login = ?");
            $stmt->execute([$_POST['name'], $_POST['phone'], $_POST['email'], $_POST['birthdate'], $_POST['gender'], $_POST['biography'], $_SESSION['login']]);

            $stmt = $db->prepare("SELECT id FROM application WHERE login = ?");
            $stmt->execute([$_SESSION['login']]);
            $user_id = $stmt->fetchColumn();
            
            $db->prepare("DELETE FROM application_languages WHERE application_id = ?")->execute([$user_id]);
        } else {
            // New user registration
            $login = 'user' . rand(1, 10000);
            $pass = substr(md5(uniqid()), 0, 8);
            
            // Сохраняем в куки для авто-входа
            setcookie('login', $login, time() + 3600 * 24 * 365);
            setcookie('pass', $pass, time() + 3600 * 24 * 365);

            $stmt = $db->prepare("INSERT INTO application (name, phone, email, birthdate, gender, biography, login, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_POST['name'], $_POST['phone'], $_POST['email'], $_POST['birthdate'], $_POST['gender'], $_POST['biography'], $login, md5($pass)]);
            $user_id = $db->lastInsertId();
            
            // ВЫВОДИМ ЛОГИН И ПАРОЛЬ (чтобы пользователь их увидел)
            echo "Registration successful!<br>Login: <b>$login</b><br>Password: <b>$pass</b><br>";
            echo "<a href='index.php'>Go to Form</a>";
            
            // Сохраняем языки и останавливаем скрипт, чтобы показать данные
            if (!empty($_POST['languages'])) {
                $stmt = $db->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)");
                foreach ($_POST['languages'] as $lang_id) { $stmt->execute([$user_id, $lang_id]); }
            }
            exit(); 
        }

        // Save languages for updated user
        if (!empty($_POST['languages'])) {
            $stmt = $db->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)");
            foreach ($_POST['languages'] as $lang_id) { $stmt->execute([$user_id, $lang_id]); }
        }

    } catch (PDOException $e) { exit('DB Error: ' . $e->getMessage()); }

    setcookie('save', '1');
    header('Location: index.php');
}
