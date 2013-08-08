<?php
$_db_inc_ = true;

session_set_cookie_params(180000, "/");
session_start();

/*if(isset($_SESSION['mmmorpglogin']) or isset($_SESSION['mmmorpgpassword'])) {
	header("Location:  play.php");
	exit;
}*/
$msg = '';
$msg2 = '';
if(isset($_GET['err']))	$err = (int)$_GET['err']; else $err = 0;
if($err == 1) {$msg = "Не правильный логин или пароль.";}
if($err == 2) {$msg = "Соединение разорвано.";}
if($err == 3) {$msg2 = "Пароли не совпадают.";}
if($err == 4) {$msg2 = "Имя или логин заняты.";}
if($err == 5) {$msg = "Косяк с количеством пользователей.";}
include ("style/index.html");


 ?>
