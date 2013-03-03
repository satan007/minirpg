<?

if(!isset($_db_inc_)) {
	echo "File not found.";
	exit;
}

$db_host = 'localhost';
$db_user = 'wordpress';
$db_password = 'wordpress';
$db_database = 'wordpress';

$link = mysql_connect($db_host, $db_user, $db_password);
mysql_select_db($db_database);
mysql_query("SET CHARACTER SET utf8"); 
mysql_query("SET SESSION collation_connection='utf8_general_ci'");
mysql_query("SET NAMES utf8");

?>
