<?php
/**
 * @package       OneAll Social Login
 * @copyright     Copyright 2012 http://www.oneall.com - All rights reserved.
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

// Identities table.
db_query("CREATE TABLE IF NOT EXISTS {$db_prefix}oneall_social_login_identities (`id` int(10) unsigned NOT NULL AUTO_INCREMENT, `ID_MEMBER` mediumint(8) unsigned NOT NULL, `user_token` char(40) NOT NULL, `identity_token` char(40) NOT NULL, PRIMARY KEY (`id`), UNIQUE KEY `user_token` (`user_token`),  UNIQUE KEY `identity_token` (`identity_token`)) ENGINE=MyISAM DEFAULT CHARSET=latin1", __FILE__, __LINE__);

// API connection handler.
db_query("INSERT IGNORE INTO {$db_prefix}settings VALUES ('oasl_api_handler', 'curl')", __FILE__, __LINE__);
db_query("INSERT IGNORE INTO {$db_prefix}settings VALUES ('oasl_api_port', '443')", __FILE__, __LINE__);

// API settings.
db_query("INSERT IGNORE INTO {$db_prefix}settings VALUES ('oasl_api_subdomain', '')", __FILE__, __LINE__);
db_query("INSERT IGNORE INTO {$db_prefix}settings VALUES ('oasl_api_key', '')", __FILE__, __LINE__);
db_query("INSERT IGNORE INTO {$db_prefix}settings VALUES ('oasl_api_secret', '')", __FILE__, __LINE__);

//  Additional Settings.
db_query("INSERT IGNORE INTO {$db_prefix}settings VALUES ('oasl_settings_login_caption', 'Login with your social network')", __FILE__, __LINE__);
db_query("INSERT IGNORE INTO {$db_prefix}settings VALUES ('oasl_settings_registration_caption', 'Simply register using your social network account')", __FILE__, __LINE__);
db_query("INSERT IGNORE INTO {$db_prefix}settings VALUES ('oasl_settings_profile_caption', 'Social Networks')", __FILE__, __LINE__);
db_query("INSERT IGNORE INTO {$db_prefix}settings VALUES ('oasl_settings_profile_desc', 'Link your forum account to one or more social network accounts.')", __FILE__, __LINE__);

db_query("INSERT IGNORE INTO {$db_prefix}settings VALUES ('oasl_settings_link_accounts', '1')", __FILE__, __LINE__);
db_query("INSERT IGNORE INTO {$db_prefix}settings VALUES ('oasl_settings_use_avatars', '1')", __FILE__, __LINE__);

// Availabled and enabled providers.
db_query("REPLACE INTO {$db_prefix}settings VALUES ('oasl_providers', 'amazon, battlenet, blogger, discord, disqus, draugiem, dribbble, facebook, foursquare, github, google, instagram, line, linkedin, livejournal, mailru, meetup, odnoklassniki, openid, paypal, pinterest, pixelpin, reddit, skyrock, soundcloud, stackexchange, steam, tumblr, twitch, twitter, vimeo, vkontakte, weibo, windowslive, wordpress, xing, yahoo, youtube')", __FILE__, __LINE__);
db_query("INSERT IGNORE INTO {$db_prefix}settings VALUES ('oasl_enabled_providers', 'facebook,twitter,google,linkedin')", __FILE__, __LINE__);

if (!empty($ssi))
{
    echo 'Database installation complete!';
}
