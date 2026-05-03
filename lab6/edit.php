<?php
session_start();
$user = 'u82369'; $pass_db = '4449825';
$db = new PDO("mysql:host=localhost;dbname=$user", $user, $pass_db);

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $stmt = $db->prepare("SELECT * FROM application WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $v = $stmt->fetch();
?>
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($v['id']); ?>">
    
    Имя: <input name="name" value="<?php echo htmlspecialchars($v['name']); ?>"><br>
    Био: <textarea name="biography"><?php echo htmlspecialchars($v['biography']); ?></textarea><br>
    
    <input type="submit" value="Сохранить">
</form>
<?php
} else {
    // Проверка CSRF перед сохранением правок
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) { die('CSRF Token Error'); }

    $db->prepare("UPDATE application SET name = ?, biography = ? WHERE id = ?")
       ->execute([$_POST['name'], $_POST['biography'], $_POST['id']]);
    header('Location: admin.php');
}