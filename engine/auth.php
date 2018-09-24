<?php
/*

	KKomnata Web Service
	Made by Asterleen @ 2018
	https://asterleen.com

*/

function finish ($status, $payload = '')
{
	if ($status == 0)
	{
		header ('HTTP/1.1 302 Authenticated');
		header (sprintf ('Location: '.FRONTEND_ADDRESS, $payload));
		die ('Redirecting...');
	}
		else
		die ('<h1>Failed to authorize: '.$payload.'</h1>');
}

function processIncomingLogin ($login, $misc_info = '')
{
	$res = getToken($login);
	$ntoken = '';
	if (!empty($res['internal_token']))
	{
		if ($res['active'])
			$ntoken = $res['internal_token'];
		else
		{
			$ntoken = mknonce();
			updateToken ($login, $ntoken);
			setTokenActive ($ntoken, true);
		}
	}
		else
	{
		$ntoken = mknonce();
		addToken($ntoken, $login);
	}

	setUserMiscInfo ($login, $misc_info);
	finish(0, $ntoken);
}

array_shift($route); // we're in /api/auth/ section

switch ($route[0])
{
	case 'vk' :
		require_once 'engine/auth/vk.php';
		break;

	case 'fb' :
		require_once 'engine/auth/fb.php';
		break;
	
	default:
		display404();
		break;
}