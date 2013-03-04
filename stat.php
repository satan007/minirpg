<?php
$_db_inc_ = true;
include("config.php");
include("system.php");



$userlist_result = mysql_query("SELECT * FROM `user` WHERE 1=1 ORDER BY `victory_count` DESC");
echo mysql_error();
$count = 0;


while($user = mysql_fetch_assoc($userlist_result)) {
	$userlist[$count] = $user;
	$count++;
}


include("style/stat.html");
?>
