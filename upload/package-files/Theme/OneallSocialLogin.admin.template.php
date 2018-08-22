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
if (!defined('SMF'))
{
	die('You are not allowed to access this file directly');
}

/**
 * Display the settings in the administraton area
 */
function template_oneall_social_login_config ()
{
	global $txt, $context, $scripturl, $modSettings;

	?>
		<div class="oasl_info_box information">
			<ul>
				<li><?php echo $txt['oasl_follow_twitter']; ?>;</li>
				<li><?php echo $txt['oasl_read_documentation']; ?>;</li>
				<li><?php echo $txt['oasl_contact_us']; ?>;</li>
				<li><?php echo $txt['oasl_other_plugins']; ?></li>
			</ul>
		</div>
		<form method="post" name="creator" id="creator" action="<?php echo $scripturl; ?>?action=admin;area=oasl" accept-charset="<?php echo $context['character_set']; ?>">
			<div class="cat_bar" id="oasl_api_connection_handler">
				<h3 class="catbg">
					<span class="ie6_header floatleft"><?php echo $txt['oasl_api_connection_handler']; ?></span>
				</h3>
			</div>
			<div class="windowbg2">
				<span class="topslice"><span></span></span>
				<div class="content">
					<dl>
						<dt>
							<strong><?php echo $txt['oasl_api_connection_method']; ?></strong>
						</dt>
						<dd>
							<input type="radio" id="oasl_api_handler_curl" name="oasl_api_handler" value="use_curl"<?php echo ($modSettings['oasl_api_handler'] <> 'fsockopen' ? ' checked="checked"' : ''); ?> />
							<label for="oasl_api_handler_curl"><?php echo $txt['oasl_api_connection_use_curl']; ?> <strong>(<?php echo $txt['oasl_default']; ?>)</strong></label>
							<div class="description"><?php echo $txt['oasl_api_connection_use_curl_desc']; ?></div>
						</dd>
						<dt>&nbsp;</dt>
						<dd>
							<input type="radio" id="oasl_api_handler_fsockopen" name="oasl_api_handler" value="use_fsockopen"<?php echo ($modSettings['oasl_api_handler'] == 'fsockopen' ? ' checked="checked"' : ''); ?> />
							<label for="oasl_api_handler_fsockopen"><?php echo $txt['oasl_api_connection_use_fsockopen']; ?></label>
							<div class="description"><?php echo $txt['oasl_api_connection_use_fsockopen_desc']; ?></div>
						</dd>
					</dl>
					<hr class="hrcolor clear" />
					<dl>
						<dt>
							<strong><?php echo $txt['oasl_api_connection_port']; ?></strong>
						</dt>
						<dd>
							<input type="radio" id="oasl_api_port_443" name="oasl_api_port" value="443"<?php echo ($modSettings['oasl_api_port'] <> 80 ? ' checked="checked"' : ''); ?> />
							<label for="oasl_api_port_443"><?php echo $txt['oasl_api_connection_port_443']; ?> <strong>(<?php echo $txt['oasl_default']; ?>)</strong></label>
							<div class="description"><?php echo $txt['oasl_api_connection_port_443_desc']; ?></div>
						</dd>
						<dt>&nbsp;</dt>
						<dd>
							<input type="radio" id="oasl_api_port_80" name="oasl_api_port" value="80"<?php echo ($modSettings['oasl_api_port'] == 80 ? ' checked="checked"' : ''); ?> />
							<label for="oasl_api_port_80"><?php echo $txt['oasl_api_connection_port_80']; ?></label>
							<div class="description"><?php echo $txt['oasl_api_connection_port_80_desc']; ?></div>
						</dd>
					</dl>
				</div>
				<div>
					<input type="button" class="oasl_button button_submit" id="oasl_autodetect_button" value="<?php echo $txt['oasl_api_connection_autodetect']; ?>" />
					<?php
						if ($modSettings['oasl_action'] == 'autodetect')
						{
							switch ($modSettings['oasl_status'])
							{
								case 'success':
									echo '<span class="oasl_success_message">' . $txt['oasl_api_connection_autodetect_success'] . '</span>';
								break;

								case 'error':
									echo '<span class="oasl_error_message">' . $txt['oasl_api_connection_autodetect_error'] . '</span>';
								break;
							}
						}
						else
						{
							echo '<span class="oasl_info_message">' . $txt['oasl_api_connection_autodetect_wait'] . '</span>';
						}
					?>
				</div>
				<span class="botslice"><span></span></span>
			</div>
			<div class="cat_bar" id="oasl_api_settings">
				<h3 class="catbg">
					<span class="ie6_header floatleft"><?php echo $txt['oasl_api_settings']; ?></span>
				</h3>
			</div>
			<div class="windowbg2">
				<div class="oasl_info_box information">
					<?php echo $txt['oasl_api_credentials']; ?> <a href="https://app.oneall.com/applications/" target="_blank"><?php echo $txt['oasl_api_credentials_get']; ?></a>
				</div>
				<span class="topslice"><span></span></span>
				<div class="content">
					<dl>
						<dt>
							<label for="oasl_api_subdomain"><strong><?php echo $txt['oasl_api_subdomain']; ?></strong></label>
						</dt>
						<dd>
							<input type="text" id="oasl_api_subdomain" name="oasl_api_subdomain" size="50" value="<?php echo htmlspecialchars($modSettings['oasl_api_subdomain']); ?>" />
						</dd>
						<dt>
							<label for="oasl_api_key"><strong><?php echo $txt['oasl_api_public_key']; ?></strong></label>
						</dt>
						<dd>
							<input type="text" id="oasl_api_key" name="oasl_api_key" size="50" value="<?php echo htmlspecialchars($modSettings['oasl_api_key']); ?>" />
						</dd>
						<dt>
							<label for="oasl_api_secret"><strong><?php echo $txt['oasl_api_private_key']; ?></strong></label>
						</dt>
						<dd>
							<input type="text" id="oasl_api_secret" name="oasl_api_secret" size="50" value="<?php echo htmlspecialchars($modSettings['oasl_api_secret']); ?>" />
						</dd>
					</dl>
				</div>
				<div>
					<input type="button" class="oasl_button button_submit" id="oasl_verify_button" value="<?php echo $txt['oasl_api_verify']; ?>" />
					<?php
						if ($modSettings['oasl_action'] == 'verify')
						{
							switch ($modSettings['oasl_status'])
							{
								case 'success':
									echo '<span class="oasl_success_message">' . $txt['oasl_api_verify_success'] . '</span>';
								break;

								case 'error_not_all_fields_filled_out':
									echo '<span class="oasl_error_message">' . $txt['oasl_api_verify_missing'] . '</span>';
								break;

								case 'error_communication':
								case 'error_selected_handler_faulty':
									echo '<span class="oasl_error_message">' . $txt['oasl_api_verify_error_handler'] . '</span>';
								break;

								case 'error_subdomain_wrong':
									echo '<span class="oasl_error_message">' . $txt['oasl_api_verify_error_subdomain'] . '</span>';
								break;

								case 'error_subdomain_wrong_syntax':
									echo '<span class="oasl_error_message">' . $txt['oasl_api_verify_error_syntax'] . '</span>';
								break;

								case 'error_authentication_credentials_wrong':
									echo '<span class="oasl_error_message">' . $txt['oasl_api_verify_error_keys'] . '</span>';
								break;
							}
						}
						else
						{
							echo '<span class="oasl_info_message">' . $txt['oasl_api_verify_wait'] . '</span>';
						}
						?>
				</div>
				<span class="botslice"><span></span></span>
			</div>
			<div class="cat_bar">
				<h3 class="catbg">
					<span class="ie6_header floatleft"><?php echo $txt['oasl_enable_networks']; ?></span>
				</h3>
			</div>
			<div class="windowbg2">
				<span class="topslice"><span></span></span>
				<div class="content">
					<dl>
						<?php
							foreach ($modSettings['oasl_providers'] AS $provider)
							{
								echo '
									<dt class="oasl_provider_row">
										<label for="oasl_provider_' . $provider . '"><span class="oasl_provider oasl_provider_' . $provider . '">' . ucwords(strtolower($provider)) . '</span></label>
										<input type="checkbox" id="oasl_provider_ ' . $provider . '" name="oasl_enabled_providers[]" value="' . $provider . '"' . ((in_array($provider, $modSettings['oasl_enabled_providers'])) ? 'checked="checked"' : '') .' />
										<label for="oasl_provider_' . $provider . '">' . $txt['oasl_enable'] . ' <strong>' . ucwords(strtolower($provider)) . '</strong></label>
									</dd>
									<dd>&nbsp;</dd>';
							}
						?>
					</dl>
				</div>
				<span class="botslice"><span></span></span>
			</div>
			<div class="cat_bar">
				<h3 class="catbg">
					<span class="ie6_header floatleft"><?php echo $txt['oasl_settings']; ?></span>
				</h3>
			</div>
			<div class="windowbg2">
				<span class="topslice"><span></span></span>
				<div class="content">
					<dl>
						<dt>
							<label for="oasl_settings_login_caption"><strong><?php echo $txt['oasl_settings_login_text']; ?></strong></label>
						</dt>
						<dd>
							<input type="text" id="oasl_settings_login_caption" name="oasl_settings_login_caption" size="50" value="<?php echo htmlspecialchars($modSettings['oasl_settings_login_caption']); ?>" />
						</dd>
						<dt>
							<label for="oasl_settings_registration_caption"><strong><?php echo $txt['oasl_settings_register_text']; ?></strong></label>
						</dt>
						<dd>
							<input type="text" id="oasl_settings_registration_caption" name="oasl_settings_registration_caption" size="50" value="<?php echo htmlspecialchars($modSettings['oasl_settings_registration_caption']); ?>" />
						</dd>

						<dt>
							<label for="oasl_settings_profile_caption"><strong><?php echo $txt['oasl_settings_profile_text']; ?></strong></label>
						</dt>
						<dd>
							<input type="text" id="oasl_settings_profile_caption" name="oasl_settings_profile_caption" size="50" value="<?php echo htmlspecialchars($modSettings['oasl_settings_profile_caption']); ?>" />
						</dd>
						<dt>
							<label for="oasl_settings_profile_desc"><strong><?php echo $txt['oasl_settings_profile_desc']; ?></strong></label>
						</dt>
						<dd>
							<input type="text" id="oasl_settings_profile_desc" name="oasl_settings_profile_desc" size="50" value="<?php echo htmlspecialchars($modSettings['oasl_settings_profile_desc']); ?>" />
						</dd>
					</dl>

					<hr class="hrcolor clear" />
					<dl>
						<dt>
							<strong><?php echo $txt['oasl_settings_social_avatar']; ?></strong><br />
							<span class="smalltext"><?php echo $txt['oasl_settings_social_avatar_desc']; ?></span>
						</dt>
						<dd>
							<input type="checkbox" id="oasl_settings_use_avatars" name="oasl_settings_use_avatars" value="1"<?php echo (!empty($modSettings['oasl_settings_use_avatars']) ? ' checked="checked"' : ''); ?> />
							<label for="oasl_settings_use_avatars"><?php echo $txt['oasl_settings_social_avatar_yes']; ?></label>
						</dd>
					</dl>

					<hr class="hrcolor clear" />
					<dl>
						<dt>
							<strong><?php echo $txt['oasl_settings_social_avatar_upload']; ?></strong><br />
							<span class="smalltext"><?php echo $txt['oasl_settings_social_avatar_upload_desc']; ?></span>
						</dt>
						<dd>
							<input type="checkbox" id="oasl_settings_upload_avatars" name="oasl_settings_upload_avatars" value="1"<?php echo (!empty($modSettings['oasl_settings_upload_avatars']) ? ' checked="checked"' : ''); ?> />
							<label for="oasl_settings_upload_avatars"><?php echo $txt['oasl_settings_social_avatar_upload_yes']; ?></label>
						</dd>
					</dl>

					<hr class="hrcolor clear" />
					<dl>
						<dt>
							<strong><?php echo $txt['oasl_settings_allow_login_new']; ?></strong><br />
							<span class="smalltext"><?php echo $txt['oasl_settings_allow_login_new_desc']; ?></span>
						</dt>
						<dd>
							<input type="checkbox" id="oasl_settings_login_allow_new" name="oasl_settings_login_allow_new" value="1"<?php echo (!empty($modSettings['oasl_settings_login_allow_new']) ? ' checked="checked"' : ''); ?> />
							<label for="oasl_settings_login_allow_new"><?php echo $txt['oasl_settings_allow_login_new_yes']; ?></label>
						</dd>
					</dl>


					<hr class="hrcolor clear" />
					<dl>
						<dt>
							<strong><?php echo $txt['oasl_settings_reg_method']; ?></strong><br />
							<span class="smalltext"><?php echo $txt['oasl_settings_reg_method_desc']; ?></span>
						</dt>
						<dd>
							<select id="oasl_settings_reg_method" name="oasl_settings_reg_method">
								<?php
									$reg_methods = array ('auto', 'system', 'email', 'admin', 'disable');

									if (empty ($modSettings['oasl_settings_reg_method']) || ! in_array ($modSettings['oasl_settings_reg_method'], $reg_methods))
									{
										$modSettings['oasl_settings_reg_method'] = 'auto';
									}

									foreach ($reg_methods AS $reg_method)
									{
										?>
											<option value="<?php echo $reg_method; ?>"<?php echo (($modSettings['oasl_settings_reg_method'] == $reg_method) ? ' selected="selected"' : ''); ?>><?php echo $txt['oasl_settings_reg_method_' . $reg_method]; ?></option>
										<?php
									}
								?>
							</select>
						</dd>
					</dl>

					<hr class="hrcolor clear" />
					<dl>
						<dt>
							<strong><?php echo $txt['oasl_settings_social_link']; ?></strong><br />
							<span class="smalltext"><?php echo $txt['oasl_settings_social_link_desc']; ?></span>
						</dt>
						<dd>
							<input type="checkbox" id="oasl_settings_link_accounts" name="oasl_settings_link_accounts" value="1"<?php echo (!empty($modSettings['oasl_settings_link_accounts']) ? ' checked="checked"' : ''); ?> />
							<label for="oasl_settings_link_accounts"><?php echo $txt['oasl_settings_social_link_yes']; ?></label>
						</dd>
					</dl>

					<hr class="hrcolor clear" />
					<dl>
						<dt>
							<strong><?php echo $txt['oasl_settings_ask_for_email']; ?></strong><br />
							<span class="smalltext"><?php echo $txt['oasl_settings_ask_for_email_desc']; ?></span>
						</dt>
						<dd>
							<select id="oasl_settings_ask_for_email" name="oasl_settings_ask_for_email">
								<option value="0"<?php echo (empty($modSettings['oasl_settings_ask_for_email']) ? ' selected="selected"' : ''); ?>><?php echo $txt['oasl_settings_ask_for_email_no']; ?></option>
								<option value="1"<?php echo (!empty($modSettings['oasl_settings_ask_for_email']) ? ' selected="selected"' : ''); ?>><?php echo $txt['oasl_settings_ask_for_email_yes']; ?></option>
							</select>
						</dd>
					</dl>

				</div>
				<span class="botslice"><span></span></span>
			</div>
			<hr class="hrcolor clear" />
			<div class="righttext">
				<input type="submit" class="button_submit" value="<?php echo $txt['oasl_save_settings']; ?>" />
				<input type="hidden" name="sc" value="<?php echo $context['session_id']; ?>" />
				<input type="hidden" id="oasl_sa" name="sa" value="save" />
			</div>
		</form>
		<script type="text/javascript">
			<!--
				oasl_config_init ();
			//-->
		</script>
 	<?php
}
