<?php
/**
 * @package   	OneAll Social Login
 * @copyright 	Copyright 2011-Present http://www.oneall.com - All rights reserved.
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
/**
 * Before attempting to execute, this file attempts to load SSI.php to enable access to the database functions.
 */

// If we have found SSI.php and we are outside of SMF, then we are running standalone.
if (file_exists (dirname (__FILE__) . '/SSI.php') && !defined ('SMF'))
{
	require_once(dirname (__FILE__) . '/SSI.php');
	db_extend ('packages');
}
// If we are outside SMF and can't find SSI.php, then throw an error.
elseif (!defined ('SMF'))
{
	die ('<b>Error:</b> Cannot uninstall - please verify you put this file in the same place as SMF\'s SSI.php.');
}

// List of settings to remove.
$oasl_settings_to_remove = array (
	'oasl_api_handler',
	'oasl_api_port',
	'oasl_api_subdomain',
	'oasl_api_key',
	'oasl_api_secret',
	'oasl_settings_login_caption',
	'oasl_settings_registration_caption',
	'oasl_settings_profile_caption',
	'oasl_settings_profile_desc',
	'oasl_settings_link_accounts',
	'oasl_settings_use_avatars',
	'oasl_settings_ask_for_email',
	'oasl_providers',
	'oasl_enabled_providers',
);

// Clear the package settings.
global $modSettings;
foreach ($oasl_settings_to_remove as $setting)
{
	if (isset ($modSettings [$setting]))
	{
		unset ($modSettings [$setting]);
	}
}

// Remove the package settings from the database; updateSettings can't actually remove them.
$smcFunc ['db_query'] ('', '
	DELETE FROM {db_prefix}settings
	WHERE variable IN ({array_string:settings})', array (
		'settings' => $oasl_settings_to_remove,
	)
);

// And tell SMF we've updated $modSettings.
updateSettings (array (
	'settings_updated' => time (),
));
