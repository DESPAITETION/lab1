<?php
session_start();
$user = 'u82369';
$pass_db = '4449825';

// HTTP Basic Auth
if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_USER'] != 'admin' || md5($_SERVER['PHP_AUTH_PW']) != md5('admin_pass')) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="My Site"');
    exit('Доступ запрещен');
}

try {
    $db = new PDO("mysql:host=localhost;dbname=$user", $user, $pass_db);
    
    // Статистика по языкам
    $stats = $db->query("SELECT l.name, COUNT(al.application_id) as count 
                         FROM languages l 
                         LEFT JOIN application_languages al ON l.id = al.language_id 
                         GROUP BY l.id")->fetchAll();

    // Список всех пользователей
    $users = $db->query("SELECT * FROM application")->fetchAll();
} catch (PDOException $e) {
    error_log($e->getMessage());
    exit('Ошибка загрузки данных.');
}
?>

<h2>Панель администратора</h2>

<h3>Статистика по языкам:</h3>
<ul>
    <?php foreach($stats as $s): ?>
        <li><?php echo htmlspecialchars($s['name']) . ": " . $s['count']; ?></li>
    <?php endforeach; ?>
</ul>

<h3>Список пользователей:</h3>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Имя</th>
        <th>Логин</th>
        <th>Действия</th>
    </tr>
    <?php foreach($users as $u): ?>
    <tr>
        <td><?= $u['id'] ?></td>
        <td><?= htmlspecialchars($u['name']) ?></td>
        <td><?= htmlspecialchars($u['login']) ?></td>
        <td>
            <a href="edit.php?id=<?= $u['id'] ?>">Редактировать</a> | 
            <!-- CSRF защита: передаем токен в ссылке -->
            <a href="delete.php?id=<?= $u['id'] ?>&token=<?= $_SESSION['csrf_token'] ?>" 
               onclick="return confirm('Вы уверены?')">Удалить</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>