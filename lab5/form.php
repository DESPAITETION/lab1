<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Задание 5</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
    if (!empty($_COOKIE['save'])) {
        echo '<div class="message">Результаты сохранены.</div>';
        setcookie('save', '', 100000);
    }
    ?>

    <form action="index.php" method="POST">
        <!-- Защита CSRF -->
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <h2>Форма регистрации</h2>

        <label>ФИО:</label><br />
        <input name="name" placeholder="Введите ФИО" 
               value="<?php echo htmlspecialchars($values['name']); ?>" /> <br />

        <label>Телефон:</label><br />
        <input name="phone" type="tel" placeholder="Введите телефон" 
               value="<?php echo htmlspecialchars($values['phone']); ?>" /> <br />

        <label>E-mail:</label><br />
        <input name="email" type="email" placeholder="Введите e-mail" 
               value="<?php echo htmlspecialchars($values['email']); ?>" /> <br />

        <label>Дата рождения:</label><br />
        <input name="birthdate" type="date" 
               value="<?php echo htmlspecialchars($values['birthdate']); ?>" /> <br />

        <label>Пол:</label><br />
        <input type="radio" name="gender" value="M" <?php if ($values['gender'] == 'M') echo 'checked'; ?> /> Мужской
        <input type="radio" name="gender" value="F" <?php if ($values['gender'] == 'F') echo 'checked'; ?> /> Женский <br />

        <label>Биография:</label><br />
        <textarea name="biography"><?php echo htmlspecialchars($values['biography']); ?></textarea> <br />

        <input type="submit" value="Сохранить" />
    </form>
</body>
</html>