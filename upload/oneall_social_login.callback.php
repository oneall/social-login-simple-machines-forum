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
    die('<strong>Unable to execute:</strong> Please make sure that you have installed Social Login correctly.');
}

/**
 * Social Link Callback Handler
 */
function oneall_social_login_link_callback()
{
    // Nothing to do if these haven't been set.
    if (isset($_POST) and !empty($_POST['oa_action']) and $_POST['oa_action'] == 'social_link' and !empty($_POST['connection_token']))
    {
        // Global vars.
        global $boarddir, $sourcedir, $user_settings, $context, $modSettings;

        // Include the OneAll Toolbox.
        require_once $sourcedir . '/OneallSocialLogin.sdk.php';

        // Some security checks.
        if (!empty($_REQUEST['oasl_uid']) and !empty($context['user']['is_logged']) and !empty($context['user']['id']) and $context['user']['id'] == $_REQUEST['oasl_uid'])
        {
            // The current user
            $id_member = $context['user']['id'];

            // API Connection Handler.
            $oasl_api_handler = ((isset($modSettings['oasl_api_handler']) and $modSettings['oasl_api_handler'] == 'fsockopen') ? 'fsockopen' : 'curl');
            $oasl_api_port = ((isset($modSettings['oasl_api_port']) and $modSettings['oasl_api_port'] == 80) ? 80 : 443);

            // API Settings.
            $oasl_api_key = (isset($modSettings['oasl_api_key']) ? $modSettings['oasl_api_key'] : '');
            $oasl_api_secret = (isset($modSettings['oasl_api_key']) ? $modSettings['oasl_api_secret'] : '');
            $oasl_api_subdomain = (isset($modSettings['oasl_api_subdomain']) ? $modSettings['oasl_api_subdomain'] : '');

            // Resource.
            $oasl_api_resource_url = ($oasl_api_port == 80 ? 'http' : 'https') . '://' . $oasl_api_subdomain . '.api.oneall.com/connections/' . $_POST['connection_token'] . '.json';

            // Get the connection details.
            $result = oneall_social_login_do_api_request($oasl_api_handler, $oasl_api_resource_url, array('api_key' => $oasl_api_key, 'api_secret' => $oasl_api_secret), 15);

            // Parse result
            if (is_object($result) and property_exists($result, 'http_code') and $result->http_code == 200)
            {
                // Check Result.
                if (property_exists($result, 'http_data'))
                {
                    // Decode the Social Profile Data.
                    $social_data = json_decode($result->http_data);
                    if (is_object($social_data))
                    {
                        //Extract data
                        $data = $social_data->response->result->data;

                        //Check for plugin status
                        if (is_object($data) and property_exists($data, 'plugin') and $data->plugin->key == 'social_link' and $data->plugin->data->status == 'success')
                        {
                            $identity = $data->user->identity;
                            $identity_token = $identity->identity_token;

                            $user = $data->user;
                            $user_token = $user->user_token;

                            //Status
                            $status_flag = null;
                            $status_action = null;

                            //Get the id of the linked user - Can be empty
                            $id_member_for_user_token = oneall_social_login_get_id_member_for_user_token($user_token);

                            //Link identity
                            if ($data->plugin->data->action == 'link_identity')
                            {
                                // The user already has a user_token
                                if (is_numeric($id_member_for_user_token))
                                {
                                    //Already connected to this user
                                    if ($id_member_for_user_token == $id_member)
                                    {
                                        //Status message
                                        $status_flag = 'success';
                                        $status_action = 'linked';
                                    }
                                    //Connected to a different user
                                    else
                                    {
                                        //Status message
                                        $status_flag = 'error';
                                        $status_action = 'linked_to_another_user';
                                    }
                                }
                                // The user does not have a user_token yet
                                else
                                {
                                    //Links the tokens to an id_member
                                    if (oneall_social_login_link_tokens_to_id_member($user_token, $identity_token, $id_member) === true)
                                    {
                                        //Status message
                                        $status_flag = 'success';
                                        $status_action = 'linked';
                                    }
                                }
                            }
                            //UnLink identity
                            elseif ($data->plugin->data->action == 'unlink_identity')
                            {
                                // The user already has a user_token
                                if (is_numeric($id_member_for_user_token))
                                {
                                    //Was connected to this user
                                    if ($id_member_for_user_token == $id_member)
                                    {
                                        //UnLinks the token from an id_member
                                        if (oneall_social_login_unlink_identity_token_from_id_member($identity_token, $id_member) === true)
                                        {
                                            //Status message
                                            $status_flag = 'success';
                                            $status_action = 'unlinked';
                                        }
                                    }
                                    //Connected to a different user
                                    else
                                    {
                                        //Status message
                                        $status_flag = 'error';
                                        $status_action = 'linked_to_another_user';
                                    }
                                }
                                // The user does not have a user_token yet
                                else
                                {
                                    //Status message
                                    $status_flag = 'success';
                                    $status_action = 'unlinked';
                                }
                            }

                            //Redirect to account
                            redirectexit('action=profile;u=' . $id_member . ';sa=account;oasl_status=' . $status_flag . ';oasl_action=' . $status_action . '#oasl_social_link');
                        }
                    }
                }
            }
        }
    }

    // Error
    redirectexit();
}

/**
 * Social Login Callback Handler
 */
function oneall_social_login_login_callback()
{
    // Nothing to do if these haven't been set.
    if (isset($_POST) and !empty($_POST['oa_action']) and $_POST['oa_action'] == 'social_login' and !empty($_POST['connection_token']))
    {
        // Global vars.
        global $boarddir, $sourcedir, $user_settings, $context, $modSettings, $db_prefix, $sc;

        // Include the OneAll Toolbox.
        require_once $sourcedir . '/OneallSocialLogin.sdk.php';

        // API Connection Handler.
        $oasl_api_handler = ((isset($modSettings['oasl_api_handler']) and $modSettings['oasl_api_handler'] == 'fsockopen') ? 'fsockopen' : 'curl');
        $oasl_api_port = ((isset($modSettings['oasl_api_port']) and $modSettings['oasl_api_port'] == 80) ? 80 : 443);

        // API Settings.
        $oasl_api_key = (isset($modSettings['oasl_api_key']) ? $modSettings['oasl_api_key'] : '');
        $oasl_api_secret = (isset($modSettings['oasl_api_key']) ? $modSettings['oasl_api_secret'] : '');
        $oasl_api_subdomain = (isset($modSettings['oasl_api_subdomain']) ? $modSettings['oasl_api_subdomain'] : '');

        // Resource.
        $oasl_api_resource_url = ($oasl_api_port == 80 ? 'http' : 'https') . '://' . $oasl_api_subdomain . '.api.oneall.com/connections/' . $_POST['connection_token'] . '.json';

        // Get the connection details.
        $result = oneall_social_login_do_api_request($oasl_api_handler, $oasl_api_resource_url, array('api_key' => $oasl_api_key, 'api_secret' => $oasl_api_secret), 15);

        // Parse result
        if (is_object($result) and property_exists($result, 'http_code') and $result->http_code == 200)
        {
            // Check Result.
            if (property_exists($result, 'http_data'))
            {
                // Decode the Social Profile Data.
                $social_data = json_decode($result->http_data);
                if (is_object($social_data))
                {
                    $identity = $social_data->response->result->data->user->identity;
                    $identity_token = $identity->identity_token;

                    $user = $social_data->response->result->data->user;
                    $user_token = $user->user_token;

                    // Parse Social Profile Data.
                    $user_first_name = (!empty($identity->name->givenName) ? $identity->name->givenName : '');
                    $user_last_name = (!empty($identity->name->familyName) ? $identity->name->familyName : '');
                    $user_location = (!empty($identity->currentLocation) ? $identity->currentLocation : '');
                    $user_constructed_name = trim($user_first_name . ' ' . $user_last_name);
                    $user_picture = (!empty($identity->pictureUrl) ? $identity->pictureUrl : '');
                    $user_thumbnail = (!empty($identity->thumbnailUrl) ? $identity->thumbnailUrl : '');

                    // Fullname.
                    if (!empty($identity->name->formatted))
                    {
                        $user_full_name = $identity->name->formatted;
                    }
                    elseif (!empty($identity->name->displayName))
                    {
                        $user_full_name = $identity->name->displayName;
                    }
                    else
                    {
                        $user_full_name = $user_constructed_name;
                    }

                    // Preferred Username.
                    if (!empty($identity->preferredUsername))
                    {
                        $user_login = $identity->preferredUsername;
                    }
                    elseif (!empty($identity->displayName))
                    {
                        $user_login = $identity->displayName;
                    }
                    else
                    {
                        $user_login = $user_full_name;
                    }

                    // Email Address.
                    $user_email = '';
                    if (property_exists($identity, 'emails') and is_array($identity->emails))
                    {
                        $user_email_is_verified = false;
                        while ($user_email_is_verified !== true and (list(, $email) = each($identity->emails)))
                        {
                            $user_email = $email->value;
                            $user_email_is_verified = ($email->is_verified == '1');
                        }
                    }

                    // Website/Homepage.
                    if (!empty($identity->profileUrl))
                    {
                        $user_website = $identity->profileUrl;
                    }
                    elseif (!empty($identity->urls[0]->value))
                    {
                        $user_website = $identity->urls[0]->value;
                    }
                    else
                    {
                        $user_website = '';
                    }

                    // Gender
                    $user_gender = 0;
                    if (!empty($identity->gender))
                    {
                        if ($identity->gender == 'male')
                        {
                            $user_gender = 1;
                        }
                        elseif ($identity->gender == 'female')
                        {
                            $user_gender = 2;
                        }
                    }

                    // Get the user identifier for a given token.
                    $id_member_tmp = oneall_social_login_get_id_member_for_user_token($user_token);

                    // This user already exists.
                    if (is_numeric($id_member_tmp))
                    {
                        $id_member = $id_member_tmp;
                    }
                    // This is a new user.
                    else
                    {
                        // Account linking is enabled.
                        if (!isset($modSettings['oasl_settings_link_accounts']) or $modSettings['oasl_settings_link_accounts'] == '1')
                        {
                            // Account linking only works if the email address has been verified
                            if (!empty($user_email) and $user_email_is_verified === true)
                            {
                                // Try to read the existing user account
                                if (($id_member_tmp = oneall_social_login_get_id_member_for_email_address($user_email)) !== false)
                                {
                                    // Tie the user_token to the newly created member.
                                    if (oneall_social_login_link_tokens_to_id_member($user_token, $identity_token, $id_member_tmp))
                                    {
                                        $id_member = $id_member_tmp;
                                    }
                                }
                            }
                        }
                    }

                    // Login the user.
                    if (!empty($id_member))
                    {
                        // What is being done?
                        $action = 'login';
                    }
                    //Create a new account.
                    else
                    {
                        // What is being done?
                        $action = 'register';

                        // Registration functions.
                        require_once $sourcedir . '/Subs-Members.php';

                        // Build User fields.
                        $regOptions = array();
                        $regOptions['password'] = mt_rand(0, 9999);
                        $regOptions['password_check'] = $regOptions['password'];
                        $regOptions['auth_method'] = 'password';
                        $regOptions['interface'] = 'guest';

                        // Email address is provided.
                        if (!empty($user_email))
                        {
                            $regOptions['email'] = $user_email;
                            $regOptions['hide_email'] = 0;
                        }
                        // Email address is not provided.
                        else
                        {
                            $regOptions['email'] = oneall_social_login_create_rand_email_address();
                            $regOptions['hide_email'] = 1;
                        }

                        // We need a unique email address.
                        while (oneall_social_login_get_id_member_for_email_address($regOptions['email']) !== false)
                        {
                            $regOptions['email'] = oneall_social_login_create_rand_email_address();
                            $regOptions['hide_email'] = 1;
                        }

                        // Additional user fields.
                        $regOptions['extra_register_vars']['websiteUrl'] = "'" . addslashes($user_website) . "'";
                        $regOptions['extra_register_vars']['gender'] = $user_gender;
                        $regOptions['extra_register_vars']['location'] = "'" . addslashes($user_location) . "'";
                        $regOptions['extra_register_vars']['realName'] = "'" . addslashes($user_full_name) . "'";

                        // Social Network Avatar
                        if (!isset($modSettings['oasl_settings_use_avatars']) or $modSettings['oasl_settings_use_avatars'] == '1')
                        {
                            if (!empty($user_picture))
                            {
                                $regOptions['extra_register_vars']['avatar'] = "'" . addslashes($user_picture) . "'";
                            }
                        }

                        // We don't need activation.
                        $regOptions['require'] = 'nothing';

                        // Do not check the password strength.
                        $regOptions['check_password_strength'] = false;

                        // Compute a unique username.
                        $regOptions['username'] = $user_login;
                        if (isReservedName($regOptions['username']))
                        {
                            $i = 1;
                            do
                            {
                                $user_login_tmp = $regOptions['username'] . ($i++);
                            } while (isReservedName($user_login_tmp));
                            $regOptions['username'] = $user_login_tmp;
                        }

                        // Cut if username is too long.
                        $regOptions['username'] = substr($regOptions['username'], 0, 25);

                        $modSettings['disableRegisterCheck'] = true;
                        $user_info['is_guest'] = true;

                        // Create a new user account.
                        $id_member = registerMember($regOptions, $err);
                        if (is_numeric($id_member))
                        {
                            // Tie the user_token to the newly created member.
                            oneall_social_login_link_tokens_to_id_member($user_token, $identity_token, $id_member);
                        }
                    }

                    // Login.
                    if (!empty($id_member))
                    {
                        // At this point the user is authenticated
                        if (isset($_SESSION['failed_login']))
                        {
                            unset($_SESSION['failed_login']);
                        }

                        // Read user data.
                        $request = db_query("SELECT passwd, ID_MEMBER, ID_GROUP, lngfile, is_activated, emailAddress, additionalGroups, memberName, passwordSalt FROM {$db_prefix}members WHERE ID_MEMBER = '" . intval($id_member) . "'", __FILE__, __LINE__);
                        $user_settings = mysql_fetch_assoc($request);
                        mysql_free_result($request);

                        if (!empty($user_settings['memberName']))
                        {
                            //This is being used by Login2()
                            $_REQUEST['user'] = $user_settings['memberName'];
                            $_REQUEST['hash_passwrd'] = sha1($user_settings['passwd'] . $sc);

                            // Login.
                            require_once $sourcedir . '/LogInOut.php';
                            Login2();
                        }
                    }
                }
            }
        }
    }

    // Error
    redirectexit();
}

// Launch callback handler.
if (isset($_POST) and !empty($_POST['oa_action']) and !empty($_POST['connection_token']))
{
    if ($_POST['oa_action'] == 'social_login')
    {
        oneall_social_login_login_callback();
        exit();
    }
    elseif ($_POST['oa_action'] == 'social_link')
    {
        oneall_social_login_link_callback();
        exit();
    }
}

// The url has likely be called directly
redirectexit();
