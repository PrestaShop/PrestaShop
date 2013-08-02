{*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
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
				url: '{$link->getAdminLink('AdminInformation')|addslashes}',
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
						$('#changedFiles').html('<div class="alert alert-block">{l s='Changed/missing files have been detected.'}</div>');
					else
						$('#changedFiles').html('<div class="alert">{l s='No change has been detected in your files'}</div>');

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
		<h3>
			<i class="icon-info"></i>
			{l s='Configuration information'}
		</h3>
		<p>{l s='This information must be provided when you report an issue on our bug tracker or forum.'}</p>
	</fieldset>
	<fieldset>
		<h3>
			<i class="icon-info"></i>
			{l s='Server information'}
		</h3>	
		{if count($uname)}
		<p>
			<strong>{l s='Server information'}:</strong> {$uname|escape:'htmlall':'UTF-8'}
		</p>
		{/if}
		<p>
			<strong>{l s='Server software version'}:</strong> {$version.server|escape:'htmlall':'UTF-8'}
		</p>
		<p>
			<strong>{l s='PHP version'}:</strong> {$version.php|escape:'htmlall':'UTF-8'}
		</p>
		<p>
			<strong>{l s='Memory limit'}:</strong> {$version.memory_limit|escape:'htmlall':'UTF-8'}
		</p>
		<p>
			<strong>{l s='Max execution time'}:</strong> {$version.max_execution_time|escape:'htmlall':'UTF-8'}
		</p>
		{if $apache_instaweb}
			<p>{l s='PageSpeed module for Apache installed (mod_instaweb)'}</p>
		{/if}
	</fieldset>
	<fieldset>
		<h3>
			<i class="icon-info"></i>
			{l s='Database information'}
		</h3>
		<p>
			<strong>{l s='MySQL version'}:</strong> {$database.version|escape:'htmlall':'UTF-8'}
		</p>
		<p>
			<strong>{l s='MySQL engine'}:</strong> {$database.engine|escape:'htmlall':'UTF-8'}
		</p>
		<p>
			<strong>{l s='Tables prefix'}:</strong> {$database.prefix|escape:'htmlall':'UTF-8'}
		</p>
	</fieldset>
	<fieldset>
		<h3>
			<i class="icon-info"></i>
			{l s='Store information'}
		</h3>
		<p>
			<strong>{l s='PrestaShop version'}:</strong> {$shop.ps|escape:'htmlall':'UTF-8'}
		</p>
		<p>
			<strong>{l s='Shop URL'}:</strong> {$shop.url|escape:'htmlall':'UTF-8'}
		</p>
		<p>
			<strong>{l s='Current theme in use'}:</strong> {$shop.theme|escape:'htmlall':'UTF-8'}
		</p>
	</fieldset>
	<fieldset>
		<h3>
			<i class="icon-info"></i>
			{l s='Mail configuration'}
		</h3>
		<p>
			<strong>{l s='Mail method'}:</strong>
	
	{if $mail}
		{l s='You are using the PHP mail function.'}</p>
	{else}
		{l s='You are using your own SMTP parameters.'}</p>
		<p>
			<strong>{l s='SMTP server'}:</strong> {$smtp.server|escape:'htmlall':'UTF-8'}
		</p>
		<p>
			<strong>{l s='SMTP user'}:</strong>
			{if $smtp.user neq ''}
				{l s='Defined'}
			{else}
				<span style="color:red;">{l s='Not defined'}</span>
			{/if}
		</p>
		<p>
			<strong>{l s='SMTP password'}:</strong>
			{if $smtp.password neq ''}
				{l s='Defined'}
			{else}
				<span style="color:red;">{l s='Not defined'}</span>
			{/if}
		</p>
		<p>
			<strong>{l s='Encryption'}:</strong> {$smtp.encryption|escape:'htmlall':'UTF-8'}
		</p>
		<p>
			<strong>{l s='Port'}:</strong> {$smtp.port|escape:'htmlall':'UTF-8'}
		</p>
	{/if}
	</fieldset>
	<fieldset>
		<h3>
			<i class="icon-info"></i>
			{l s='Your information'}
		</h3>
		<p>
			<strong>{l s='Your web browser'}:</strong> {$user_agent|escape:'htmlall':'UTF-8'}
		</p>
	</fieldset>

	<fieldset id="checkConfiguration">
		<h3>
			<i class="icon-info"></i>
			{l s='Check your configuration'}
		</h3>
		<p>
			<strong>{l s='Required parameters'}:</strong>
			{if !$failRequired}
					<span class="text-success">OK</span>
				</p>
			{else}
				<span class="text-danger">{l s='Please fix the following error(s)'}</span>
			</p>
			<ul class="nav">
				{foreach from=$testsRequired item='value' key='key'}
					{if $value eq 'fail'}
						<li>{$testsErrors[$key]}</li>
					{/if}
				{/foreach}
			</ul>
			{/if}
	
			<p>
				<strong>{l s='Optional parameters'}:</strong>
			{if !$failOptional}
				<span>OK</span>
			</p>
			{else}
				<span class="text-success">{l s='Please fix the following error(s)'}</span>
			</p>
			<ul class="nav">
				{foreach from=$testsOptional item='value' key='key'}
					{if $value eq 'fail'}
						<li>{$key}</li>
					{/if}
				{/foreach}
			</ul>
			{/if}
	</fieldset>

	<fieldset>
		<h3>
			<i class="icon-info"></i> 
			{l s='List of changed files'}
		</h3>
		<div id="changedFiles"><img src="../img/admin/ajax-loader.gif" /> {l s='Checking files...'}</div>
	</fieldset>

{/block}
