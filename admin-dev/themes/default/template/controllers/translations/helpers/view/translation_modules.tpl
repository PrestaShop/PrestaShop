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

	<div class="hint" style="display:block;">
		<ul style="margin-left:30px;list-style-type:disc;">
			<li>{l s='Click on the titles to open fieldsets'}.</li>
			<li>{l s='Some sentences to translate uses this syntax: %s...: You must let it in your translations.' sprintf='%d, %s, %1$s, %2$d'}</li>
		</ul>
	</div><br /><br />

	<p>
		{l s='Expressions to translate: %d.' sprintf=$count}<br />
		{l s='Total missing expresssions: %d.' sprintf=$missing_translations}<br />
	</p>

	{if $post_limit_exceeded}
	<div class="warn">
		{if $limit_warning['error_type'] == 'suhosin'}
			{l s='Warning, your hosting provider is using the suhosin patch for PHP, which limits the maximum number of fields to post in a form:'}

			<b>{$limit_warning['post.max_vars']}</b> {l s='for suhosin.post.max_vars.'}<br/>
			<b>{$limit_warning['request.max_vars']}</b> {l s='for suhosin.request.max_vars.'}<br/>
			{l s='Please ask your hosting provider to increase the suhosin post and request a limit of'}
		{else}
			{l s='Warning, your PHP configuration limits the maximum number of fields to post in a form:'}<br/>
			<b>{$limit_warning['max_input_vars']}</b> {l s='for max_input_vars.'}<br/>
			{l s='Please ask your hosting provider to increase the this limit to'}
		{/if}
		{l s='%s at least or edit the translation file manually.' sprintf=$limit_warning['needed_limit']}
	</div>
	{else}
		<form method="post" id="{$table}_form" action="{$url_submit}" class="form">
		{$toggle_button}
		<input type="hidden" name="lang" value="{$lang}" />
		<input type="hidden" name="type" value="{$type}" />
		<input type="hidden" name="theme" value="{$theme}" />
		<input type="submit" id="{$table}_form_submit_btn" name="submitTranslations{$type|ucfirst}" value="{l s='Update translations'}" class="button" />
		<br />

		{foreach $modules_translations as $theme_name => $theme}
			<h2>&gt;{l s='Theme:'} <a name="{$theme_name}">{if $theme_name === $default_theme_name}{l s='default'}{else}{$theme_name}{/if} </h2>
			{foreach $theme as $module_name => $module}
				<h3>{l s='Module:'} <a name="{$module_name}" style="font-style:italic">{$module_name}</a></h3>
				{foreach $module as $template_name => $newLang}
					{if !empty($newLang)}
						{$occurrences = $newLang|array_count_values}
						{if isset($occurrences[''])}
							{$missing_translations_module = $occurrences['']}
						{else}
							{$missing_translations_module = 0}
						{/if}
						<fieldset>
							<legend style="cursor : pointer" onclick="$('#{$theme_name}_{$module_name}_{$template_name}').slideToggle();">{if $theme_name === 'default'}{l s='default'}{else}{$theme_name}{/if} - {$template_name}
								<font color="blue">{$newLang|count}</font> {l s='expressions'} (<font color="red">{$missing_translations_module}</font>)
							</legend>
							<div name="{$type}_div" id="{$theme_name}_{$module_name}_{$template_name}" style="display:{if $missing_translations_module}block{else}none{/if}">
								<table cellpadding="2">
									{foreach $newLang as $key => $value}
										<tr>
											<td style="width: 40%">{$key|stripslashes}</td>
											<td>= 
												{* Prepare name string for md5() *}
												{capture assign="name"}{strtolower($module_name)}_{strtolower($theme_name)}_{strtolower($template_name)}_{md5($key)}{/capture}
												{if $key|strlen < $textarea_sized}
													<input type="text" 
														style="width: 450px{if empty($value)};background:#FBB{/if}"
														name="{$name|md5}" 
														value="{$value|regex_replace:'#"#':'&quot;'|stripslashes}" />
												{else}
													<textarea rows="{($key|strlen / $textarea_sized)|intval}" 
														style="width: 450px{if empty($value)};background:#FBB{/if}"
														name="{$name|md5}">{$value|regex_replace:'#"#':'&quot;'|stripslashes}</textarea>
												{/if}
											</td>
										</tr>
									{/foreach}
								</table>
							</div>
						</fieldset><br />
					{/if}
				{/foreach}
			{/foreach}
		{/foreach}
	{/if}

{/block}
