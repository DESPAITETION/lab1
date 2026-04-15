<?php
/**
 * Лабораторная работа №4. 
 * Файл должен быть сохранен в кодировке UTF-8.
 */
header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $messages = array();
    if (!empty($_COOKIE['save'])) {
        setcookie('save', '', 100000);
        $messages[] = '<div style="color:green; font-weight:bold;">Результаты успешно сохранены!</div>';
    }

    $errors = array();
    $fields = ['name', 'phone', 'email', 'date', 'gender', 'languages', 'bio', 'agree'];
    foreach ($fields as $field) {
        $errors[$field] = !empty($_COOKIE[$field . '_error']);
    }

    if (array_filter($errors)) {
        foreach ($fields as $field) {
            if ($errors[$field]) {
                setcookie($field . '_error', '', 100000);
                // Этот массив теперь будет отображаться корректно в UTF-8
                $names = [
                    'name' => 'ФИО', 
                    'phone' => 'Телефон', 
                    'email' => 'Email',
                    'date' => 'Дата рождения', 
                    'gender' => 'Пол', 
                    'languages' => 'Языки', 
                    'bio' => 'Биография', 
                    'agree' => 'Согласие'
                ];
                $messages[] = "<div style='color:red;'>Ошибка в поле: " . $names[$field] . "</div>";
            }
        }
    }

    $values = array();
    foreach ($fields as $field) {
        $values[$field] = empty($_COOKIE[$field . '_value']) ? '' : urldecode($_COOKIE[$field . '_value']);
    }
    $values['languages'] = !empty($values['languages']) ? explode(',', $values['languages']) : [];

    include('form.php');
} else {
    $errors = FALSE;

    // Валидация ФИО
    if (empty($_POST['name']) || !preg_match("/^[a-zA-Zа-яА-ЯёЁ\s\-]+$/u", $_POST['name'])) {
        setcookie('name_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('name_value', urlencode($_POST['name']), time() + 30 * 24 * 60 * 60);

    // Валидация Телефона
    if (empty($_POST['phone']) || !preg_match("/^\+?[0-9]{10,15}$/", $_POST['phone'])) {
        setcookie('phone_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('phone_value', urlencode($_POST['phone']), time() + 30 * 24 * 60 * 60);

    // Валидация Email
    if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        setcookie('email_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('email_value', urlencode($_POST['email']), time() + 30 * 24 * 60 * 60);

    // Валидация Даты
    $d = DateTime::createFromFormat('Y-m-d', $_POST['date']);
    if (empty($_POST['date']) || !($d && $d->format('Y-m-d') === $_POST['date'])) {
        setcookie('date_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('date_value', urlencode($_POST['date']), time() + 30 * 24 * 60 * 60);

    // Пол
    if (empty($_POST['gender'])) {
        setcookie('gender_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('gender_value', urlencode($_POST['gender']), time() + 30 * 24 * 60 * 60);

    // Языки
    if (empty($_POST['languages']) || !is_array($_POST['languages'])) {
        setcookie('languages_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    } else {
        setcookie('languages_value', urlencode(implode(',', $_POST['languages'])), time() + 30 * 24 * 60 * 60);
    }

    // Биография (используем strlen для совместимости с сервером)
    if (empty($_POST['bio']) || strlen($_POST['bio']) < 2) {
        setcookie('bio_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('bio_value', urlencode($_POST['bio']), time() + 30 * 24 * 60 * 60);

    // Согласие
    if (empty($_POST['agree'])) {
        setcookie('agree_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('agree_value', urlencode($_POST['agree']), time() + 30 * 24 * 60 * 60);

    if ($errors) {
        header('Location: index.php');
        exit();
    } else {
        setcookie('save', '1');
    }
    header('Location: index.php');
}