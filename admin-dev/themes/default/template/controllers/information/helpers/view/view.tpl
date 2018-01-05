{**
 * 2007-2017 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

{extends file="helpers/view/view.tpl"}

{block name="override_tpl"}
	{if !$host_mode}
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
						'missing': '{l s='Missing files' d='Admin.Advparameters.Notification'}',
						'updated': '{l s='Updated files' d='Admin.Advparameters.Notification'}'
					};

					if (json.missing.length || json.updated.length)
						$('#changedFiles').html('<div class="alert alert-warning">{l s='Changed/missing files have been detected.' js=1 d='Admin.Advparameters.Notification'}</div>');
					else
						$('#changedFiles').html('<div class="alert alert-success">{l s='No change has been detected in your files.' js=1 d='Admin.Advparameters.Notification'}</div>');

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
								.append($('<h4>').html(lang+' ('+json[key].length+')'))
								.append(html);
						}
					});
				}
			});
		});
	</script>
	{/if}
	<div class="row">
		<div class="col-lg-6">
			<div class="panel">
				<h3>
					<i class="icon-info"></i>
					{l s='Configuration information' d='Admin.Advparameters.Feature'}
				</h3>
				<p>{l s='This information must be provided when you report an issue on our bug tracker or forum.' d='Admin.Advparameters.Feature'}</p>
			</div>
			{if !$host_mode}
			<div class="panel">
				<h3>
					<i class="icon-info"></i>
					{l s='Server information' d='Admin.Advparameters.Feature'}
				</h3>
				{if count($uname)}
				<p>
					<strong>{l s='Server information:' d='Admin.Advparameters.Feature'}</strong> {$uname|escape:'html':'UTF-8'}
				</p>
				{/if}
				<p>
					<strong>{l s='Server software version:' d='Admin.Advparameters.Feature'}</strong> {$version.server|escape:'html':'UTF-8'}
				</p>
				<p>
					<strong>{l s='PHP version:' d='Admin.Advparameters.Feature'}</strong> {$version.php|escape:'html':'UTF-8'}
				</p>
				<p>
					<strong>{l s='Memory limit:' d='Admin.Advparameters.Feature'}</strong> {$version.memory_limit|escape:'html':'UTF-8'}
				</p>
				<p>
					<strong>{l s='Max execution time:' d='Admin.Advparameters.Feature'}</strong> {$version.max_execution_time|escape:'html':'UTF-8'}
				</p>
				<p>
					<strong>{l s='Upload Max File size:' d='Admin.Advparameters.Feature'}</strong> {$version.upload_max_filesize|escape:'html':'UTF-8'}
				</p>
				{if $apache_instaweb}
					<p>{l s='PageSpeed module for Apache installed (mod_instaweb)' d='Admin.Advparameters.Feature'}</p>
				{/if}
			</div>
			<div class="panel">
				<h3>
					<i class="icon-info"></i>
					{l s='Database information'}
				</h3>
				<p>
					<strong>{l s='MySQL version:' d='Admin.Advparameters.Feature'}</strong> {$database.version|escape:'html':'UTF-8'}
				</p>
				<p>
					<strong>{l s='MySQL server:' d='Admin.Advparameters.Feature'}</strong> {$database.server|escape:'html':'UTF-8'}
				</p>
				<p>
					<strong>{l s='MySQL name:' d='Admin.Advparameters.Feature'}</strong> {$database.name|escape:'html':'UTF-8'}
				</p>
				<p>
					<strong>{l s='MySQL user:' d='Admin.Advparameters.Feature'}</strong> {$database.user|escape:'html':'UTF-8'}
				</p>
				<p>
					<strong>{l s='Tables prefix:' d='Admin.Advparameters.Feature'}</strong> {$database.prefix|escape:'html':'UTF-8'}
				</p>
				<p>
					<strong>{l s='MySQL engine:' d='Admin.Advparameters.Feature'}</strong> {$database.engine|escape:'html':'UTF-8'}
				</p>
				<p>
					<strong>{l s='MySQL driver:' d='Admin.Advparameters.Feature'}</strong> {$database.driver|escape:'html':'UTF-8'}
				</p>
			</div>
		</div>
		{/if}
		<div class="col-lg-6">
			<div class="panel">
				<h3>
					<i class="icon-info"></i>
					{l s='Store information' d='Admin.Advparameters.Feature'}
				</h3>
				<p>
					<strong>{l s='PrestaShop version:' d='Admin.Advparameters.Feature'}</strong> {$shop.ps|escape:'html':'UTF-8'}
				</p>
				<p>
					<strong>{l s='Shop URL:' d='Admin.Advparameters.Feature'}</strong> {$shop.url|escape:'html':'UTF-8'}
				</p>
				<p>
					<strong>{l s='Current theme in use:' d='Admin.Advparameters.Feature'}</strong> {$shop.theme|escape:'html':'UTF-8'}
				</p>
			</div>
			<div class="panel">
				<h3>
					<i class="icon-info"></i>
					{l s='Mail configuration' d='Admin.Advparameters.Feature'}
				</h3>
				<p>
					<strong>{l s='Mail method:' d='Admin.Advparameters.Feature'}</strong>

			{if $mail}
				{l s='You are using the PHP mail() function.' d='Admin.Advparameters.Feature'}</p>
			{else}
				{l s='You are using your own SMTP parameters.' d='Admin.Advparameters.Feature'}</p>
				<p>
					<strong>{l s='SMTP server:' d='Admin.Advparameters.Feature'}</strong> {$smtp.server|escape:'html':'UTF-8'}
				</p>
				<p>
					<strong>{l s='SMTP username:' d='Admin.Advparameters.Feature'}</strong>
					{if $smtp.user neq ''}
						{l s='Defined' d='Admin.Advparameters.Feature'}
					{else}
						<span style="color:red;">{l s='Not defined' d='Admin.Advparameters.Feature'}</span>
					{/if}
				</p>
				<p>
					<strong>{l s='SMTP password:' d='Admin.Advparameters.Feature'}</strong>
					{if $smtp.password neq ''}
						{l s='Defined' d='Admin.Advparameters.Feature'}
					{else}
						<span style="color:red;">{l s='Not defined' d='Admin.Advparameters.Feature'}</span>
					{/if}
				</p>
				<p>
					<strong>{l s='Encryption:' d='Admin.Advparameters.Feature'}</strong> {$smtp.encryption|escape:'html':'UTF-8'}
				</p>
				<p>
					<strong>{l s='SMTP port:' d='Admin.Advparameters.Feature'}</strong> {$smtp.port|escape:'html':'UTF-8'}
				</p>
			{/if}
			</div>
			<div class="panel">
				<h3>
					<i class="icon-info"></i>
					{l s='Your information' d='Admin.Advparameters.Feature'}
				</h3>
				<p>
					<strong>{l s='Your web browser:' d='Admin.Advparameters.Feature'}</strong> {$user_agent|escape:'html':'UTF-8'}
				</p>
			</div>

			<div class="panel" id="checkConfiguration">
				<h3>
					<i class="icon-info"></i>
					{l s='Check your configuration' d='Admin.Advparameters.Feature'}
				</h3>
				<p>
					<strong>{l s='Required parameters:' d='Admin.Advparameters.Feature'}</strong>
				{if !$failRequired}
					<span class="text-success">{l s='OK' d='Admin.Advparameters.Notification'}</span>
				</p>
				{else}
					<span class="text-danger">{l s='Please fix the following error(s)' d='Admin.Advparameters.Notification'}</span>
				</p>
				<ul>
					{foreach from=$testsRequired item='value' key='key'}
						{if $value eq 'fail' && isset($testsErrors[$key])}
							<li>{$testsErrors[$key]}</li>
						{/if}
					{/foreach}
				</ul>
				{/if}
				{if isset($failOptional)}
					<p>
						<strong>{l s='Optional parameters:' d='Admin.Advparameters.Feature'}</strong>
					{if !$failOptional}
						<span class="text-success">{l s='OK' d='Admin.Advparameters.Notification'}</span>
					</p>
					{else}
						<span class="text-danger">{l s='Please fix the following error(s)' d='Admin.Advparameters.Notification'}</span>
					</p>
					<ul>
						{foreach from=$testsOptional item='value' key='key'}
							{if $value eq 'fail' && isset($testsErrors[$key])}
								<li>{$testsErrors[$key]}</li>
							{/if}
						{/foreach}
					</ul>
					{/if}
				{/if}
			</div>
		</div>
	</div>
	{if !$host_mode}
	<div class="panel">
		<h3>
			<i class="icon-info"></i>
			{l s='List of changed files' d='Admin.Advparameters.Feature'}
		</h3>
		<div id="changedFiles"><i class="icon-spin icon-refresh"></i> {l s='Checking files...' d='Admin.Advparameters.Notification'}</div>
	</div>
	{/if}
{/block}
