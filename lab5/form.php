<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Форма регистрации</title>
    <style>
        .error { border: 2px solid red; }
        .error-msg { color: red; font-size: 0.8em; }
    </style>
</head>
<body>

<?php 
if (!empty($_COOKIE['save'])) { 
    echo '<p style="color:green;">Данные успешно сохранены!</p>'; 
    setcookie('save', '', 1); 
} 
?>

<form action="" method="POST">
    ФИО:<br />
    <input name="name" 
           value="<?php echo htmlspecialchars($values['name']); ?>" 
           placeholder="Иванов Иван"
           pattern="^[a-zA-Zа-яёА-ЯЁ\s\-]+$"
           class="<?php echo !empty($_COOKIE['name_error']) ? 'error' : ''; ?>" required />
    <?php if(!empty($_COOKIE['name_error'])) { echo '<span class="error-msg">Используйте только буквы</span>'; setcookie('name_error', '', 1); } ?>
    <br />

    Телефон:<br />
    <input name="phone" 
           value="<?php echo htmlspecialchars($values['phone']); ?>" 
           placeholder="+79991234567"
           class="<?php echo !empty($_COOKIE['phone_error']) ? 'error' : ''; ?>" required />
    <?php if(!empty($_COOKIE['phone_error'])) { echo '<span class="error-msg">Неверный формат телефона</span>'; setcookie('phone_error', '', 1); } ?>
    <br />

    E-mail:<br />
    <input name="email" 
           type="email"
           value="<?php echo htmlspecialchars($values['email']); ?>" 
           required /><br />

    Дата рождения:<br />
    <input name="birthdate" type="date" value="<?php echo $values['birthdate']; ?>" required /><br />

    Пол:
    <input type="radio" name="gender" value="M" <?php if ($values['gender'] == 'M') echo 'checked'; ?>> Муж
    <input type="radio" name="gender" value="F" <?php if ($values['gender'] == 'F') echo 'checked'; ?>> Жен <br />

    Любимые языки программирования:<br />
    <select name="languages[]" multiple="multiple" size="8" required>
        <?php
        $langs = [1 => 'Pascal', 2 => 'C', 3 => 'C++', 4 => 'JavaScript', 5 => 'PHP', 6 => 'Python', 7 => 'Java', 8 => 'Haskell'];
        foreach ($langs as $id => $name) {
            $selected = (isset($user_langs) && in_array($id, $user_langs)) ? 'selected' : '';
            echo "<option value='$id' $selected>$name</option>";
        }
        ?>
    </select><br />

    Биография:<br />
    <textarea name="biography" required><?php echo htmlspecialchars($values['biography']); ?></textarea><br />

    <input type="submit" value="Отправить" />

    <?php if(!empty($_SESSION['login'])): ?>
        <br><br><a href="login.php">Выйти из системы</a>
    <?php else: ?>
        <br><br><a href="login.php">Войти</a>
    <?php endif; ?>
</form>

</body>
</html>
