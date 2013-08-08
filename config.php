<?
if(!isset($_db_inc_)) {
	echo "File not found.";
	exit;
}

$db_host = 'localhost';
$db_user = '123';
$db_password = '123';
$db_database = '123';

$BATTLE_SECOND = 10; // скорость боя в секундах, чем больше, тем медленнее
$MESSAGE_COUNT = 1000;
$LIFE_KOEF = 2; // скорость регенерации, чем меньше, тем быстрее
$EVENT_KOEF = 30; // частота событий, чем больше, тем реже
$WEAPON_EVENT_KOEF = 7; // частота событий с оружием, чем больше, тем реже, не меньше 10

$game_user = 'user'; //Таблица с пользователями
$game_login = 'login';
$game_email = 'email';
$game_name = 'name'; //
$game_id = 'id';
$game_password = 'password';


$save_msg_time = time();

$link = mysql_connect($db_host, $db_user, $db_password);
mysql_select_db($db_database);
mysql_query("SET CHARACTER SET utf8"); 
mysql_query("SET SESSION collation_connection='utf8_general_ci'");
mysql_query("SET NAMES utf8");
?>