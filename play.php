<?php include("system.php");?>

<font size="-1">
<center>Зарегистрировано <b><?=$reg_users_count-5;?></b> персонажей. Из них <b><?=(sizeof($userlist))-5;?></b> в сети.<br>
<div id="mmmorpg_refrash_timer"></div></center>
</font>
<br>


<? include ("style/play.html"); ?>
<script>
document.getElementById('chat_msg').focus();
</script>
<script language="JavaScript" src="script.js"></script>
