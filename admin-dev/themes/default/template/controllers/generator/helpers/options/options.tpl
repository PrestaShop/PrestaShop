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
*  @version  Release: $Revision: 9548 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{extends file="helpers/options/options.tpl"}
{block name="defaultOptions"}
		<form action="{$current}&amp;token={$token}" method="post" enctype="multipart/form-data">
		<fieldset><legend><img src="../img/admin/htaccess.gif" alt="" />{l s='Htaccess file generation'}</legend>
		<p>
			<b>{l s='Warning:'}</b> 
			{l s='this tool can ONLY be used if you are hosted by an Apache web server. Please ask your webhost.'}
		</p>
		<p>
			{l s='This tool will automatically generate a ".htaccess" file that will give you the ability to do URL rewriting and to catch 404 errors.'}
		</p>
		{if $checkConfiguration_ht}
			<div class="clear">&nbsp;</div>
			<label for="imageCacheControl">{l s='Optimization'}</label>
			<div class="margin-form">
				<input type="checkbox" name="PS_HTACCESS_CACHE_CONTROL" id="PS_HTACCESS_CACHE_CONTROL" value="1" {if $ps_htaccess_cache_control}checked="checked"{/if} />
				<p>{l s='This will add directives to your .htaccess file which should improve caching and compression.'}</p>
			</div>
			<div class="clear">&nbsp;</div>
			<label for="imageCacheControl">{l s='Friendly URL'}</label>
			<div class="margin-form">
				<input type="checkbox" name="PS_REWRITING_SETTINGS" id="PS_REWRITING_SETTINGS" value="1" {if $ps_rewriting_settings}checked="checked"{/if} />
				<p>{l s='Enable only if your server allows URL rewriting.'}</p>
			</div>
			<div class="clear">&nbsp;</div>
			<label for="imageCacheControl">{l s='Disable apache multiviews'}</label>
			<div class="margin-form">
				<input type="checkbox" name="PS_HTACCESS_DISABLE_MULTIVIEWS" id="PS_HTACCESS_DISABLE_MULTIVIEWS" value="1" {if $ps_htaccess_disable_multiviews}checked="checked"{/if} />
				<p>{l s='Enable this option only if you have problems with some pages URL rewriting.'}</p>
			</div>
			<p class="clear" style="font-weight:bold;">{l s='Generate your ".htaccess" file by clicking on the following button:'} 
			<input type="submit" value="{l s='Generate .htaccess file'}" name="submitHtaccess" class="button" /></p>
			<p>{l s='This will erase your'}<b> {l s='old'}</b> {l s='.htaccess file!'}</p>
		{else}
			
			<p style="color:red; font-weight:bold;">{l s='Before being able to use this tool, you need to:'}</p>
			<p>{l s='- create a'} <b>{l s='.htaccess'}</b> {l s='blank file in directory'} <b>{$smarty.const.__PS_BASE_URI__}</b>
			<br />{l s='- give it write permissions (CHMOD 666 on Unix system)'}</p>
		{/if}
		</p></fieldset></form>

		<br /><br />
		<form action="{$current}&amp;token={$token}" method="post" enctype="multipart/form-data">
		<fieldset><legend><img src="../img/admin/binoculars.png" alt="" />{l s='Robots file generation'}</legend>
		<p><b>{l s='Warning:'} </b>{l s='Your file robots.txt MUST be in your website\'s root directory and nowhere else.'}</p>
		<p>{l s='eg: http://www.yoursite.com/robots.txt'}</p>
		<p>{l s='This tool will automatically generate a "robots.txt" file that you can configure to deny access to search engines for some pages.'}</p>
		{if $checkConfiguration_rb}
			
			<p style="font-weight:bold;">{l s='Generate your "robots.txt" file by clicking on the following button:'} 
			<input type="submit" value="{l s='Generate robots.txt file'}" name="submitRobots" class="button" /></p>
			<p>{l s='This will erase your'}<b> {l s='old'}</b> {l s='robots.txt file!'}</p>
		{else}
			<p style="color:red; font-weight:bold;">{l s='Before being able to use this tool, you need to:'}</p>
			<p>{l s='- create a'} <b>{l s='robots.txt'}</b> {l s='blank file in dir:'} <b>{$smarty.const.__PS_BASE_URI__}</b>
			<br />{l s='- give it write permissions (CHMOD 666 on Unix system)'}</p>
		{/if}
		</p></fieldset></form>
		<br />
{/block}
