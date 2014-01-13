{*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helpers/view/view.tpl"}

{block name="override_tpl"}

	{$tinyMCE}

	{if !empty($limit_warning)}
		<div class="alert alert-warning">
			{if $limit_warning['error_type'] == 'suhosin'}
				{l s='Warning! Your hosting provider is using the Suhosin patch for PHP, which limits the maximum number of fields allowed in a form:'}

				<b>{$limit_warning['post.max_vars']}</b> {l s='for suhosin.post.max_vars.'}<br/>
				<b>{$limit_warning['request.max_vars']}</b> {l s='for suhosin.request.max_vars.'}<br/>
				{l s='Please ask your hosting provider to increase the Suhosin limit to'}
			{else}
				{l s='Warning! Your PHP configuration limits the maximum number of fields allowed in a form:'}<br/>
				<b>{$limit_warning['max_input_vars']}</b> {l s='for max_input_vars.'}<br/>
				{l s='Please ask your hosting provider to increase this limit to'}
			{/if}
			{l s='%s at least, or you will have to edit the translation files.' sprintf=$limit_warning['needed_limit']}
		</div>
	{else}
		<div class="alert alert-info">
			<p>
				{l s='Some sentences to translate use this syntax: "You have %%s items...". These "%s" are variables, and PrestaShop takes care of replacing them before displaying your translation.' sprintf='%d, %s, %1$s, %2$d'}<br>
				<strong>{l s='You must leave these in your translations, and place them appropriately in your sentence.'}</strong>
			</p>
			<p>
				{l s='Click on titles to open fieldsets'}.
			</p>
		</div>
		<form method="post" id="{$table}_form" action="{$url_submit}" class="form-horizontal">
			<div class="panel">
				<input type="hidden" name="lang" value="{$lang}" />
				<input type="hidden" name="type" value="{$type}" />
				<input type="hidden" name="theme" value="{$theme}" />
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
							{l s='Some of these expressions use this special syntax:'} <strong>%d</strong><br><br>
							{l s='You MUST use this syntax in your translations. Here are several examples:'}
						</p>
						<ul>
							<li>"There are <strong>%d</strong> products": "<strong>%d</strong>" {l s='will be replaced by a number.'}).</li>
							<li>"List of pages in <strong>%s</strong>": "<strong>%s</strong>" {l s='will be replaced by a string.'}).</li>
							<li>"Feature: <strong>%1$s</strong> (<strong>%2$d</strong> values)": "<strong>n$</strong>" {l s='helps you reorder the arguments when necessary.'}).</li>
						</ul>
					</div>
				</div>
				<div class="panel-footer">
					<a name="submitTranslations{$type|ucfirst}" href="{$cancel_url}" class="btn btn-default">
						<i class="process-icon-cancel"></i> {l s='Cancel'}
					</a>
					{*$toggle_button*}
					<button type="submit" id="{$table}_form_submit_btn" name="submitTranslations{$type|ucfirst}" class="btn btn-default pull-right">
						<i class="process-icon-save"></i>
						{l s='Save'}
					</button>
					<button type="submit" id="{$table}_form_submit_btn" name="submitTranslations{$type|ucfirst}AndStay" class="btn btn-default pull-right">
						<i class="process-icon-save"></i>
						{l s='Save and stay'}
					</button>
				</div>
			</div>
			<div class="panel">
				<h3>
					<i class="icon-envelope"></i>
					{l s='Core emails'}
					<span class="badge">
						<i class="icon-folder"></i>
						mails/{$lang|strtolower}/
					</span>
				</h3>
				{$mail_content}
			</div>
			<div class="panel">
				<h3>
					<i class="icon-envelope"></i>
					{l s='Module emails'}
					<span class="badge">
						<i class="icon-folder"></i>
						modules/name_of_module/mails/{$lang|strtolower}/
					</span>
				</h3>
				{*foreach $module_mails as $module_name => $mails}
					{$mails['display']}
				{/foreach*}
			</div>
		</form>
	{/if}
{/block}
