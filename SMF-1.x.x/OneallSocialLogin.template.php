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
 * Display the settings in the administraton area
 */
function template_oneall_social_login_config ()
{
	global $txt, $context, $scripturl, $modSettings;

	// Read API settings.
	$oasl_api_handler = ((isset ($modSettings ['oasl_api_handler']) AND $modSettings ['oasl_api_handler'] == 'fsockopen') ? 'fsockopen' : 'curl');
	$oasl_api_port = ((isset ($modSettings ['oasl_api_port']) AND $modSettings ['oasl_api_port'] == 80) ? 80 : 443);

	//Available Providers
	$available_providers = array ();
	if (isset ($modSettings ['oasl_providers']))
	{
		$providers = explode (',', trim($modSettings ['oasl_providers']));
		foreach ($providers AS $provider)
		{
			if (strlen (trim ($provider)) > 0)
			{
				$available_providers [] = strtolower ($provider);
			}
		}
	}

	// Read enabled providers.
	$enabled_providers = array ();
	if (isset ($modSettings ['oasl_enabled_providers']))
	{
		$providers = explode (',', trim ($modSettings ['oasl_enabled_providers']));
		foreach ($providers AS $provider)
		{
			if (in_array ($provider, $available_providers))
			{
				$enabled_providers [] = strtolower ($provider);
			}
		}
	}

	?>
		<form method="post" name="creator" id="creator" action="<?php echo $scripturl; ?>?action=oasl" accept-charset="<?php echo $context ['character_set']; ?>">
			<table class="tborder" width="100%" align="center" cellspacing="0" cellpadding="0" border="0">
					<tbody>
						<tr>
							<td style="background-color:#F4FA7D">
								<ul>
									<li><?php echo $txt['oasl_follow_twitter']; ?>;</li>
									<li><?php echo $txt['oasl_read_documentation']; ?>;</li>
									<li><?php echo $txt['oasl_contact_us']; ?>;</li>
									<li><?php echo $txt['oasl_other_plugins']; ?></li>
								</ul>
							</td>
						</tr>
				</tbody>
			</table>
			<br />
			<table class="tborder" width="100%" align="center" cellspacing="0" cellpadding="0" border="0" id="oasl_api_connection_handler">
				<tbody>
					<tr>
						<td>
							<table width="100%" cellspacing="0" cellpadding="4" border="0">
								<tbody>
									<tr class="titlebg">
										<td colspan="2">
											<?php echo $txt['oasl_api_connection_handler']; ?>
										</td>
									</tr>
									<tr class="windowbg2">
										<td rowspan="2" width="30%">
											<strong><?php echo $txt['oasl_api_connection_method']; ?></strong>
										</td>
										<td>
											<input type="radio" id="oasl_api_handler_curl" name="oasl_api_handler" value="curl"<?php echo ($oasl_api_handler <> 'fsockopen' ? ' checked="checked"' : ''); ?> />
											<label for="oasl_api_handler_curl"><?php echo $txt['oasl_api_connection_use_curl']; ?> <strong>(<?php echo $txt['oasl_default']; ?>)</strong></label>
											<div class="smalltext"><?php echo $txt['oasl_api_connection_use_curl_desc']; ?></div>
										</td>
									</tr>
									<tr class="windowbg2">
										<td>
											<input type="radio" id="oasl_api_handler_fsockopen" name="oasl_api_handler" value="fsockopen"<?php echo ($oasl_api_handler == 'fsockopen' ? ' checked="checked"' : ''); ?> />
											<label for="oasl_api_handler_fsockopen"><?php echo $txt['oasl_api_connection_use_fsockopen']; ?></label>
											<div class="smalltext"><?php echo $txt['oasl_api_connection_use_fsockopen_desc']; ?></div>
										</td>
									</tr>
									<tr class="windowbg2">
										<td rowspan="2">
												<strong><?php echo $txt['oasl_api_connection_port']; ?></strong>
										</td>
										<td>
											<input type="radio" id="oasl_api_port_443" name="oasl_api_port" value="443"<?php echo ($oasl_api_port <> 80 ? ' checked="checked"' : ''); ?> />
											<label for="oasl_api_port_443"><?php echo $txt['oasl_api_connection_port_443']; ?> <strong>(<?php echo $txt['oasl_default']; ?>)</strong></label>
											<div class="smalltext"><?php echo $txt['oasl_api_connection_port_443_desc']; ?></div>
										</td>
									</tr>
									<tr class="windowbg2">
										<td>
											<input type="radio" id="oasl_api_port_80" name="oasl_api_port" value="80"<?php echo ($oasl_api_port == 80 ? ' checked="checked"' : ''); ?> />
											<label for="oasl_api_port_80"><?php echo $txt['oasl_api_connection_port_80']; ?></label>
											<div class="smalltext"><?php echo $txt['oasl_api_connection_port_80_desc']; ?></div>
										</td>
									</tr>
									<tr class="windowbg2">
										<td colspan="2">
											<input type="button" class="button_submit" id="oasl_autodetect_button" value="<?php echo $txt['oasl_api_connection_autodetect']; ?>" />
										</td>
									</tr>
									<tr class="windowbg2">
										<?php
											if ( ! empty ($_REQUEST['oasl_action']) AND $_REQUEST['oasl_action'] == 'autodetect')
											{
												if ( ! empty ($_REQUEST['oasl_status']))
												{
													if ($_REQUEST['oasl_status'] == 'success')
													{
														?>
															<td colspan="2" style="background-color:green;color:white;font-weight:bold;"><?php echo $txt['oasl_api_connection_autodetect_success']; ?></td>
														<?php
													}
													elseif ($_REQUEST['oasl_status'] == 'error')
													{
														?>
															<td colspan="2" style="background-color:red;color:white;font-weight:bold;"><?php echo $txt['oasl_api_connection_autodetect_error']; ?></td>
														<?php
													}
												}
											}
										?>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			<br />
			<table class="tborder" width="100%" align="center" cellspacing="0" cellpadding="0" border="0" id="oasl_api_settings">
				<tbody>
					<tr>
						<td>
							<table width="100%" cellspacing="0" cellpadding="4" border="0">
								<tbody>
									<tr class="titlebg">
										<td colspan="2">
											<strong><?php echo $txt['oasl_api_settings']; ?></strong>
										</td>
									</tr>
									<tr class="windowbg2">
										<td colspan="2" style="background-color:#F4FA7D">
											<?php echo $txt['oasl_api_credentials']; ?> <a href="https://app.oneall.com/applications/" target="_blank"><?php echo $txt['oasl_api_credentials_get']; ?></a>
										</td>
									</tr>
									<tr class="windowbg2">
										<td>
											<label for="oasl_api_subdomain"><strong><?php echo $txt['oasl_api_subdomain']; ?></strong></label>
										</td>
										<td>
											<input type="text" id="oasl_api_subdomain" name="oasl_api_subdomain" size="50" value="<?php echo htmlspecialchars ($modSettings ['oasl_api_subdomain']); ?>" />
										</td>
									</tr>
									<tr class="windowbg2">
										<td>
											<label for="oasl_api_key"><strong><?php echo $txt['oasl_api_public_key']; ?></strong></label>
										</td>
										<td>
											<input type="text" id="oasl_api_key" name="oasl_api_key" size="50" value="<?php echo htmlspecialchars ($modSettings ['oasl_api_key']); ?>" />
										</td>
									</tr>
									<tr class="windowbg2">
										<td>
											<label for="oasl_api_secret"><strong><?php echo $txt['oasl_api_private_key']; ?></strong></label>
										</td>
										<td>
											<input type="text" id="oasl_api_secret" name="oasl_api_secret" size="50" value="<?php echo htmlspecialchars ($modSettings ['oasl_api_secret']); ?>" />
										</td>
									</tr>
									<tr class="windowbg2">
										<td colspan="2">
											<input type="button" class="button_submit" id="oasl_verify_button" value="<?php echo $txt['oasl_api_verify']; ?>" />
										</td>
									</tr>
									<tr class="windowbg2">
										<?php
											if ( ! empty ($_REQUEST['oasl_action']) AND $_REQUEST['oasl_action'] == 'verify')
											{
												if ( ! empty ($_REQUEST['oasl_status']))
												{
													$status_is_error = true;
													$status_message = null;

													switch ($_REQUEST['oasl_status'])
													{
														case 'success':
															$status_is_error = false;
															$status_message = $txt['oasl_api_verify_success'];
														break;

														case 'error_not_all_fields_filled_out':
															$status_message = $txt['oasl_api_verify_missing'];
														break;

														case 'error_communication':
														case 'error_selected_handler_faulty':
															$status_message = $txt['oasl_api_verify_error_handler'];
														break;

														case 'error_subdomain_wrong':
															$status_message = $txt['oasl_api_verify_error_subdomain'];
														break;

														case 'error_subdomain_wrong_syntax':
															$status_message = $txt['oasl_api_verify_error_syntax'];
														break;

														case 'error_authentication_credentials_wrong':
															$status_message = $txt['oasl_api_verify_error_keys'];
														break;
													}

													if ( ! empty ($status_message))
													{
														if ($status_is_error)
														{
															?>
																<td colspan="2" style="background-color:red;color:white;font-weight:bold;"><?php echo $status_message; ?></td>
															<?php
														}
														else
														{
															?>
																<td colspan="2" style="background-color:green;color:white;font-weight:bold;"><?php echo $status_message; ?></td>
															<?php
														}
													}
												}
											}
										?>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			<br />
			<table class="tborder" width="100%" align="center" cellspacing="0" cellpadding="0" border="0">
				<tbody>
					<tr>
						<td>
							<table width="100%" cellspacing="0" cellpadding="4" border="0">
								<tbody>
									<tr class="titlebg">
										<td colspan="2">
											<strong><?php echo $txt['oasl_enable_networks']; ?></strong>
										</td>
									</tr>
									<?php
										foreach ($available_providers AS $provider)
										{
											$provider_enabled = (in_array ($provider, $enabled_providers));
											?>
												<tr class="windowbg2">
													<td>
														<label for="oasl_provider_<?php echo $provider; ?>"><strong><?php echo ucwords (strtolower ($provider)); ?></strong></label>
													</td>
													<td>
														<input type="checkbox" id="oasl_provider_<?php echo $provider; ?>" name="oasl_enabled_providers[]" value="<?php echo $provider; ?>"<?php echo ($provider_enabled ? ' checked="checked"' : ''); ?> />
														<label for="oasl_provider_<?php echo $provider; ?>"><?php echo $txt['oasl_enable']; ?> <strong><?php echo ucwords (strtolower ($provider)); ?></strong></label>
													</td>
												</tr>
											<?php
										}
									?>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			<br />
			<table class="tborder" width="100%" align="center" cellspacing="0" cellpadding="0" border="0">
				<tbody>
					<tr>
						<td>
							<table width="100%" cellspacing="0" cellpadding="4" border="0">
								<tbody>
									<tr class="titlebg">
										<td colspan="2">
											<strong><?php echo $txt['oasl_settings']; ?></strong>
										</td>
									</tr>
									<tr class="windowbg2">
										<td>
											<label for="oasl_settings_login_caption"><strong><?php echo $txt['oasl_settings_login_text']; ?></strong></label>
										</td>
										<td>
											<input type="text" id="oasl_settings_login_caption" name="oasl_settings_login_caption" size="50" value="<?php echo htmlspecialchars ($modSettings ['oasl_settings_login_caption']); ?>" />
										</td>
									</tr>
									<tr class="windowbg2">
										<td>
											<label for="oasl_settings_registration_caption"><strong><?php echo $txt['oasl_settings_register_text']; ?></strong></label>
										</td>
										<td>
											<input type="text" id="oasl_settings_registration_caption" name="oasl_settings_registration_caption" size="50" value="<?php echo htmlspecialchars ($modSettings ['oasl_settings_registration_caption']); ?>" />
										</td>
									</tr>

									<tr class="windowbg2">
										<td>
											<label for="oasl_settings_profile_caption"><strong><?php echo $txt['oasl_settings_profile_text']; ?></strong></label>
										</td>
										<td>
											<input type="text" id="oasl_settings_profile_caption" name="oasl_settings_profile_caption" size="50" value="<?php echo htmlspecialchars ($modSettings ['oasl_settings_profile_caption']); ?>" />
										</td>
									</tr>
									<tr class="windowbg2">
										<td>
											<label for="oasl_settings_profile_desc"><strong><?php echo $txt['oasl_settings_profile_desc']; ?></strong></label>
										</td>
										<td>
											<input type="text" id="oasl_settings_profile_desc" name="oasl_settings_profile_desc" size="50" value="<?php echo htmlspecialchars ($modSettings ['oasl_settings_profile_desc']); ?>" />
										</td>
									</tr>

									<tr class="windowbg2">
										<td class="windowbg2" colspan="2">
											<hr width="100%" size="1" class="hrcolor" />
										</td>
									</tr>
									<tr class="windowbg2">
										<td>
											<strong><?php echo $txt['oasl_settings_social_avatar']; ?></strong><br />
											<span class="smalltext"><?php echo $txt['oasl_settings_social_avatar_desc']; ?></span>
										</td>
										<td>
											<input type="checkbox" id="oasl_settings_use_avatars" name="oasl_settings_use_avatars" value="1"<?php echo ( ! empty ($modSettings ['oasl_settings_use_avatars']) ? ' checked="checked"' : ''); ?> />
											<label for="oasl_settings_use_avatars"><?php echo $txt['oasl_settings_social_avatar_yes']; ?></label>
										</td>
									</tr>
									<tr class="windowbg2">
										<td class="windowbg2" colspan="2">
											<hr width="100%" size="1" class="hrcolor" />
										</td>
									</tr>
									<tr class="windowbg2">
										<td>
											<strong><?php echo $txt['oasl_settings_social_link']; ?></strong><br />
											<span class="smalltext"><?php echo $txt['oasl_settings_social_link_desc']; ?></span>
										</td>
										<td>
											<input type="checkbox" id="oasl_settings_link_accounts" name="oasl_settings_link_accounts" value="1"<?php echo ( ! empty ($modSettings ['oasl_settings_link_accounts']) ? ' checked="checked"' : ''); ?> />
											<label for="oasl_settings_link_accounts"><?php echo $txt['oasl_settings_social_link_yes']; ?></label>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			<br />
			<table width="100%" align="center" cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td align="center">
						<input type="submit" class="button_submit" value="<?php echo $txt['oasl_save_settings']; ?>" />
						<input type="hidden" name="sc" value="<?php echo $context ['session_id']; ?>" />
						<input type="hidden" id="oasl_sa" name="sa" value="save" />
					</td>
				</tr>
			</table>
		</form>
		<script type="text/javascript"><!-- // --><![CDATA[
			var oasl_button, oasl_form, oasl_sa;

			oasl_sa = document.getElementById('oasl_sa');
			oasl_form = document.getElementById('creator');

			oasl_button = document.getElementById('oasl_autodetect_button');
			oasl_button.onclick = function () {
				oasl_sa.value = 'autodetect';
				oasl_form.submit();
			};

			oasl_button = document.getElementById('oasl_verify_button');
			oasl_button.onclick = function () {
				oasl_sa.value = 'verify';
				oasl_form.submit();
			};
		// ]]></script>
 	<?php
}