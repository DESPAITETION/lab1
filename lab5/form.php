<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Форма лабораторной 4</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 20px auto; }
        .error-field { border: 2px solid red; background-color: #fff4f4; }
        .msg-box { border: 1px solid #ccc; padding: 10px; margin-bottom: 20px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input[type="text"], input[type="date"], textarea, select { width: 100%; padding: 5px; }
    </style>
</head>
<body>

    <?php if (!empty($messages)): ?>
        <div class="msg-box"><?php foreach ($messages as $m) echo $m; ?></div>
    <?php endif; ?>

    <form action="index.php" method="POST">
        <label>ФИО:</label>
        <input name="name" value="<?= htmlspecialchars($values['name']) ?>" class="<?= $errors['name'] ? 'error-field' : '' ?>">

        <label>Телефон:</label>
        <input name="phone" value="<?= htmlspecialchars($values['phone']) ?>" class="<?= $errors['phone'] ? 'error-field' : '' ?>">

        <label>E-mail:</label>
        <input name="email" value="<?= htmlspecialchars($values['email']) ?>" class="<?= $errors['email'] ? 'error-field' : '' ?>">

        <label>Дата рождения:</label>
        <input type="date" name="date" value="<?= htmlspecialchars($values['date']) ?>" class="<?= $errors['date'] ? 'error-field' : '' ?>">

        <label>Пол:</label>
        <input type="radio" name="gender" value="m" <?= $values['gender'] == 'm' ? 'checked' : '' ?>> Муж
        <input type="radio" name="gender" value="f" <?= $values['gender'] == 'f' ? 'checked' : '' ?>> Жен

        <label>Любимый язык программирования:</label>
        <select name="languages[]" multiple class="<?= $errors['languages'] ? 'error-field' : '' ?>">
            <option value="cpp" <?= in_array('cpp', $values['languages']) ? 'selected' : '' ?>>C++</option>
            <option value="php" <?= in_array('php', $values['languages']) ? 'selected' : '' ?>>PHP</option>
            <option value="python" <?= in_array('python', $values['languages']) ? 'selected' : '' ?>>Python</option>
        </select>

        <label>Биография:</label>
        <textarea name="bio" class="<?= $errors['bio'] ? 'error-field' : '' ?>"><?= htmlspecialchars($values['bio']) ?></textarea>

        <div style="margin-top:10px;">
            <input type="checkbox" name="agree" <?= $values['agree'] == 'on' ? 'checked' : '' ?>> С контрактом ознакомлен
        </div>

        <input type="submit" value="Отправить" style="margin-top:15px; padding: 10px 20px;">
    </form>
</body>
</html>