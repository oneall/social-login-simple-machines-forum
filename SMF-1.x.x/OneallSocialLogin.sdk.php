<?php
/**
 * @package   	OneAll Social Login
 * @copyright 	Copyright 2012 http://www.oneall.com - All rights reserved.
 * @license   	GNU/GPL 2 or later
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307,USA.
 *
 * The "GNU General Public License" (GPL) is available at
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 */
if (!defined('SMF'))
{
	die('You are not allowed to access this file directly');
}

//OneAll Social Login Version
define ('OASL_VERSION', '1.0');


/**
 * Links the user/identity tokens to an id_member
 */
function oneall_social_login_link_tokens_to_id_member ($user_token, $identity_token, $id_member)
{
	global $db_prefix;

	// Drop other links - we don't want multiple links.
	oneall_social_login_unlink_identity_token_from_id_member ($identity_token, $id_member);

	// Tie the user_token to a member.
	db_query("INSERT INTO {$db_prefix}oneall_social_login_identities SET ID_MEMBER='".intval($id_member)."', user_token='".$user_token."', identity_token='".$identity_token."'", __FILE__, __LINE__);

	// Done.
	return true;
}


/**
 * UnLinks the identity from an id_member
 */
function oneall_social_login_unlink_identity_token_from_id_member ($identity_token, $id_member)
{
	global $db_prefix;

	// Drop other links - we don't want multiple links.
	db_query("DELETE FROM {$db_prefix}oneall_social_login_identities WHERE ID_MEMBER = '".intval ($db_prefix)."' AND identity_token = '".$identity_token."'", __FILE__, __LINE__);

	// Done.
	return true;
}


/**
 * Get the user_token for a given id_member
 */
function oneall_social_login_get_user_token_for_id_member_for_user_token ($id_member)
{
	global $db_prefix;

	//Get the user_token for a given id_member
	$request = db_query("SELECT user_token FROM {$db_prefix}oneall_social_login_identities WHERE ID_MEMBER = '".intval($id_member)."'", __FILE__, __LINE__);
	$row = mysql_fetch_assoc($request);
	mysql_free_result($request);

	// We have found an entry
	if (!empty($row['user_token']))
	{
		return $row['user_token'];
	}

	//Not found
	return false;
}


/**
 * Get the user identifier for a given token
 */
function oneall_social_login_get_id_member_for_user_token ($user_token)
{
	global $db_prefix;

	//Get the user identifier for a given token
	$request = db_query("SELECT ID_MEMBER FROM {$db_prefix}oneall_social_login_identities WHERE user_token = '".$user_token."'", __FILE__, __LINE__);
	$row = mysql_fetch_assoc($request);
	mysql_free_result($request);

	// We have found an entry
	if (!empty($row['ID_MEMBER']))
	{
		// Check if the user account exists.
		$request = db_query("SELECT ID_MEMBER FROM {$db_prefix}members WHERE ID_MEMBER = '".intval ($row['ID_MEMBER'])."'", __FILE__, __LINE__);
		$row = mysql_fetch_assoc($request);
		mysql_free_result($request);

		// The user account exists.
		if (!empty($row['ID_MEMBER']))
		{
			// Return the user identifier.
			return $row['ID_MEMBER'];
		}
		//The user account does not exist.
		else
		{
			// Delete the wrongly linked user_token.
			db_query("DELETE FROM {$db_prefix}oneall_social_login_identities WHERE user_token ='".$user_token."'", __FILE__, __LINE__);
		}
	}

	//Error
	return false;
}


/**
 * Get the user identifier for a given email address
 */
function oneall_social_login_get_id_member_for_email_address ($email_address)
{
	global $db_prefix;

	// Check if the user account exists.
	$request = db_query("SELECT ID_MEMBER FROM {$db_prefix}members WHERE emailAddress = '".$email_address."'", __FILE__, __LINE__);
	$row = mysql_fetch_assoc($request);
	mysql_free_result($request);

	// The user account exists.
	if (!empty($row['ID_MEMBER']))
	{
		// Return the user identifier.
		return $row['ID_MEMBER'];
	}

	// The email is not assigned to any account.
	return false;
}


/**
 * Create a random email
 */
function oneall_social_login_create_rand_email_address ()
{
	do
	{
		$email_address = md5 (uniqid (mt_rand (10000, 99000))) . "@example.com";
	}
	while (oneall_social_login_get_id_member_for_email_address ($email_address) !== false);
	return $email_address;
}


/**
 * Send an API request by using the given handler
 */
function oneall_social_login_do_api_request ($handler, $url, $options = array (), $timeout = 15)
{
	//FSOCKOPEN
	if ($handler == 'fsockopen')
	{
		return oneall_social_login_fsockopen_request ($url, $options, $timeout);
	}
	//CURL
	else
	{
		return oneall_social_login_curl_request ($url, $options, $timeout);
	}
}


/**
 * Check if CURL can be used to communicate with the OneAll API
 */
function oneall_social_login_check_curl ($secure = true)
{
	if (in_array ('curl', get_loaded_extensions ()) AND function_exists ('curl_exec'))
	{
		$result = oneall_social_login_curl_request (($secure ? 'https' : 'http') . '://www.oneall.com/ping.html');
		if (is_object ($result) AND property_exists ($result, 'http_code') AND $result->http_code == 200)
		{
			if (property_exists ($result, 'http_data'))
			{
				if (strtolower ($result->http_data) == 'ok')
				{
					return true;
				}
			}
		}
	}
	return false;
}


/**
 * Send a CURL request to the OneAll API
 */
function oneall_social_login_curl_request ($url, $options = array (), $timeout = 10)
{
	//Store the result
	$result = new stdClass ();

	//Send request
	$curl = curl_init ();
	curl_setopt ($curl, CURLOPT_URL, $url);
	curl_setopt ($curl, CURLOPT_HEADER, 0);
	curl_setopt ($curl, CURLOPT_TIMEOUT, $timeout);
	curl_setopt ($curl, CURLOPT_VERBOSE, 0);
	curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt ($curl, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt ($curl, CURLOPT_USERAGENT, 'SocialLogin '.OASL_VERSION.' SMF (+http://www.oneall.com/)');

	// BASIC AUTH?
	if (isset ($options ['api_key']) AND isset ($options ['api_secret']))
	{
		curl_setopt ($curl, CURLOPT_USERPWD, $options ['api_key'] . ":" . $options ['api_secret']);
	}

	//Make request
	if (($http_data = curl_exec ($curl)) !== false)
	{
		$result->http_code = curl_getinfo ($curl, CURLINFO_HTTP_CODE);
		$result->http_data = $http_data;
		$result->http_error = null;
	}
	else
	{
		$result->http_code = -1;
		$result->http_data = null;
		$result->http_error = curl_error ($curl);
	}

	//Done
	return $result;
}


/**
 * Check if fsockopen can be used to communicate with the OneAll API
 */
function oneall_social_login_check_fsockopen ($secure = true)
{
	$result = oneall_social_login_fsockopen_request (($secure ? 'https' : 'http') . '://www.oneall.com/ping.html');
	if (is_object ($result) AND property_exists ($result, 'http_code') AND $result->http_code == 200)
	{
		if (property_exists ($result, 'http_data'))
		{
			if (strtolower ($result->http_data) == 'ok')
			{
				return true;
			}
		}
	}
	return false;
}


/**
 * Sends an fsockopen request to the OneAll API
 */
function oneall_social_login_fsockopen_request ($url, $options = array (), $timeout = 15)
{
	//Store the result
	$result = new stdClass ();

	//Make that this is a valid URL
	if (($uri = parse_url ($url)) == false)
	{
		$result->http_code = -1;
		$result->http_data = null;
		$result->http_error = 'invalid_uri';
		return $result;
	}

	//Make sure we can handle the schema
	switch ($uri ['scheme'])
	{
		case 'http':
			$port = (isset ($uri ['port']) ? $uri ['port'] : 80);
			$host = ($uri ['host'] . ($port != 80 ? ':' . $port : ''));
			$fp = @fsockopen ($uri ['host'], $port, $errno, $errstr, $timeout);
			break;

		case 'https':
			$port = (isset ($uri ['port']) ? $uri ['port'] : 443);
			$host = ($uri ['host'] . ($port != 443 ? ':' . $port : ''));
			$fp = @fsockopen ('ssl://' . $uri ['host'], $port, $errno, $errstr, $timeout);
			break;

		default:
			$result->http_code = -1;
			$result->http_data = null;
			$result->http_error = 'invalid_schema';
			return $result;
			break;
	}

	//Make sure the socket opened properly
	if (!$fp)
	{
		$result->http_code = -$errno;
		$result->http_data = null;
		$result->http_error = trim ($errstr);
		return $result;
	}

	//Construct the path to act on
	$path = (isset ($uri ['path']) ? $uri ['path'] : '/');
	if (isset ($uri ['query']))
	{
		$path .= '?' . $uri ['query'];
	}

	//Create HTTP request
	$defaults = array (
			'Host' => "Host: $host",
			'User-Agent' => 'User-Agent: SocialLogin '.OASL_VERSION.' SMF (+http://www.oneall.com/)'
	);

	// BASIC AUTH?
	if (isset ($options ['api_key']) AND isset ($options ['api_secret']))
	{
		$defaults ['Authorization'] = 'Authorization: Basic ' . base64_encode ($options ['api_key'] . ":" . $options ['api_secret']);
	}

	//Build and send request
	$request = 'GET ' . $path . " HTTP/1.0\r\n";
	$request .= implode ("\r\n", $defaults);
	$request .= "\r\n\r\n";
	fwrite ($fp, $request);

	//Fetch response
	$response = '';
	while (!feof ($fp))
	{
		$response .= fread ($fp, 1024);
	}

	//Close connection
	fclose ($fp);

	//Parse response
	list($response_header, $response_body) = explode ("\r\n\r\n", $response, 2);

	//Parse header
	$response_header = preg_split ("/\r\n|\n|\r/", $response_header);
	list($header_protocol, $header_code, $header_status_message) = explode (' ', trim (array_shift ($response_header)), 3);

	//Build result
	$result->http_code = $header_code;
	$result->http_data = $response_body;

	//Done
	return $result;
}
