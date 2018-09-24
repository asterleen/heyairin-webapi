<?php
/*

        HeyAirin Web API :: Database module
    Licensed under GNU GPL v3, see LICENSE file
       by Asterleen ~ https://asterleen.com
	      
*/

require_once ('engine/pgsql.php');

function getToken ($uid)
{
	return sqlQuery('SELECT internal_token, active FROM auth WHERE user_id = ?', $uid)->fetch();
}

function addToken($itoken, $uid)
{
	sqlQuery('INSERT INTO auth (internal_token, user_id) VALUES (?, ?)', $itoken, $uid);
}

function updateToken ($uid, $nitoken)
{
	sqlQuery('UPDATE auth SET internal_token = ? WHERE user_id = ?', $nitoken, $uid);
}

function setTokenActive ($itoken, $active = true)
{
	sqlQuery('UPDATE auth SET active = ? WHERE internal_token = ?',
			($active) ? 'true' : 'false', $itoken);
}

function getUserLogin($itoken)
{
	$res = sqlQuery('SELECT COUNT(*) as cnt, user_id FROM auth WHERE internal_token=? group by user_id limit 1', $itoken)->fetch();
	return ($res['cnt'] == 0) ? false : $res['user_id']; // use only strict comparsion! 
}

function setUserMiscInfo ($uid, $info)
{
	sqlQuery('UPDATE auth SET misc_info = ? WHERE user_id = ?', $info, $uid);
}

function isUserBanned ($login)
{
	$res = sqlQuery('SELECT ban_state from bans WHERE ban_login = ?', $login)->fetch();

	/*
		Ban states:
			0 or empty array - not banned
			1 - shadow banned
			2 - completely banned

		See ban_states table in database
	*/

	return ($res['ban_state'] == 2 ? true : false);
}

function isUserAuthorized ($login)
{
	$res = sqlQuery('SELECT COUNT(*) as cnt, user_id FROM auth WHERE user_id = ? AND active = true group by user_id limit 1', $login)->fetch();

	return ($res['cnt'] == 0) ? false : true;
}

function isUserAdmin ($login)
{
	$res = sqlQuery('SELECT COUNT(*) as cnt FROM admin_users WHERE user_login = ? group by user_login limit 1', $login)->fetch();

	return ($res['cnt'] == 0) ? false : true;
}

/// -------------------------------------------- ///

function getLastId()
{
	$res = sqlQuery('SELECT MAX(message_id) as mval FROM messages')->fetch();
	return (int)$res['mval'];
}
					  
function getMessages($amount, $offset, $getHidden = false)
{
	$amount = (int)$amount;

	if ($getHidden === true)
	{
		$res = sqlQuery('SELECT count(*) as cnt  
						FROM messages 
						where message_visible = false AND message_id >= ?
						GROUP BY message_id
						LIMIT '.$amount,
				$offset)->fetch();

		return (int)$res['cnt'];
	}
		else
	return sqlQuery('SELECT message_id as id,
						message_author_name as name,
						message_name_color as hash, 
						message_text as message,
						UNIX_TIMESTAMP(message_timestamp) as timestamp 
						FROM messages 
						where message_visible = true AND message_id >= ? order by message_id asc LIMIT '.$amount,
				$offset)->fetchAll();
}

