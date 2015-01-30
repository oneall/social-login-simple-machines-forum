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

/**
 * Administration Area
 */
function oneall_social_login_config ()
{
	global $context;

	// Perform action.
	$sa = (!empty ($_REQUEST ['sa']) ? $_REQUEST ['sa'] : '');
	switch ($sa)
	{
		case 'autodetect';
		$context['sub_action'] = 'autodetect';
			oneall_social_login_autodetect_api_connection ();
			exit;

		case 'verify';
		$context['sub_action'] = 'verify';
			oneall_social_login_verify_api_settings ();
			exit;

		case 'save':
			$context['sub_action'] = 'save';
			oneall_social_login_config_save ();
			break;

		default:
			$context['sub_action'] = 'settings';
			oneall_social_login_config_show ();
			break;
	}
}

/**
 * Autodetect API Connection Handler
 */
function oneall_social_login_autodetect_api_connection ()
{
	global $boarddir, $sourcedir;

	// Only for administrators.
	isAllowedTo ('admin_forum');

	// Security Check.
	checkSession ('post');

	// Include the OneAll SDK.
	require_once($sourcedir . '/OneallSocialLogin.sdk.php');

	// Initialize.
	$oasl_api_handler = null;
	$oasl_api_port = null;

	// Check CURL HTTPS - Port 443.
	if (oneall_social_login_check_curl (true) === true)
	{
		$oasl_api_handler = 'curl';
		$oasl_api_port = 443;
	}
	// Check CURL HTTP - Port 80.
	elseif (oneall_social_login_check_curl (false) === true)
	{
		$oasl_api_handler = 'curl';
		$oasl_api_port = 80;
	}
	// Check FSOCKOPEN HTTPS - Port 443.
	elseif (oneall_social_login_check_fsockopen (true) == true)
	{
		$oasl_api_handler = 'fsockopen';
		$oasl_api_port = 443;
	}
	// Check FSOCKOPEN HTTP - Port 80.
	elseif (oneall_social_login_check_fsockopen (false) == true)
	{
		$oasl_api_handler = 'fsockopen';
		$oasl_api_port = 80;
	}

	// Update Settings.
	if (!empty ($oasl_api_handler) AND !empty ($oasl_api_port))
	{
		//Update
		$values = array ();
		$values ['oasl_api_handler'] = $oasl_api_handler;
		$values ['oasl_api_port'] = $oasl_api_port;
		updateSettings ($values);

		//Set status
		$status = 'success';
	}
	else
	{
		// Set status.
		$status = 'error';
	}

	// Redirect to the administration area.
	redirectexit ('action=oasl;sa=settings;oasl_action=autodetect;oasl_status=' . $status . '#oasl_api_connection_handler');
}


/**
 * Verify API Settings
 */
function oneall_social_login_verify_api_settings ()
{
	global $boarddir, $sourcedir;

	// Only for administrators
	isAllowedTo ('admin_forum');

	// Security Check.
	checkSession ('post');

	// Include the OneAll SDK.
	require_once($sourcedir . '/OneallSocialLogin.sdk.php');

	//Default
	$status = null;

	// Read settings.
	$oasl_api_subdomain = (! empty ($_POST ['oasl_api_subdomain']) ? trim (strtolower ($_POST ['oasl_api_subdomain'])) : '');
	$oasl_api_key = (! empty ($_POST ['oasl_api_key']) ?trim ($_POST ['oasl_api_key']) : '');
	$oasl_api_secret = (! empty ($_POST ['oasl_api_secret']) ? trim ($_POST ['oasl_api_secret']) : '');

	// Full domain entered.
	if (preg_match ("/([a-z0-9\-]+)\.api\.oneall\.com/i", $oasl_api_subdomain, $matches))
	{
		$oasl_api_subdomain = $matches [1];
	}

	// Update Settings.
	$values = array ();
	$values ['oasl_api_subdomain'] = $oasl_api_subdomain;
	$values ['oasl_api_key'] = $oasl_api_key;
	$values ['oasl_api_secret'] = $oasl_api_secret;
	updateSettings ($values);

	//Check if all fields have been filled out
	if (empty ($oasl_api_key) OR empty ($oasl_api_secret) OR empty ($oasl_api_subdomain))
	{
		$status = 'error_not_all_fields_filled_out';
	}
	else
	{
		//Read settings
		$oasl_api_connection_handler = ((!empty ($_POST ['oasl_api_handler']) AND $_POST ['oasl_api_handler'] == 'fsockopen') ? 'fsockopen' : 'curl');
		$oasl_api_connection_use_https = ((!isset ($_POST ['oasl_api_port']) OR $_POST ['oasl_api_port'] == 443) ? true : false);

		//FSOCKOPEN
		if ($oasl_api_connection_handler == 'fsockopen')
		{
			if (!oneall_social_login_check_fsockopen ($oasl_api_connection_use_https))
			{
				$status = 'error_selected_handler_faulty';
			}
		}
		//CURL
		else
		{
			if (!oneall_social_login_check_curl ($oasl_api_connection_use_https))
			{
				$status = 'error_selected_handler_faulty';
			}
		}

		//If we have a status then we have a problem
		if (empty ($status))
		{
			//Check subdomain format
			if (!preg_match ("/^[a-z0-9\-]+$/i", $oasl_api_subdomain))
			{
				$status = 'error_subdomain_wrong_syntax';
			}
			else
			{
				//Domain
				$oasl_api_domain = $oasl_api_subdomain . '.api.oneall.com';

				//Connection to
				$api_resource_url = ($oasl_api_connection_use_https ? 'https' : 'http') . '://' . $oasl_api_domain . '/tools/ping.json';

				//Get connection details
				$result = oneall_social_login_do_api_request ($oasl_api_connection_handler, $api_resource_url, array ('api_key' => $oasl_api_key, 'api_secret' => $oasl_api_secret), 15);

				//Parse result
				if (is_object ($result) AND property_exists ($result, 'http_code') AND property_exists ($result, 'http_data'))
				{
					switch ($result->http_code)
					{
						//Success
						case 200:

							//Set status
							$status = 'success';
							break;

						//Authentication Error
						case 401:
							$status = 'error_authentication_credentials_wrong';
							break;

						//Wrong Subdomain
						case 404:
							$status = 'error_subdomain_wrong';
							break;

						//Other error
						default:
							$status = 'error_communication';
							break;
					}
				}
				else
				{
					$status = 'error_communication';
				}
			}
		}
	}

	// Redirect to the administration area.
	redirectexit ('action=oasl;sa=settings;oasl_action=verify;oasl_status=' . $status . '#oasl_api_settings');
}


/**
 * Show administration area settings
 */
function oneall_social_login_config_show ()
{
	global $txt, $context;

	// Only for administrators
	isAllowedTo ('admin_forum');

	// Load the administration bar.
	adminIndex('oasl_config');

	// Load template.
	loadtemplate ('OneallSocialLogin');

	// Set template.
	$context ['sub_template'] = 'oneall_social_login_config';

	// Set page title.
	$context ['page_title'] = $txt['oasl_title'];

	// Setup tabs,
	$context['admin_tabs'] = array(
		'title' => $txt['oasl_title'],
		'description' => $txt['oasl_config_desc'],
		'tabs' => array(
			'settings' => array(
				'title' =>  $txt['oasl_config'],
				'href' => $scripturl . '?action=oasl;sa=settings',
				'is_selected' => true
			)
		)
	);
}


/**
 * Save administration area settings
 */
function oneall_social_login_config_save ()
{
	// Only for administrators
	isAllowedTo ('admin_forum');

	// Load template.
	loadtemplate ('OneallSocialLogin');

	// Security Check.
	checkSession ('post');

	// API Connection Handler.
	$oasl_api_handler = ((isset ($_POST ['oasl_api_handler']) AND $_POST ['oasl_api_handler'] == 'fsockopen') ? 'fsockopen' : 'curl');
	$oasl_api_port = ((isset ($_POST ['oasl_api_port']) AND $_POST ['oasl_api_port'] == 80) ? 80 : 443);

	// API Settings.
	$oasl_api_key = (isset ($_POST ['oasl_api_key']) ? trim ($_POST ['oasl_api_key']) : '');
	$oasl_api_secret = (isset ($_POST ['oasl_api_key']) ? trim ($_POST ['oasl_api_secret']) : '');
	$oasl_api_subdomain = (isset ($_POST ['oasl_api_subdomain']) ? strtolower (trim ($_POST ['oasl_api_subdomain'])) : '');

	// Additional Settings.
	$oasl_settings_login_caption = (isset ($_POST ['oasl_settings_login_caption']) ? trim ($_POST ['oasl_settings_login_caption']) : '');
	$oasl_settings_registration_caption = (isset ($_POST ['oasl_settings_registration_caption']) ? trim ($_POST ['oasl_settings_registration_caption']) : '');
	$oasl_settings_profile_caption = (isset ($_POST ['oasl_settings_profile_caption']) ? trim ($_POST ['oasl_settings_profile_caption']) : '');
	$oasl_settings_profile_desc = (isset ($_POST ['oasl_settings_profile_desc']) ? trim ($_POST ['oasl_settings_profile_desc']) : '');
	$oasl_settings_link_accounts = ( ! empty ($_POST ['oasl_settings_link_accounts']) ? 1 : 0);
	$oasl_settings_use_avatars = ( ! empty ($_POST ['oasl_settings_use_avatars']) ? 1 : 0);

	// Full domain entered.
	if (preg_match ("/([a-z0-9\-]+)\.api\.oneall\.com/i", $oasl_api_subdomain, $matches))
	{
		$oasl_api_subdomain = $matches [1];
	}

	// Enabled Providers.
	$oasl_enabled_providers = array ();
	if (isset ($_POST ['oasl_enabled_providers']) AND is_array ($_POST ['oasl_enabled_providers']))
	{
		foreach ($_POST ['oasl_enabled_providers'] AS $provider)
		{
			$oasl_enabled_providers [] = trim (strtolower ($provider));
		}
	}

	// API Settings.
	$values = array ();
	$values ['oasl_api_handler'] = $oasl_api_handler;
	$values ['oasl_api_port'] = $oasl_api_port;
	$values ['oasl_api_subdomain'] = $oasl_api_subdomain;
	$values ['oasl_api_key'] = $oasl_api_key;
	$values ['oasl_api_secret'] = $oasl_api_secret;

	// Additional Settings.
	$values ['oasl_settings_profile_caption'] = $oasl_settings_profile_caption;
	$values ['oasl_settings_profile_desc'] = $oasl_settings_profile_desc;
	$values ['oasl_settings_login_caption'] = $oasl_settings_login_caption;
	$values ['oasl_settings_registration_caption'] = $oasl_settings_registration_caption;
	$values ['oasl_settings_link_accounts'] = $oasl_settings_link_accounts;
	$values ['oasl_settings_use_avatars'] = $oasl_settings_use_avatars;

	// Enabled Providers.
	$values ['oasl_enabled_providers'] = implode (',', $oasl_enabled_providers);

	// Update Settings.
	updateSettings ($values);

	// Redirect to the administration area.
	redirectexit ('action=oasl;sa=settings;oasl_action=saved');
}