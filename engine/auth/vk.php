<?php
/*

    HeyAirin Web API :: Authentication module for VKontakte
        Licensed under GNU GPL v3, see LICENSE file
           by Asterleen ~ https://asterleen.com
	      
*/

array_shift($route); // we're in /api/auth/vk section
if ($route[0] === 'start') {
	header ('HTTP/1.1 302 Auth Initiated');
	// TODO: replace %s with corresponding fields
	header (sprintf('Location: https://oauth.vk.com/authorize?client_id=%s&display=page&redirect_uri=%s&response_type=code', VK_CLIENT_ID, VK_REDIRECT_URI));
	die ('Auth initiated, redirecting...');
}

if (!empty($_GET['error']))
{
	finish (2, sprintf('[%s] %s, %s', $_GET['error'], $_GET['error_reason'], $_GET['error_description']));
}

$code = $_GET['code'];

if (empty($code))
{
	header ('HTTP/1.1 500 Server Error');
	finish(1, 'Bad request');
}


$res = curl_request(
					'https://oauth.vk.com/access_token',
					'get',
					Array(
							'client_id'     => VK_CLIENT_ID,
							'client_secret' => VK_CLIENT_SECRET,
							'redirect_uri'  => VK_REDIRECT_URI,
							'code'          => $code
						)
					);

$json = json_decode($res, true);
if (empty($json))
{
	error_log('WARNING: Bad VK response: '.$res);
	finish(1, 'Bad response from VK auth server');
}
	else
if (!empty($json['error']))
{
	finish (2, sprintf('Authentication error: %s (%s)', $json['error'], $json['error_description']));
}
	else
{
	$res = curl_request('https://api.vk.com/method/users.get', 'get',
					     Array('user_ids' => $json['user_id'],
					     	   'access_token' => $json['access_token'],
					     	   'v' => VK_API_VERSION));

	$uinfo = json_decode($res, true);

	if (!empty($uinfo['error']))
	{
		error_log(print_r($uinfo, true));
		finish (2, sprintf('Stage 2 auth error: #%s (%s)', $uinfo['error']['error_code'], $uinfo['error']['error_msg']));
	}
	else
		processIncomingLogin('vk_'.(int)$json['user_id'], $uinfo['response'][0]['first_name']);
}