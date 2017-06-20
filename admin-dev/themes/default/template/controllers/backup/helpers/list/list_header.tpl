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

{extends file="helpers/list/list_header.tpl"}

{block name="override_header"}

	{if isset($backup_url) && isset($backup_weight)}
	<div class="panel">
		<h3>
			<i class="icon-download"></i>
			{l s='Download' d='Admin.Actions'}
		</h3>
		<p>
			<a class="btn btn-default" href="{$backup_url}">
				<i class="icon-download"></i>
				{l s='Download the backup file (%s MB)' sprintf=[$backup_weight] d='Admin.Advparameters.Feature'}
			</a>
		</p>
		<p>{l s='Tip: You can also download this file from your FTP server. Backup files are located in the "/adminXXXX/backups" directory.' d='Admin.Advparameters.Feature'}</p>
	</div>
	{/if}

		<div class="alert alert-warning">
			<button data-dismiss="alert" class="close" type="button">×</button>
			<h4>{l s='Disclaimer before creating a new backup' d='Admin.Advparameters.Notification'}</h4>
			<ol>
				<li>{l s='PrestaShop is not responsible for your database, its backups and/or recovery.' d='Admin.Advparameters.Notification'}</li>
				<li>{l s='PrestaShop is open-source software. You are using it at your own risk under the license agreement.' d='Admin.Advparameters.Notification'}</li>
				<li>{l s='You should back up your data on a regular basis (both files and database).' d='Admin.Advparameters.Notification'}</li>
				<li>{l s='This function only backs up your database, not your files.' d='Admin.Advparameters.Notification'}</li>
				<li>{l s='By default, your existing database tables will be dropped during the backup recovery (see "Backup options" below).' d='Admin.Advparameters.Notification'}</li>
				<li>{l s='Always verify the quality and integrity of your backup files!' d='Admin.Advparameters.Notification'}</li>
				<li>{l s='Always verify that your backup files are complete, up-to-date and valid, even if you had a success message appear during the backup process.' d='Admin.Advparameters.Notification'}</li>
				<li>{l s='Always check your data.' d='Admin.Advparameters.Notification'}</li>
				<li>{l s='Never restore a backup on a live site.' d='Admin.Advparameters.Notification'}</li>
			</ol>
			<form action="{$currentIndex|escape:'html':'UTF-8'}&amp;add{$table}&amp;token={$token|escape:'html':'UTF-8'}" method="post" class="form-horizontal">
				<button type="submit" class="btn btn-default">
					<i class="icon-save"></i>
					{l s='I have read the disclaimer. Please create a new backup.' d='Admin.Advparameters.Notification'}
				</button>
			</form>
		</div>

		{if $host_mode}
		<div class="alert alert-info">
			<h4>{l s='How to restore a database backup' d='Admin.Advparameters.Notification'}</h4>
			{l s='If you need to restore a database backup, we invite you to subscribe to a [1][2]technical support plan[/2][/1].' html=true sprintf=['[1]' => '<strong>', '[/1]' => '</strong>', '[2]' => '<a class="_blank" href="http://addons.prestashop.com/support/16298-support-essentiel-plan.html">', '[/2]' => '</a>'] d='Admin.Advparameters.Notification'}
			<br />
			{l s='Our team will take care of restoring your database safely.' d='Admin.Advparameters.Notification'}
			<br />
			<br />
			{l s='Why can\'t I restore it by myself?' d='Admin.Advparameters.Notification'}
			<br />
			{l s='Your shop is hosted by PrestaShop. Although you can create backup files here below, there are core settings you cannot access for security reasons, like the database management. Thus, only our team can proceed to a restoration.' d='Admin.Advparameters.Notification'}
		</div>
		{else}
		<div class="alert alert-info">
			<h4>{l s='How to restore a database backup in 10 easy steps' d='Admin.Advparameters.Help'}</h4>
			<ol>
				<li>{l s='Set "Enable Shop" to "No" in the "Maintenance" page under the "Preferences" menu.' d='Admin.Advparameters.Help'}</li>
				<li>{l s='Download the backup from the list below or from your FTP server (in the folder "admin/backups").' d='Admin.Advparameters.Help'}</li>
				<li>{l s='Check the backup integrity: Look for errors, incomplete file, etc... Be sure to verify all of your data.' d='Admin.Advparameters.Help'}</li>
				<li>{l s='Please ask your hosting provider for "phpMyAdmin" access to your database.' d='Admin.Advparameters.Help'}</li>
				<li>{l s='Connect to "phpMyAdmin" and select your current database.' d='Admin.Advparameters.Help'}</li>
				<li>{l s='Unless you enabled the "Drop existing tables" option, you must delete all tables from your current database.' d='Admin.Advparameters.Help'}</li>
				<li>{l s='At the top of the screen, please select the "Import" tab' d='Admin.Advparameters.Help'}</li>
				<li>{l s='Click on the "Browse" button and select the backup file from your hard drive.' d='Admin.Advparameters.Help'}</li>
				<li>{l s='Check the maximum filesize allowed (e.g. Max: 16MB)'}<br />{l s='If your backup file exceeds this limit, contact your hosting provider for assistance. ' d='Admin.Advparameters.Help'}</li>
				<li>{l s='Click on the "Go" button and please wait patiently for the import process to conclude. This may take several minutes.' d='Admin.Advparameters.Help'}</li>
			</ol>
		</div>
		{/if}

{/block}
