timeGo = 0;

window.setTimeout('gotoTimer()', 1);

function gotoTimer() {
	timeGo++;
	if(timeGo > 30) {
		document.location = " play.php";
		return;
	}
	document.getElementById('mmmorpg_refrash_timer').innerHTML = 'Обновление через <b>' + (31 - timeGo) + '</b> секунд.';
	window.setTimeout('gotoTimer()', 1000);
}
