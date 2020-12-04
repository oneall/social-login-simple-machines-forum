<?php
/**
 * @package       OneAll Social Login
 * @copyright     Copyright 2011-Present http://www.oneall.com - All rights reserved.
 * @license       GNU/GPL 2 or later
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

// Security Check.
if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
{
    require_once dirname(__FILE__) . '/SSI.php';
}
elseif (!defined('SMF'))
{
    die('<strong>Unable to install:</strong> Please make sure that you have copied this file in the same location as the index.php of your SMF.');
}

// Load the SMF database functions.
db_extend('packages');

// Create user_tokens table.
$smcFunc['db_create_table']('{db_prefix}oasl_users', array(
    array(
        'name' => 'id_oasl_user',
        'type' => 'int',
        'unsigned' => '1',
        'auto' => true
    ),
    array(
        'name' => 'id_member',
        'type' => 'int',
        'unsigned' => '1',
        'default' => '0'
    ),
    array(
        'name' => 'user_token',
        'type' => 'char',
        'size' => '40'
    )
),
    array(
        array(
            'type' => 'primary',
            'columns' => array(
                'id_oasl_user'
            )
        ),
        array(
            'type' => 'unique',
            'columns' => array(
                'user_token'
            )
        )
    ),
    array(),
    'ignore'
);

// Create identity_tokens table.
$smcFunc['db_create_table']('{db_prefix}oasl_identities', array(
    array(
        'name' => 'id_oasl_identity',
        'type' => 'int',
        'unsigned' => '1',
        'auto' => true
    ),
    array(
        'name' => 'id_oasl_user',
        'type' => 'int',
        'unsigned' => '1',
        'default' => '0'
    ),
    array(
        'name' => 'identity_token',
        'type' => 'char',
        'size' => '40'
    )
),
    array(
        array(
            'type' => 'primary',
            'columns' => array(
                'id_oasl_identity'
            )
        ),
        array(
            'type' => 'unique',
            'columns' => array(
                'identity_token'
            )
        )
    ),
    array(),
    'ignore'
);

// Setup settings.
$oasl_settings = array();

// API connection handler.
$oasl_settings['oasl_api_handler'] = 'curl';
$oasl_settings['oasl_api_port'] = '443';

// API connection settings.
$oasl_settings['oasl_api_subdomain'] = '';
$oasl_settings['oasl_api_key'] = '';
$oasl_settings['oasl_api_secret'] = '';

// General settings.
$oasl_settings['oasl_settings_login_caption'] = 'Login with your social network';
$oasl_settings['oasl_settings_login_allow_new'] = '1';
$oasl_settings['oasl_settings_registration_caption'] = 'Simply register using your social network account';
$oasl_settings['oasl_settings_profile_caption'] = 'Social Networks';
$oasl_settings['oasl_settings_profile_desc'] = 'Link your forum account to one or more social network accounts.';
$oasl_settings['oasl_settings_link_accounts'] = '1';
$oasl_settings['oasl_settings_use_avatars'] = '1';
$oasl_settings['oasl_settings_upload_avatars'] = '1';
$oasl_settings['oasl_settings_ask_for_email'] = '1';
$oasl_settings['oasl_settings_reg_method'] = 'auto';
$oasl_settings['oasl_settings_allow_login_new'] = '1';

// Available and enabled providers.
$oasl_settings['oasl_providers'] = 'amazon,apple,battlenet,blogger,discord,disqus,draugiem,dribbble,facebook,foursquare,github,google,instagram,line,linkedin,livejournal,mailru,meetup,mixer,odnoklassniki,openid,patreon,paypal,pinterest,pixelpin,reddit,skyrock,soundcloud,stackexchange,steam,tumblr,twitch,twitter,vimeo,vkontakte,weibo,windowslive,wordpress,yahoo,youtube';
$oasl_settings['oasl_enabled_providers'] = 'facebook,twitter,google,linkedin';

// Update settings.
updateSettings($oasl_settings);

// Are we done?
if (SMF == 'SSI')
{
    echo 'Database installation complete!';
}
