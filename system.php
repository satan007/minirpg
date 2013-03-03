<?php

session_set_cookie_params(180000, "/");
session_start();

$_db_inc_ = true;
include("db.inc.php");
include("robo1.php");
include("robo2.php");

// config
$BATTLE_SECOND = 10; // скорость боя в секундах, чем бальше, тем медленнее
$MESSAGE_COUNT = 15;
$LIFE_KOEF = 20; // скорость регенерации, чем меньше, тем быстрее
$EVENT_KOEF = 30; // частота событий, чем больше, тем реже
$WEAPON_EVENT_KOEF = 70; // частота событий с оружием, чем больше, тем реже, не меньше 10

$save_msg_time = time();


/*if(!isset($_COOKIE['mmmorpglogin']) or !isset($_COOKIE['mmmorpgpassword'])) {
  header("Location:  index.php?err=2");
	exit;
}

$login = $_COOKIE['mmmorpglogin'];
$password = $_COOKIE['mmmorpgpassword'];*/

if(!isset($_SESSION['mmmorpglogin']) or !isset($_SESSION['mmmorpgpassword'])) {
  header("Location:  index.php?err=2");
	exit;
}
$login = $_SESSION['mmmorpglogin'];
$password = $_SESSION['mmmorpgpassword'];
$login_result = mysql_query("SELECT * FROM `user` WHERE `login`='$login' AND `password`='".$password."' AND `type`='p'");

if(!mysql_error() && @mysql_num_rows($login_result) != 1) { 
	header("Location:  index.php?err=5");
	exit; 
} else {
	echo mysql_error();
}

setcookie('mmmorpglogin', $login, time()+1800); 
setcookie('mmmorpgpassword', $password, time()+1800); 
$user = mysql_fetch_assoc($login_result);

if(isset($_POST['chat_msg'])) {
  SaveMsg($user['id'], 'c', "<b>{$user['name']}</b>: ".htmlspecialchars($_POST['chat_msg']));
}


// user list
$userlist_result = mysql_query("SELECT `id` FROM `user`");
$reg_users_count = @mysql_num_rows($userlist_result);
if(!$reg_users_count) $reg_users_count = 0;
$userlist_result = mysql_query("SELECT * FROM `user` WHERE `id`<>{$user['id']} AND ((".time()."-`last_update` < 300) OR `type`='m') ORDER BY `level`, `hp`");
echo mysql_error();
$online_users_count = @mysql_num_rows($userlist_result);
if(!$online_users_count) $online_users_count = 0;
$userlist = array();
$i = 0;
while($userlist[$i] = mysql_fetch_assoc($userlist_result)) {
	if($userlist[$i]['id'] == $user['quest']) $user['quest_name'] = $userlist[$i]['name'];
	$i++;
}

// battle

$battle_bool = false;

// message delete
mysql_query("DELETE FROM `msg` WHERE (".time()."-`date` > 240 AND `type`='b') OR (".time()."-`date` > 480 AND `type`='e') OR (".time()."-`date` > 1200)");
echo mysql_error();

// battle delete
mysql_query("DELETE FROM `battle` WHERE (".time()."-`date` > 600)");
echo mysql_error();

if($user['hp'] == 0) {
	mysql_query("DELETE FROM `battle` WHERE `user_id`={$user['id']}");
} else {

  // actions
  if(!isset($_GET['action'])) $action = 'none'; else $action = $_GET['action'];
  switch($action) {
  	case 'battle_begin':
  		$enemy_id = $_GET['enemy'];
  		$res = mysql_query("SELECT `id` FROM `battle` WHERE `user_id`={$user['id']}");
  		if(!mysql_error() && @mysql_num_rows($res) < 1) {
    		$res = mysql_query("SELECT `id` FROM `battle` WHERE `user_id`={$enemy_id} AND `enemy_id`<>{$user['id']}");
    		if(!mysql_error() && @mysql_num_rows($res) < 1) {
      		$enemy_result = mysql_query("SELECT * FROM `user` WHERE `id`={$enemy_id}");
      		if(!mysql_error() && @mysql_num_rows($enemy_result) > 0) { 
          	$enemy = mysql_fetch_assoc($enemy_result);
          	mysql_query("INSERT INTO `battle` (`user_id`, `enemy_id`, `date`) VALUES ({$user['id']}, {$enemy_id}, ".(time() - $BATTLE_SECOND / 2).")");
          	if($enemy['type'] == 'm') {
              mysql_query("INSERT INTO `battle` (`user_id`, `enemy_id`, `date`) VALUES ({$enemy_id}, {$user['id']}, ".(time() - $BATTLE_SECOND / 2).")");
            }
          	if(mysql_error()) echo "#1. ".mysql_error();
    				$battle_bool = true;
            mysql_query("UPDATE `user` SET `last_update`=".time()." WHERE `id`={$user['id']} LIMIT 1");
          } else {
          	echo mysql_error();
          }
    		} else {
      		echo mysql_error();
    		}
    	} else {
    		echo mysql_error();
  		}
  	break;
  }

}

$time_delta = 0;

$res = mysql_query("SELECT * FROM `battle` WHERE `user_id`={$user['id']}");
if(!mysql_error() && @mysql_num_rows($res) > 0) {
		$battle = mysql_fetch_assoc($res);
		$enemy_id = $battle['enemy_id'];
		$time_delta = time() - $battle['date'];
  	$res = mysql_query("SELECT `id` FROM `battle` WHERE `user_id`={$enemy_id} AND `enemy_id`<>{$user['id']}");
  	if(!mysql_error() && @mysql_num_rows($res) < 1) {
  		$enemy_result = mysql_query("SELECT * FROM `user` WHERE `id`={$enemy_id}");
    		if(!mysql_error() && @mysql_num_rows($enemy_result) == 1) { 
        	$enemy = mysql_fetch_assoc($enemy_result);
        	
        	if($enemy['hp'] > 0) {
     	      $battle_bool = true;
          	if($time_delta >= $BATTLE_SECOND) {
            	if(isset($_POST['battle_submit'])) {
              	$time_delta = 0;
              	mysql_query("UPDATE `battle` SET `date`=".time()." WHERE `user_id`={$user['id']}");
            		if($enemy['type'] == 'm') {
                  mysql_query("UPDATE `battle` SET `date`=".time()." WHERE `user_id`={$user['id']}");
                }
                BattleEvents(&$user, &$enemy);
                Battle($user, $enemy);
                if($enemy['type'] == 'm' AND $enemy['hp'] > 0) {
                  BattleEvents(&$enemy, &$user);
                  Battle(&$enemy, &$user);
                }
                if($enemy['type'] == 'm' AND $enemy['hp'] <= 0) {
                  $e_exp = 10000;
                  if(rand(0, 10) < 2) $e_exp = 37000;
                  else if(rand(0, 10) < 5) $e_exp = 97000;
                  $e_exp = rand(0, $e_exp);
                  $e_level = GetLevel($e_exp);
                  $e_hp = GetHP($e_level);
                  mysql_query("UPDATE `user` SET `name`='".GenName(3, 5)."', `exp`=$e_exp, `level`=$e_level, `hp`=$e_hp, `weapon`='".GetWeapon()."', `battle_count`=0, `victory_count`=0 WHERE `id`={$enemy['id']} LIMIT 1");
                  echo mysql_error();
                }
                if($user['hp'] <= 0 OR $enemy['hp'] <= 0) {
                  $battle_bool = false;
                  mysql_query("DELETE FROM `battle` WHERE `user_id`={$enemy['id']} OR `user_id`={$user['id']} OR `enemy_id`={$enemy['id']} OR `enemy_id`={$user['id']}");
                  echo mysql_error();
                  if($user['quest'] == 0) QuestEvent(&$user);
                  //header("Location:  play.php");
                } 	
        			}
        		} // time delta
    			} else {
						$battle_bool = false;
					}
        } else {
        	echo mysql_error();
        }
  	} else {
    		echo mysql_error();
  	}	
} else if(mysql_error()) {
  echo mysql_error();
} else {
  $r_hp = rand(0, (time() - $user['last_update']) / $LIFE_KOEF);
  if($r_hp > GetHP($user['level']) - $user['hp']) $r_hp = GetHP($user['level']) - $user['hp'];
  if($r_hp > 0 AND $user['hp'] < GetHP($user['level'])) {
    $fast_msg = "Вы восстановили <b>{$r_hp}</b> жизней.";
  	$user['hp'] += $r_hp;
    mysql_query("UPDATE `user` SET `hp`={$user['hp']}, `last_update`=".time()." WHERE `id`={$user['id']} LIMIT 1");
  }
}


// user list continue
$i = 0;
while($userlist[$i]) {
	$res = mysql_query("SELECT `id` FROM `battle` WHERE `user_id`={$userlist[$i]['id']} AND `enemy_id`<>{$user['id']}");
	$userlist[$i]['battle'] = false;
	if(!mysql_error() && @mysql_num_rows($res) > 0) { 
  	$userlist[$i]['battle'] = true;
  } else {
  	echo mysql_error();
	}
	$i++;
}


// events
if(rand(0, $EVENT_KOEF * sizeof($userlist)) < 10) {
  $rnd_msg = GetEvent();
  SaveMsg($user['id'], 'e', "<i>$rnd_msg</i>");
}


// msg
$msg_result = mysql_query("SELECT * FROM `msg` WHERE ((`type`='b' OR `type`='e') AND `user_id`={$user['id']}) OR (`type`='c') ORDER BY `date` DESC");
$i = 0;
$msg = array();
while($msg_data = mysql_fetch_assoc($msg_result)) {
	$msg[$i] = $msg_data;
	$i++;
	if($i > $MESSAGE_COUNT) break;
}

echo $roboverh;

function BattleEvents(&$user, &$enemy) {
  global $WEAPON_EVENT_KOEF;
  
  $rnd = rand(0, $WEAPON_EVENT_KOEF);
  if($rnd < 10) {
    $rnd = rand(0, 6);
    switch($rnd) {
      case 0: // уронили оружие
        if($user['weapon'] != '') {
          SaveMsg($user['id'], 'e', "<i>В запале драки вы случайно выронили оружие из рук. <b>{$user['weapon']}</b> упал в зловонную жижу под ногами и сгинул там на веки.</i>");
          $user['weapon'] = '';
        }
      break;
      case 1: // нашли оружие
        if($user['weapon'] == '') {
          $user['weapon'] = GetWeapon();
          SaveMsg($user['id'], 'e', "<i>Глядь, а в кустах лежит новенький <b>{$user['weapon']}</b>. Скорее подобрали, пока никто не видит.</i>");
        }
      break;
      case 2: // выбили у противника оружие
        if($enemy['weapon'] != '') {
          $enemy['weapon'] = '';
          SaveMsg($user['id'], 'e', "<i>Хорошенько приноровившись к стилю боя противника вы ловко выбили из его рук оружие.</i>");
          if($enemy['type'] != 'm') SaveMsg($enemy['id'], 'e', "<i>Сделав обманное движение противник ловким приёмом обезоруживает вас.</i>");
        }
      break;
      case 3: // противник выбивает оружие
        if($user['weapon'] != '') {
          $user['weapon'] = '';
          SaveMsg($user['id'], 'e', "<i>Сделав обманное движение противник ловким приёмом обезоруживает вас.</i>");
          if($enemy['type'] != 'm') SaveMsg($enemy['id'], 'e', "<i>Хорошенько приноровившись к стилю боя противника вы ловко выбили из его рук оружие.</i>");
        }
      break;
      case 4: // таинственные превращения
        if($user['weapon'] != '') {
          $weapon = GetWeapon();
          SaveMsg($user['id'], 'e', "<i><b>{$user['weapon']}</b> вдруг окутало непроницаемым чёрным туманом, а когда туман рассеился в руках вы держали уже <b>{$weapon}</b>.</i>");
          $user['weapon'] = $weapon;
        }
      break;
      case 5: // вырвать из рук противника
        if($user['weapon'] == '' AND $enemy['weapon'] != '') {
          $user['weapon'] = $enemy['weapon'];
          $enemy['weapon'] = '';
          SaveMsg($user['id'], 'b', "<i>Вцепившись в оружие <b>{$enemy['name']}</b> вы вырвали его из рук вашего противника.</i>");
          if($enemy['type'] != 'm') SaveMsg($enemy['id'], 'b', "<i><b>{$user['name']}</b> вцепился в ваше оружие и вырвал его из ваших рук.</i>");
        }
      break;
      case 6: // повредил себя
        if($user['weapon'] != '') {
          $hit = rand(1, 25);
          SaveMsg($user['id'], 'c', "<i>Размахивая своим оружием почём зря <b>{$user['name']}</b> случайно ударил себя и нанёс <b>{$hit}</b> урона.</i>");
          $user['hp'] -= $hit;
          if($user['hp'] <= 0) {
            $user['hp'] = 0;
            if($user['type'] != 'm') SaveMsg($user['id'], 'b', "<i>Небесная концелярия зафиксировала акт суицида. Это самопожертвование зачтётся вам, когда будут подсчитываться ваши грехи.</i>");
          }
        }
      break;
    }
  }
}

function GetWeapon() {
  $rnd = rand(0, 15);
  switch($rnd) {
    case  0: return 'Молоток';
    case  1: return 'Кувалда';
    case  2: return 'Осиновый кол';
    case  3: return 'Кирпич';
    case  4: return 'Заточка';
    case  5: return 'Ножницы';
    case  6: return 'Труба';
    case  7: return 'Арматура';
    case  8: return 'Прут';
    case  9: return 'Пила';
    case 10: return 'Автоген';
    case 11: return 'Бензопила';
    case 12: return 'Газонокосилка';
    case 13: return 'Электрогитара';
    case 14: return 'Карандаш';
    case 15: return 'Крышка от люка';
  }
  return $rnd;
}

function QuestEvent(&$user) {
  global $online_users_count, $userlist;
  $rnd = rand(0, 10);
  if($rnd < 2) { // даём квест
    $eid = rand(0, $online_users_count - 1);
    mysql_query("UPDATE `user` SET `quest`={$userlist[$eid]['id']} WHERE `id`={$user['id']} LIMIT 1");
    SaveMsg($user['id'], 'e', "<i>На вас снизошло озарение свыше. И вы поняли, что <b>{$userlist[$eid]['name']}</b> должен умереть! И эту великую миссию сможете выполнить только вы один.</i>");
  }
}

function GetEvent() {
  $rnd = rand(0, 14);
	switch($rnd) {
		case  0: return "Вы услышали жуткий вой, доносившийся со стороны кладбища.";
		case  1: return "Луна осветила человеческие кости под ногами.";
		case  2: return "Луну заволокло тучами и столо совсем темно.";
		case  3: return "Мимо пробежала собака без уха и одной ноги, вся заляпанная кровью.";
		case  4: return "Ветер зловеще пошевелил мёртвые деревья завывая в ветках свои загробные псалмы.";
		case  5: return "Земля над одной из могил дрогнула и осыпалась. В проломе вы увидели мёртвую человеческую плоть, покрытую червями.";
		case  6: return "Мимо проскользнула еле заметная тень.";
		case  7: return "Кто-то истошно закричал вдалеке.";
		case  8: return "Под ногой хрустнул череп.";
		case  9: return "Неподалёку противно пискнула крыса.";
		case 10: return "Дикий хохот огласил небеса.";
		case 11: return "Сверкнула молния.";
		case 12: return "Неожиданно начался ливень.";
		case 13: return "Вы наступили в зловонную жижу.";
		case 14: return "Вы почувствовали резкий запах гнилой плоти.";
	}
	return $rnd;
}

function GetClassName($class) {
	switch($class) {
		case 'a': return 'Воин';
		case 'b': return 'Маг';
	}
}

function GetDamage($rnd, $user_rnd, $enemy_rnd) {
	$r1 = (int)(abs($rnd - $user_rnd) / 2);
	$r2 = abs($rnd - $enemy_rnd);
	if($r1 < $r2) {
		return ($r2 - $r1);
	}
	return 0;
}

function GetBody() {
	$rnd = rand(0, 10);
	switch($rnd) {
		case  0: return "в грудную броню";
		case  1: return "по управляющей микросхеме";
		case  2: return "по левой гидравлике";
		case  3: return "по правой гидравлике";
		case  4: return "по центральному узлу";
		case  5: return "в центральный привод";
		case  6: return "в датчик движения";
		case  7: return "по нижнему амортизатору";
		case  8: return "по левой нижней гидравлике";
		case  9: return "по правой нижней гидравлике";
		case 10: return "в генератор энергии";
	}
	return $rnd;
}

function GetLevel($exp) {
  $level = 0;
	if($exp > 1000) $level = 1;
	if($exp > 3000) $level = 2;
	if($exp > 6000) $level = 3;
	if($exp > 10000) $level = 4;
	if($exp > 15000) $level = 5;
	if($exp > 21000) $level = 6;
	if($exp > 29000) $level = 7;
	if($exp > 37000) $level = 8;
	if($exp > 46000) $level = 9;
	if($exp > 56000) $level = 10;
	if($exp > 67000) $level = 11;
	if($exp > 97000) $level = 12;
	if($exp > 150000) $level = 13;
	return $level;
}



function GetHP($level) {
  if($level == 13) return 666;
  return 100 + $level * 30;
}

function Battle(&$user, &$enemy) {
  //echo "battle {$user['name']} vs. {$enemy['name']}<br>";
  $rnd = rand(0, 99);
	$user_rnd = rand(0, 99);
	$enemy_rnd = rand(0, 99);
	$damage = GetDamage($rnd, $user_rnd, $enemy_rnd);
	//echo "damage $damage<br>";
	$body = GetBody();
	if($damage > 0) {
    if($user['weapon'] != '') $damage += rand(1, 25);
    $exp = $enemy['level'] - $user['level'];
    if($exp < 0) $exp = 0;
		$exp = (int)(($damage / 5) * ($exp + 1));
		if($exp <= 0) $exp = 1;
		$user['exp'] += $exp;
		if($user['type'] != 'm') SaveMsg($user['id'], 'b', "Вы ударили <b>{$enemy['name']}</b> ".$body." и нанесли <b>{$damage}</b> урона. Получено <b>$exp</b> опыта.");
		if($enemy['type'] != 'm') {
		  if($user['type'] == 'm')
        SaveMsg($enemy['id'], 'b', "<b>{$user['name']}</b> ударил вас ".$body." и нанёс <b>{$damage}</b> урона.");
      else
        SaveMsg($enemy['id'], 'b', "<a href=\"play.php?action=battle_begin&enemy={$user['id']}\">{$user['name']}</a> ударил вас ".$body." и нанёс <b>{$damage}</b> урона.");
    }
		
		$enemy['hp'] -= $damage;
  	if($enemy['hp'] <= 0) {
  		$enemy['hp'] = 0;
  		//mysql_query("DELETE FROM `battle` WHERE `user_id`={$user['id']} LIMIT 1");
  		$exp = $enemy['level'] - $user['level'];
      if($exp < 0) $exp = 0;
      $exp = ($exp + 1) * 100;
  		if($user['type'] != 'm') SaveMsg($user['id'], 'b', "<b>{$enemy['name']}</b> умер. Получено <b>$exp</b> опыта.");

      if($enemy['type'] != 'm') {
        $enemy['exp'] = (int)($enemy['exp'] * 0.95);
        $enemy['level'] = GetLevel($enemy['exp']);
        SaveMsg($enemy['id'], 'b', "Вашу браню разбили и часть микросхем были повеждены. Роботы-инженеры смогли отремонтировать вашего робота, но несмогли востановить часть накопленного опыта записанных в поврежденных микросхемах.");
      }
      
  		$user['exp'] += $exp;
  		if($user['quest'] == $enemy['id']) {
  		  $exp = 500;
        SaveMsg($user['id'], 'b', "Великая миссия завершена. Получено <b>$exp</b> опыта.");
        $user['exp'] += $exp;
        $user['quest'] = 0;
      }
  	}

    $level = GetLevel($user['exp']);
		if($level > $user['level']) {
		  $user['hp'] = GetHP($level);
		  if($user['type'] != 'm') SaveMsg($user['id'], 'b', "Вы получили <b>$level</b> уровень.");
  	}
  	$user['level'] = $level;

    if($user['hp'] <= 0) {
      $enemy['victory_count']++;
    }
    if($enemy['hp'] <= 0) {
      $user['victory_count']++;
    }
    if($user['hp'] <= 0 OR $enemy['hp'] <= 0) {
      $user['battle_count']++;
      $enemy['battle_count']++;
    }
	} else {
		if($user['type'] != 'm') SaveMsg($user['id'], 'b', "Вы попытались ударить <b>{$enemy['name']}</b> ".$body.", но промахнулись.");
		if($enemy['type'] != 'm') {
		  if($user['type'] == 'm')
		    SaveMsg($enemy['id'], 'b', "<b>{$user['name']}</b> попытался ударить вас ".$body.", но промахнулся.");
      else
        SaveMsg($enemy['id'], 'b', "<a href=\"play.php?action=battle&enemy={$user['id']}\">{$user['name']}</a> попытался ударить вас ".$body.", но промахнулся.");
    }
	}
	
	mysql_query("UPDATE `user` SET `hp`={$user['hp']}, `exp`={$user['exp']}, `level`={$user['level']}, `weapon`='{$user['weapon']}', `quest`={$user['quest']}, `last_update`=".time().", `battle_count`={$user['battle_count']}, `victory_count`={$user['victory_count']} WHERE `id`={$user['id']} LIMIT 1");
	echo mysql_error();
  mysql_query("UPDATE `user` SET `hp`={$enemy['hp']}, `exp`={$enemy['exp']}, `level`={$enemy['level']}, `weapon`='{$enemy['weapon']}', `last_update`=".time().", `battle_count`={$enemy['battle_count']}, `victory_count`={$enemy['victory_count']} WHERE `id`={$enemy['id']} LIMIT 1");
	echo mysql_error();
}

function SaveMsg($id, $type, $msg) {
  global $save_msg_time;
  $save_msg_time++;
	mysql_query("INSERT INTO `msg` (`type`, `user_id`, `date`, `message`) VALUES ('{$type}', {$id}, {$save_msg_time}, '{$msg}')");
  if(mysql_error()) echo "#3. ".mysql_error();
}

function GenName($i0, $i1) {
  $name = '';
  switch(rand(0, 9)) {
    case 0: $name = "Био-робот(бот)"; break;
    case 1: $name = "Механический воин(бот)"; break;
    case 2: $name = "Робот сержант(бот)"; break;
    case 3: $name = "Терминатор(бот)"; break;
    case 4: $name = "Киборг наемник(бот)"; break;
    case 5: $name = "Робот лесоруб(бот)"; break;
    case 6: $name = "Кибер-охотник(бот)"; break;
    case 7: $name = "Искуственный разум(бот)"; break;
    case 8: $name = "Разрушитель(бот)"; break;
    case 9: $name = "Скрытый убийца(бот)"; break;
  }
  return $name;
}
?>