<?php
/*

   HeyAirin Web API :: Index module, does routing
    Licensed under GNU GPL v3, see LICENSE file
       by Asterleen ~ https://asterleen.com
	      
*/

require_once 'engine/enconfig.php';
require_once 'engine/database.php';
require_once 'engine/functions.php';

error_reporting(E_ALL ^ E_NOTICE);

$route = explode('/', $_GET['route']);

switch ($route[0])
{
	case 'auth' :
		require_once 'engine/auth.php';
		break;

	default :
		header('HTTP/1.1 302 Nothing To See Here');
		header('Location: https://github.com/asterleen/heyairin-webapi');
		die ('Loal');
	break;
}