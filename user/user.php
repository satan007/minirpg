    <!--   USER BEGIN   -->
<center><b>Персонаж</b></center>
<b>Имя:</b> <?=$user['name'];?><br>
<b>Уровень:</b> <?=$user['level'];?><br>
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