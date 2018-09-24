<?php
/*

    HeyAirin Web API :: Authentication module for Facebook
       Licensed under GNU GPL v3, see LICENSE file
          by Asterleen ~ https://asterleen.com

*/

array_shift($route); // we're in /api/auth/vk section
if ($route[0] === 'start') {
	header ('HTTP/1.1 302 Auth Initiated');
	// TODO: replace %s with corresponding fields
	header (sprintf('Location: https://www.facebook.com/v2.12/dialog/oauth?client_id=%s&redirect_uri=%s&state=kk&response_type=code', FB_CLIENT_ID, urlencode(FB_REDIRECT_URI)));
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
					'https://graph.facebook.com/v2.12/oauth/access_token',
					'get',
					Array(
							'client_id'     => FB_CLIENT_ID,
							'redirect_uri'  => FB_REDIRECT_URI,
							'client_secret' => FB_CLIENT_SECRET,
							'code'          => $code
						)
					);

$json = json_decode($res, true);
if (empty($json))
{
	error_log('WARNING: Bad Facebook response at phase 1: '.$res);
	finish(1, 'Bad response from Facebook auth server');
}
	else
{
	if (!empty($json['error']))
	{
		finish (2, sprintf('Authentication error: %s (code %s, type %s)', $json['error']['message'], $json['error']['code'], $json['error']['type']));	
	}
		else
	{
		$res = curl_request('https://graph.facebook.com/debug_token', 'get',
						    Array('input_token'  => $json['access_token'],
								  'access_token' => FB_CLIENT_ID.'|'.FB_CLIENT_SECRET));

		$access_token = $json['access_token'];

		$json = json_decode($res, true);
		if (empty($json))
		{
			error_log('WARNING: Bad Facebook response at phase 2: '.$res);
			finish(1, 'Bad response from Facebook auth server');
		}
			else
		if (!empty($json['error']))
		{
			finish (2, sprintf('Authentication error: %s (code %s, type %s)', $json['error']['message'], $json['error']['code'], $json['error']['type']));	
		}
			else
			{
				$res = curl_request('https://graph.facebook.com/me', 'get',
									Array('access_token' => $access_token));
				$uinfo = json_decode($res, true);

				if (!empty($uinfo['error']))
					finish (2, sprintf('Stage 2 auth error: %s (code %s, type %s)', $uinfo['error']['message'], $uinfo['error']['code'], $uinfo['error']['type']));
				else
					processIncomingLogin('fb_'.(int)$json['data']['user_id'], normalize_name($uinfo['name']));
			}
	}
}
