<?php include ("../system.php");?>

 <!--   USER BEGIN   -->
 <table align="center">
	<tr>
	<td><input type="button" value="Бой" onclick="location.href='../play.php'" /></td>
	<td><input type="button" value="Профиль" onclick="location.href='info.php'" /></td>
	<td><input type="button" value="Инвентарь" onclick="location.href='invent.php'" /></td>
	<td><input type="button" value="Выход" onclick="location.href='../auth/logout.php'" /></td>
	</tr></table>
<table><tr><td><center><b>Персонаж</b></center></td></tr>
<tr>
<td><b>Имя:</b></td> <td><?=$user['name'];?></td></tr>
<tr><td><b>Уровень:</b></td> <td><?=$user['level'];?></td></tr>
<b>Класс:</b> <?=GetClassName($user['class']);?><br>
<b>Здоровье:</b> <font color="#FF0000"><?=$user['hp'];?></font>/<?=GetHP($user['level']);?><br>
<b>Опыт:</b> <?=$user['exp'];?><br>
<b>Победы:</b> <font color="#FF0000"><?=$user['victory_count'];?></font>/<?=($user['battle_count'] - $user['victory_count']);?> (<?=$user['battle_count'];?>)<br>
<?if($user['weapon'] != '') {?>
<font color="#FF0000"><?=$user['weapon'];?></font><br>
<?}?>
<?if($user['quest'] != 0) {?>
<br>Миссия: <b><?=$user['quest_name'];?></b> должен умереть.<br>
<?}?>
		<!--   USER END   -->