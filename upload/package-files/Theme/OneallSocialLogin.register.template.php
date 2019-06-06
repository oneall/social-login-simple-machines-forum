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
if (!defined('SMF'))
{
    die('You are not allowed to access this file directly');
}

// Request addition fields during the social login registration
function template_oneall_social_login_registration()
{
    global $context, $settings, $options, $scripturl, $txt, $modSettings;

    // Any errors?
    if (!empty($context['oasl_registration_errors']) and is_array($context['oasl_registration_errors']))
    {
        echo '
					<div class="register_error">
						<span>' . $txt['oasl_register_errors'] . '</span>
						<ul class="reset">
							<li>' . implode($context['oasl_registration_errors'], '</li><il>') . '</li>
						</ul>
					</div>';
    }

    ?>
  	<form action="<?php echo $scripturl; ?>?action=oasl_registration" method="post" accept-charset="<?php echo $context['character_set']; ?>">
			<div class="cat_bar">
				<h3 class="catbg"><?php echo str_replace('{provider}', $modSettings['oasl_provider'], $txt['oasl_register_connected']); ?></h3>
			</div>
			<span class="upperframe"><span></span></span>
			<div class="roundframe">
				<p><?php echo str_replace('{provider}', $modSettings['oasl_provider'], $txt['oasl_register_complete_profile']); ?></p>
			</div>
			<span class="lowerframe"><span></span></span>
			<div class="windowbg2">
				<span class="topslice"><span></span></span>
				<fieldset class="content">
					<dl class="register_form">
						<dt>
							<strong><label for="email_address"><?php echo $txt['oasl_register_email']; ?>:</label></strong>
						</dt>
						<dd>
							<input type="text" name="email_address" size="30" id="email_address" tabindex="<?php echo ($context['tabindex']++); ?>" value="<?php echo !empty($modSettings['email_address']) ? htmlspecialchars($modSettings['email_address']) : ''; ?>" class="input_text" />
						</dd>
						<dt>
							<strong><label for="public_email_address"><?php echo $txt['oasl_register_email_public']; ?>:</label></strong>
						</dt>
						<dd>
							<input type="checkbox" name="public_email_address" id="public_email_address" value="1" tabindex="<?php echo ($context['tabindex']++); ?>" class="input_check" <?php echo !empty($modSettings['public_email_address']) ? 'checked="checked"' : ''; ?>/>
						</dd>
					</dl>
				</fieldset>
				<span class="botslice"><span></span></span>
			</div>
			<div id="confirm_buttons">
				<input type="submit" name="confirm_information" value="<?php echo $txt['oasl_register_confirm']; ?>" class="button_submit" />
				<input type="hidden" name="sc" value="<?php echo $context['session_id']; ?>" />
				<input type="hidden" name="sa" value="confirm" />
			</div>
		</form>
	<?php
}
