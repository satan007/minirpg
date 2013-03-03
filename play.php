<?php include("system.php");?>

<font size="-1">
<center>Зарегистрировано <b><?=$reg_users_count-5;?></b> персонажей. Из них <b><?=(sizeof($userlist))-5;?></b> в сети.<br>
<div id="mmmorpg_refrash_timer"></div></center>
</font>
<br>
<table align="center">
	<tr>
	<td><input type="button" value="Бой" onclick="location.href='play.php'" /></td>
	<td><input type="button" value="Профиль" onclick="location.href='user/info.php'" /></td>
	<td><input type="button" value="Инвентарь" onclick="location.href='user/invent.php'" /></td>
	<td><input type="button" value="Выход" onclick="location.href='auth/logout.php'" /></td>
	</tr></table>
<table width="100%" align="center">
<tr>
	<td valign="top" width="160"><?php include ("user/user.php"); ?></td>
	<td valign="top"><?if($battle_bool != false) {?>
		<!--   BATTLE BEGIN   -->
<center>
<font color="#FF0000">Битва <b><?=$user['name'];?></b> vs. <b><?=$enemy['name'];?></b></font><br>

<form name="battle" action="play.php" method="POST">
<div id="battle_timer">...подождите <b><?$td = ($BATTLE_SECOND - $time_delta); if($td <= 0) $td = $BATTLE_SECOND; echo $td;?></b> секунд...</div>
</form>
</center>
<script>
var timeBattle = <?=$time_delta;?>;

window.setTimeout('battleTimer()', 1);

function battleTimer() {
  //document.getElementById('m_submit').disabled();
	timeBattle++;
	if(timeBattle > <?=$BATTLE_SECOND;?>) {
    //document.getElementById('battle_timer').innerHTML = '...можете смело бить врага...';
		document.getElementById('battle_timer').innerHTML = '<input name="battle_submit" type="submit" value="Ударить">';
		return;
	}
	//document.getElementById('battle_timer').innerHTML = '...подождите  <b>' + (<?=$BATTLE_SECOND;?> - timeBattle + 1) + '</b> секунд...';
	document.getElementById('battle_timer').innerHTML = '<img src="img/loading.gif">';
	window.setTimeout('battleTimer()', 1000);
}
</script>

		<!--   BATTLE END   -->
<?}?>
    <!--   CHAT BEGIN   -->
<center>
<form name="chat" action="play.php" method="POST">
Чат: <input name="chat_msg" type="text" value="" style="width: 300px;"> <input name="chat_submit" type="submit" value="Сказать">
</form>
</center>
    <!--   CHAT END   -->
		<!--   LOG BEGIN   -->
		<b>Лог жизни персонажа</b> <span align="right">[<a href="play.php">обновить</a>] [<a href="stat.php" target="_blank">статистика</a>]</span><br>
<ul>
<?if(isset($fast_msg)) {?><li><?=$fast_msg;?></li><?}?>
<?for($i = 0; $i < sizeof($msg); $i++){?>
<li><?=$msg[$i]['message'];?> <font size="-1" color="#CCCCCC">(<?=date("H:i:s", $msg[$i]['date']);?>)</font></li>
<?}?>
</ul>
		<!--   LOG END   -->
    </td>
    <td valign="top" width="200">
		<!--   USER LIST BEGIN   -->
<center>
<b>Список</b><br>
<?for($i = 0; $i < sizeof($userlist)-1; $i++) {?>
	<?if(!$battle_bool AND $userlist[$i]['battle'] == false AND $user['hp'] > 0 AND ( $userlist[$i]['hp'] == GetHP($userlist[$i]['level']) OR $userlist[$i]['type'] == 'm' )) {?>
		<a href="play.php?action=battle_begin&enemy=<?=$userlist[$i]['id'];?>&<?=rand(0, 10000000);?>"><?=$userlist[$i]['name'];?></a> [<?=$userlist[$i]['level'];?>, <?=$userlist[$i]['hp'];?>]
	<?} else {?>
    <?if(isset($enemy) AND $enemy['id'] == $userlist[$i]['id']) {?>
      <b><?=$enemy['name'];?> [<?=$enemy['level'];?>, <?=$enemy['hp'];?>]</b>
    <?} else {?>
		  <?=$userlist[$i]['name'];?> [<?=$userlist[$i]['level'];?>, <?=$userlist[$i]['hp'];?>]
    <?}?>
	<?}?>
  <font size="-2" color="#FF0000"><?=$userlist[$i]['weapon'];?></font>
  <br>
<?}?>
</center>
		<!--   USER LIST BEGIN   -->
    </td>
  <tr>
</table>
<script>
document.getElementById('chat_msg').focus();
</script>
<script language="JavaScript" src="script.js"></script>
<?php echo $roboniz; ?>
