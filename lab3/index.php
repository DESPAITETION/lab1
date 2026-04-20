<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Анкета</title>

<style>
body{
    font-family: Arial, sans-serif;
    background:#f2f2f2;
}

.container{
    width:500px;
    margin:40px auto;
    background:white;
    padding:25px;
    border-radius:10px;
    box-shadow:0 0 10px rgba(0,0,0,0.1);
}

h2{
    margin-top:0;
}

input[type=text],
input[type=tel],
input[type=email],
input[type=date],
textarea,
select{
    width:100%;
    padding:8px;
    margin:6px 0 12px 0;
    border:1px solid #ccc;
    border-radius:4px;
}

input[type=radio],
input[type=checkbox]{
    width:auto;
}

label{
    display:block;
    margin-bottom:5px;
}

button{
    padding:10px 20px;
    background:#4CAF50;
    color:white;
    border:none;
    border-radius:4px;
    cursor:pointer;
}

button:hover{
    background:#45a049;
}
</style>
</head>

<body>

<div class="container">

<h2>Анкета</h2>

<form action="form.php" method="POST">

<label>ФИО:</label>
<input name="name" type="text" required>

<label>Телефон:</label>
<input name="phone" type="tel" required>

<label>Email:</label>
<input name="email" type="email" required>

<label>Дата рождения:</label>
<input name="birthdate" type="date" required>

<label>Пол:</label>
<label><input type="radio" name="gender" value="male" required> Мужской</label>
<label><input type="radio" name="gender" value="female"> Женский</label>

<label>Любимые языки программирования:</label>

<select name="languages[]" multiple size="6" required>
<option value="1">Pascal</option>
<option value="2">C</option>
<option value="3">C++</option>
<option value="4">JavaScript</option>
<option value="5">PHP</option>
<option value="6">Python</option>
<option value="7">Java</option>
<option value="8">Haskell</option>
<option value="9">Clojure</option>
<option value="10">Prolog</option>
<option value="11">Scala</option>
<option value="12">Go</option>
</select>

<label>Биография:</label>
<textarea name="biography"></textarea>

<label>
<input type="checkbox" name="contract" value="1">
С контрактом ознакомлен
</label>

<br><br>

<button type="submit">Сохранить</button>

</form>

</div>

</body>
</html>
