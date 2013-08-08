<? 
$_db_inc_ = true;
include("../config.php");

$login = htmlspecialchars($_POST['m_login']);
$password = htmlspecialchars($_POST['m_password']);

$login_result = mysql_query("SELECT `login` FROM `user` WHERE `login`='$login' AND `password`='".md5($password)."'"); 

if(!mysql_error() && @mysql_num_rows($login_result) == 1) {
  session_set_cookie_params(180000, "/");
  session_start();
  
  $_SESSION["mmmorpglogin"] = $login;
  $_SESSION["mmmorpgpassword"] = md5($password);
	
  /*setcookie('mmmorpglogin', $login, time()+1800);
	setcookie('mmmorpgpassword', md5($password), time()+1800);
  $_COOKIE['mmmorpglogin'] = $login;
  $_COOKIE['mmmorpgpassword'] = md5($password);*/

	header("Location:/play.php"); 
	exit; 
} else if (!mysql_error()) {
	header("Location:/index.php?err=1"); 
} else {
	echo mysql_error(); 
}
?>
