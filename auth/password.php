<?php
class userpass
{

public static function pass()
{
if ($password == md5($m_password)) return true;
			
	else return false;
}

}
?>