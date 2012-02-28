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
*  @version  Release: $Revision: 8897 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helpers/view/view.tpl"}

{block name="override_tpl"}

	<script type="text/javascript">
		$(document).ready(function()
		{
			$.ajax({
				type: 'GET',
				url: '{$link->getAdminLink('AdminInformation')}',
				data: {
					'action': 'checkFiles',
					'ajax': 1
				},
				dataType: 'json',
				success: function(json)
				{
					var tab = {
						'missing': '{l s='Missing files'}',
						'updated': '{l s='Updated files'}'
					};

					if (json.missing.length || json.updated.length)
						$('#changedFiles').html('<div class="warn">{l s='Changed / missing files have been detected'}</div>');
					else
						$('#changedFiles').html('<div class="conf">{l s='No change has been detected on your files'}</div>');

					$.each(tab, function(key, lang)
					{
						if (json[key].length)
						{
							var html = $('<ul>').attr('id', key+'_files');
							$(json[key]).each(function(key, file)
							{
								html.append($('<li>').html(file))
							});
							$('#changedFiles')
								.append($('<h3>').html(lang+' ('+json[key].length+')'))
								.append(html);
						}
					});
				}
			});
		});
	</script>

	<fieldset>
		<legend><img src="../img/t/AdminInformation.gif" alt="" />{l s='Information'}</legend>
		<p>{l s='This information must be indicated when you report a bug on our bug tracker or if you report a problem on our forum.'}</p>
	</fieldset>
	<br />
	<fieldset>
		<legend><img src="../img/t/AdminInformation.gif" alt="" /> {l s='Information about your configuration'}</legend>
		<h3>{l s='Server information'}</h3>
		<p>
			<b>{l s='Prestashop Version'}:</b> {$version.ps}
		</p>
	
		{if count($uname)}
		<p>
			<b>{l s='Server information'}:</b> {$uname}
		</p>
		{/if}
	
		<p>
			<b>{l s='Server software Version'}:</b> {$version.server}
		</p>
		<p>
			<b>{l s='PHP Version'}:</b> {$version.php}
		</p>
		<p>
			<b>{l s='MySQL Version'}:</b> {$version.mysql}
		</p>
		{if $apache_instaweb}
		<p style="color:red;font-weight:700">{l s='PageSpeed module for Apache installed (mod_instaweb)'}</p>
		{/if}
	
		<hr />
		<h3>{l s='Store information'}</h3>
		<p>
			<b>{l s='URL of your website'}:</b> {$shop.url}
		</p>
		<p>
			<b>{l s='Theme name used'}:</b> {$shop.theme}
		</p>
		<hr />
		<h3>{l s='Mail information'}</h3>
		<p>
			<b>{l s='Mail method'}:</b>
	
	{if $mail}
		{l s='You use PHP mail() function.'}</p>
	{else}
		{l s='You use your own SMTP parameters'}</p>
		<p>
			<b>{l s='SMTP server'}:</b> {$smtp.server}
		</p>
		<p>
			<b>{l s='SMTP user'}:</b>
			{if $smtp.user neq ''}
				{l s='Defined'}
			{else}
				<span style="color:red;">{l s='Not defined'}</span>
			{/if}
		</p>
		<p>
			<b>{l s='SMTP password'}:</b>
			{if $smtp.password neq ''}
				{l s='Defined'}
			{else}
				<span style="color:red;">{l s='Not defined'}</span>
			{/if}
		</p>
		<p>
			<b>{l s='Encryption'}:</b> {$smtp.encryption}
		</p>
		<p>
			<b>{l s='Port'}:</b> {$smtp.port}
		</p>
	{/if}
		<hr />
		<h3>{l s='Your information'}</h3>
		<p>
			<b>{l s='Information from you'}:</b> {$user_agent}
		</p>
	</fieldset>
	<br />
	<fieldset id="checkConfiguration">
		<legend><img src="../img/t/AdminInformation.gif" alt="" /> {l s='Check your configuration'}</legend>
		<p>
			<b>{l s='Required parameters'}:</b>
			{if !$failRequired}
					<span style="color:green;font-weight:bold;">OK</span>
				</p>
			{else}
				<span style="color:red">{l s='Please consult the following error(s)'}</span>
			</p>
			<ul>
				{foreach from=$testsRequired item='value' key='key'}
					{if $value eq 'fail'}
						<li>{$testsErrors[$key]}</li>
					{/if}
				{/foreach}
			</ul>
			{/if}
	
			<p>
				<b>{l s='Optional parameters'}:</b>
			{if !$failOptional}
				<span style="color:green;font-weight:bold;">OK</span>
			</p>
			{else}
				<span style="color:red">{l s='Please consult the following error(s)'}</span>
			</p>
			<ul>
				{foreach from=$testsOptional item='value' key='key'}
					{if $value eq 'fail'}
						<li>{$key}</li>
					{/if}
				{/foreach}
			</ul>
			{/if}
	
	</fieldset>

	<br />
	<fieldset>
		<legend><img src="../img/t/AdminInformation.gif" alt="" /> {l s='List of changed files'}</legend>
		<div id="changedFiles"><img src="../img/admin/ajax-loader.gif" /> {l s='Checking files ...'}</div>
	</fieldset>

{/block}
