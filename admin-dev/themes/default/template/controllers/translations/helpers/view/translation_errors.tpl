{*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helpers/view/view.tpl"}

{block name="override_tpl"}

	<p>
		{l s='Expressions to translate'} : <b>{$count}</b>.<br />
		{l s='Total missing expresssions:'} {$missing_translations}<br />
	</p>

	{if $post_limit_exceeded}
	<div class="warn">
		{if $limit_warning['error_type'] == 'suhosin'}
			{l s='Warning, your hosting provider is using the suhosin patch for PHP, which limits the maximum number of fields to post in a form:'}

			<b>{$limit_warning['post.max_vars']}</b>{l s='for suhosin.post.max_vars.'}<br/>
			<b>{$limit_warning['request.max_vars']}</b> {l s='for suhosin.request.max_vars.'}<br/>
			{l s='Please ask your hosting provider to increase the suhosin post and request a limit of'}
		{else}
			{l s='Warning, your PHP configuration limits the maximum number of fields to post in a form:'}<br/>
			<b>{$limit_warning['max_input_vars']}</b> {l s='for max_input_vars.'}<br/>
			{l s='Please ask your hosting provider to increase the this limit to'}
		{/if}
		<u><b>{$limit_warning['needed_limit']}</b></u> {l s='at least.'} {l s='or edit the translation file manually.'}
	</div>
	{else}
		<form method="post" id="{$table}_form" action="{$url_submit}" class="form">
			{*{$auto_translate}$*}
			<input type="hidden" name="lang" value="{$lang}" />
			<input type="hidden" name="type" value="{$type}" />
			<input type="hidden" name="theme" value="{$theme}" />
			<input type="submit" id="{$table}_form_submit_btn" name="submitTranslations{$type|ucfirst}" value="{l s='Update translations'}" class="button" />
			<br /><br />
			<table cellpadding="0" cellspacing="0" class="table">
			{foreach $errorsArray as $key => $value}
				<tr {if empty($value)}style="background-color:#FBB"{else}{cycle values='class="alt_row",'}{/if}>
					<td>{$key|stripslashes}</td>
					<td style="width: 430px">= <input type="text" name="{$key|md5}" value="{$value|regex_replace:'#"#':'&quot;'|stripslashes}" style="width: 380px"></td>
				</tr>
			{/foreach}
			</table>
		</form>
	{/if}

{/block}
