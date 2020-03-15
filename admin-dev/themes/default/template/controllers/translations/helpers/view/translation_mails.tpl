{**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

{extends file="helpers/view/view.tpl"}

{block name="override_tpl"}

	{$tinyMCE}
	{if $mod_security_warning}
	<div class="alert alert-warning">
		{l s='Apache mod_security is activated on your server. This could result in some Bad Request errors' d='Admin.International.Notification'}
	</div>
	{/if}
	{if !empty($limit_warning)}
		<div class="alert alert-warning">
			{if $limit_warning['error_type'] == 'suhosin'}
				{l s='Warning! Your hosting provider is using the Suhosin patch for PHP, which limits the maximum number of fields allowed in a form:' d='Admin.International.Notification'}

      {l s='%limit% for suhosin.post.max_vars.' sprintf=['%limit%' => '<b>'|cat:$limit_warning['post.max_vars']|cat:'</b>'] d='Admin.International.Notification'}<br/>
      {l s='%limit% for suhosin.request.max_vars.' sprintf=['%limit%' => '<b>'|cat:$limit_warning['request.max_vars']|cat:'</b>'] d='Admin.International.Notification'}<br/>
      {l s='Please ask your hosting provider to increase the Suhosin limit to' d='Admin.International.Notification'}
			{else}
				{l s='Warning! Your PHP configuration limits the maximum number of fields allowed in a form:' d='Admin.International.Notification'}<br/>
				<b>{$limit_warning['max_input_vars']}</b> {l s='for max_input_vars.' d='Admin.International.Notification'}<br/>
				{l s='Please ask your hosting provider to increase this limit to' d='Admin.International.Notification'}
			{/if}
			{l s='%s at least, or you will have to edit the translation files.' sprintf=[$limit_warning['needed_limit']] d='Admin.International.Notification'}
		</div>
	{else}
		<form method="post" id="{$table}_form" action="{$url_submit|escape:'html':'UTF-8'}" class="form-horizontal">
			<div class="panel">
				<input type="hidden" name="lang" value="{$lang}" />
				<input type="hidden" name="type" value="{$type}" />
				<input type="hidden" name="selected-theme" value="{$theme}" />
				<script type="text/javascript">
					$(document).ready(function(){
						$('a.useSpecialSyntax').click(function(){
							var syntax = $(this).find('img').attr('alt');
							$('#BoxUseSpecialSyntax .syntax span').html(syntax+".");
						});
					});
				</script>
				<div id="BoxUseSpecialSyntax">
					<div class="alert alert-warning">
						<p>
							{l s='Some of these expressions use this special syntax: %s.' sprintf=['%d'] d='Admin.International.Help'}
							<br />
							{l s='You MUST use this syntax in your translations. Here are several examples:' d='Admin.International.Help'}
						</p>
						<ul>
              <li>"{l s='There are [1]%replace%[/1] products' html=true sprintf=['%replace%' => '%d', '[1]' => '<strong>', '[/1]' => '</strong>'] d='Admin.International.Help'}": {l s='"%s" will be replaced by a number.' sprintf=['%d'] d='Admin.International.Help'}</li>
              <li>"{l s='List of pages in [1]%replace%[/1]' html=true sprintf=['%replace%' => '%s', '[1]' => '<strong>', '[/1]' => '</strong>'] d='Admin.International.Help'}": {l s='"%s" will be replaced by a string.' sprintf=['%s'] d='Admin.International.Help'}</li>
              <li>"{l s='Feature: [1]%1%[/1] ([1]%2%[/1] values)' html=true sprintf=['%1%' => '%1$s', '%2%' => '%2$d', '[1]' => '<strong>', '[/1]' => '</strong>'] d='Admin.International.Help'}": {l s='The numbers enable you to reorder the variables when necessary.' d='Admin.International.Help'}</li>
						</ul>
					</div>
				</div>
				<div id="translation_mails-control-actions" class="panel-footer">
					<a name="submitTranslations{$type|ucfirst}" href="{$cancel_url}" class="btn btn-default">
						<i class="process-icon-cancel"></i> {l s='Cancel' d='Admin.Actions'}
					</a>
					{*$toggle_button*}
					<button type="submit" id="{$table}_form_submit_btn" name="submitTranslations{$type|ucfirst}" class="btn btn-default pull-right">
						<i class="process-icon-save"></i>
						{l s='Save' d='Admin.Actions'}
					</button>
					<button type="submit" id="{$table}_form_submit_btn" name="submitTranslations{$type|ucfirst}AndStay" class="btn btn-default pull-right">
						<i class="process-icon-save"></i>
						{l s='Save and stay' d='Admin.Actions'}
					</button>
				</div>
			</div>
			<div class="panel">
				<h3>
					<i class="icon-envelope"></i>
					{l s='Core emails' d='Admin.International.Feature'}
					<span class="badge">
						<i class="icon-folder"></i>
						mails/{$lang|strtolower}/
					</span>
				</h3>
				{$mail_content}
				{literal}
				<script type="text/javascript">
				//<![CDATA[
					$(document).ready(function () {
						$('.mails_field').on('shown.bs.collapse', function () {
							// get active email
							var active_email = $(this).find('.email-collapse.in');
							// get iframe container for active email
							var frame = active_email.find('.email-html-frame');
							// get source url for active email
							var src = frame.data('email-src');
							// get rte container for active email
							var rte_mail_selector = active_email.find('textarea.rte-mail').data('rte');
							// create special config
							var rte_mail_config = {};
							rte_mail_config['editor_selector'] = 'rte-mail-' + rte_mail_selector;
							rte_mail_config['height'] = '500px';
							rte_mail_config['plugins'] = 'colorpicker link image paste pagebreak table contextmenu filemanager table code media autoresize textcolor anchor fullpage';
							// move controls to active panel
							$('#translation_mails-control-actions').appendTo($(this).find('.panel-collapse.in'));
							// when user first open email
							if (frame.find('iframe.email-frame').length == 0) {
								// load iframe
								frame.append('<iframe class="email-frame" />');
								$.ajax({
									url:'index.php',
									type: 'POST',
									async: false,
									dataType: 'html',
									data: {
										ajax: 1,
										controller: 'AdminTranslations',
										action : 'emailHTML',
										email : src,
										token: window.token
									},
									success: function(result)
									{
										var doc = frame.find('iframe')[0].contentWindow.document;
										doc.open();
										doc.write(result);
										doc.close();
									}
								});

								// init tinyMCE with special config
								tinySetup(rte_mail_config);
							}
						});
					})
				//]]>
				</script>
				{/literal}
			</div>
			<div class="panel">
				<h3>
					<i class="icon-puzzle-piece"></i>
					{l s='Module emails' d='Admin.International.Feature'}
					<span class="badge">
						<i class="icon-folder"></i>
						modules/name_of_module/mails/{$lang|strtolower}/
					</span>
				</h3>
				{foreach $module_mails as $module_name => $mails}
					{$mails['display']}
				{/foreach}
			</div>
		</form>
	{/if}
{/block}
