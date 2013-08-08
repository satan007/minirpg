<?php
session_set_cookie_params(180000, "/");
session_start();

/*if(isset($_COOKIE['mmmorpglogin']) or isset($_COOKIE['mmmorpgpassword'])) {
	header("Location:  play.php");
	exit;
}
*/
if(isset($_SESSION['mmmorpglogin']) or isset($_SESSION['mmmorpgpassword'])) {
	header("Location:  ../play.php");
	exit;
}

$msg = '';
$msg2 = '';
if(isset($_GET['err']))	$err = (int)$_GET['err']; else $err = 0;
if($err == 1) {$msg = "Не правильный логин или пароль.";}
if($err == 2) {$msg = "Соединение разорвано.";}
if($err == 3) {$msg2 = "Пароли не совпадают.";}
if($err == 4) {$msg2 = "Имя или логин заняты.";}
if($err == 5) {$msg = "Косяк с количеством пользователей.";}

echo $roboverh;
?>

<center><h3>Вход в игру</h3></center>
<center><font color="#FF0000"><?=$msg;?></font></center>
<table width="160" align="center">
<tr>
<td>
<form action="auth.php" method="POST">
	Логин:<br>
	<input name="m_login" type="text" style="width: 160px;"><br>
	Пароль:<br>
	<input name="m_password" type="password" style="width: 160px;"><br>
	<input name="m_submit" type="submit" value="Войти">
</form>
</td>
</tr>
</table>

<br><br>
<center><h3>Регистрация</h3></center>
<center><font color="#FF0000"><?=$msg2;?></font></center>
<table width="160" align="center">
<tr>
<td>
<form action="register.php" method="POST">
	Логин:<br>
	<input name="m_login" type="text" style="width: 160px;"><br>
	Пароль:<br>
	<input name="m_password" type="password" style="width: 160px;"><br>
	Повторите пароль:<br>
	<input name="m_password2" type="password" style="width: 160px;"><br>
	E-mail:<br>
	<input name="m_email" type="text" style="width: 160px;"><br>
	Имя персонажа:<br>
	<input name="m_name" type="text" style="width: 160px;"><br>
	<input name="m_submit" type="submit" value="Регистрация">
</form>
</td>
</tr>
</table>
<br>
<script>
document.getElementById('m_login').focus();
</script>