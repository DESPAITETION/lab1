<?php
$user = 'u82369'; $pass_db = '4449825';
$db = new PDO("mysql:host=localhost;dbname=$user", $user, $pass_db);

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $stmt = $db->prepare("SELECT * FROM application WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $v = $stmt->fetch();
?>
<form method="POST">
    <input type="hidden" name="id" value="<?php echo $v['id']; ?>">
    Имя: <input name="name" value="<?php echo $v['name']; ?>"><br>
    Био: <textarea name="biography"><?php echo $v['biography']; ?></textarea><br>
    <input type="submit" value="Сохранить">
</form>
<?php
} else {
    $db->prepare("UPDATE application SET name = ?, biography = ? WHERE id = ?")
       ->execute([$_POST['name'], $_POST['biography'], $_POST['id']]);
    header('Location: admin.php');
}
