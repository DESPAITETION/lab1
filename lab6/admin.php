<?php
/**
 * Задача 6. Панель администратора.
 */

$user = 'u82369';
$pass_db = '4449825';

try {
    $db = new PDO("mysql:host=localhost;dbname=$user", $user, $pass_db);
} catch (PDOException $e) {
    exit('DB connection failed: ' . $e->getMessage());
}

// 1. HTTP-авторизация
if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW'])) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="Admin Page"');
    exit('<h1>401 Требуется авторизация</h1>');
}

$stmt = $db->prepare("SELECT password FROM admin_users WHERE login = ?");
$stmt->execute([$_SERVER['PHP_AUTH_USER']]);
$admin_pass_hash = $stmt->fetchColumn();

// Проверка пароля (используем md5, как в твоей базе)
if (!$admin_pass_hash || md5($_SERVER['PHP_AUTH_PW']) !== $admin_pass_hash) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="Admin Page"');
    exit('<h1>401 Неверный логин или пароль</h1>');
}

echo "<h1>Панель администратора</h1>";

// 2. СТАТИСТИКА ПО ЯЗЫКАМ (с именами вместо ID)
echo "<h2>Статистика по языкам</h2>";

// Сопоставление ID и названий
$lang_names = [
    1 => 'Pascal', 
    2 => 'C', 
    3 => 'C++', 
    4 => 'JavaScript', 
    5 => 'PHP', 
    6 => 'Python', 
    7 => 'Java', 
    8 => 'Haskell'
];

$res = $db->query("SELECT language_id, COUNT(*) as count FROM application_languages GROUP BY language_id");

echo "<table border='1'>
        <tr>
            <th>Язык программирования</th>
            <th>Количество любителей</th>
        </tr>";

while ($row = $res->fetch()) {
    $id = $row['language_id'];
    $name = isset($lang_names[$id]) ? $lang_names[$id] : "ID: $id";
    
    echo "<tr>
            <td>" . htmlspecialchars($name) . "</td>
            <td>" . $row['count'] . "</td>
          </tr>";
}
echo "</table>";

// 3. СПИСОК ПОЛЬЗОВАТЕЛЕЙ
echo "<h2>Список всех пользователей</h2>";
$users = $db->query("SELECT * FROM application");

echo "<table border='1'>
    <tr>
        <th>ID</th>
        <th>ФИО</th>
        <th>Телефон</th>
        <th>Email</th>
        <th>Действия</th>
    </tr>";

while ($u = $users->fetch()) {
    echo "<tr>
        <td>{$u['id']}</td>
        <td>" . htmlspecialchars($u['name']) . "</td>
        <td>" . htmlspecialchars($u['phone']) . "</td>
        <td>" . htmlspecialchars($u['email']) . "</td>
        <td>
            <a href='edit.php?id={$u['id']}'>Редактировать</a> | 
            <a href='delete.php?id={$u['id']}' onclick='return confirm(\"Вы уверены?\")' style='color:red;'>Удалить</a>
        </td>
    </tr>";
}
echo "</table>";
?>
