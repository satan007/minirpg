<?php

session_set_cookie_params(180000, "/");
session_start();

/*if(!isset($_COOKIE['mmmorpglogin']) or !isset($_COOKIE['mmmorpgpassword'])) {
	header("Location:  index.php?err=2");
	exit;
}*/

if(!isset($_SESSION['mmmorpglogin']) or !isset($_SESSION['mmmorpgpassword'])) {
	header("Location:  ../index.php");
	exit;
}

$login = ''; 
$password = '';

/*setcookie('mmmorpglogin', $login, 0);
setcookie('mmmorpgpassword', $password, 0); */

$_SESSION["mmmorpglogin"] = '';
$_SESSION["mmmorpgpassword"] = '';
session_destroy();

header("Location:  ../index.php");

?>
