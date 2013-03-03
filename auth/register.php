<?php
$_db_inc_ = true;
include("../db.inc.php");

if(isset($_POST['m_submit'])) {
	if($_POST['m_password'] === $_POST['m_password2']) {
		$_POST['m_login'] = htmlspecialchars($_POST['m_login']);
		$_POST['m_password'] = htmlspecialchars($_POST['m_password']);
		$_POST['m_email'] = htmlspecialchars($_POST['m_email']);
		$_POST['m_name'] = htmlspecialchars($_POST['m_name']);

		$res = mysql_query("SELECT `id` FROM `user` WHERE `login`='{$_POST['m_login']}' OR `name`='{$_POST['m_name']}'");
		echo mysql_error();
		if(@mysql_num_rows($res) < 1) {
			mysql_query("INSERT INTO `user` (`login`, `password`, `email`, `name`, `class`, `level`, `exp`, `hp`) VALUES ('{$_POST['m_login']}', '".md5($_POST['m_password'])."', '{$_POST['m_email']}', '{$_POST['m_name']}', 'a', 0, 0, 99)");
			echo mysql_error();
		
		?>
<br><br>
<center>Вы зарегистрированы. Можете <a href="index.php">войти</a> в игру.</center>
		<?
		exit;
		} else {
			header("Location: index.php?err=4");
			exit;
		}
	} else {
		header("Location: index.php?err=3");
		exit;
	}
}

header("Location: index.php");

?>