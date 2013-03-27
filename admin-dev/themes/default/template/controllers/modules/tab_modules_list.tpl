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

{if isset($tab_modules_list) && !empty($tab_modules_list)}
<script type="text/javascript">
	$(document).ready(function() {

		$('#nav_tabs_modules_installed').click(function () { showInstalledModules() });
		$('#nav_tabs_modules_not_installed').click(function () { showNotInstalledModules() });

		{if count($tab_modules_list.installed)}
			showInstalledModules();
		{else}
			showNotInstalledModules();
		{/if}
		
		bindTabModuleListAction();
	});
	
	function showInstalledModules(element)
	{
		$('#tab_modules_list_not_installed').hide();
		$('#tab_modules_list_installed').show();
		$('#nav_tabs_modules_installed').parent('li').addClass('active');
		$('#nav_tabs_modules_not_installed').parent('li').removeClass('active');
		return false;
	}
	
	function showNotInstalledModules(element)
	{
		$('#tab_modules_list_installed').hide();
		$('#tab_modules_list_not_installed').show();
		$(element).parent('li').addClass('active');
		$('#nav_tabs_modules_not_installed').parent('li').removeClass('active');
		return false;
	}
</script>
	<ul class="nav-tabs-modules">
	{if count($tab_modules_list.installed)}
		<li><a id="nav_tabs_modules_installed" href="#" onclick="">{l s='Installed'}</a></li>
	{/if}
	{if count($tab_modules_list.not_installed)}
		<li><a href="#" id="nav_tabs_modules_not_installed" >{l s='Not Installed'}</a></li>
	{/if}
	</ul>
	<div id="modules_list_container_content" style="clear:both">
	{if count($tab_modules_list.installed)}
	<table id="tab_modules_list_installed" style="clear:both">
		{foreach from=$tab_modules_list.installed item=module}
			{include file='controllers/modules/tab_module_line.tpl' class_row={cycle values=",rowalt"}}
		{/foreach}
	</table>
	{/if}
	{if count($tab_modules_list.not_installed)}
	<table id="tab_modules_list_not_installed" style="display:none;clear:both">
		{foreach from=$tab_modules_list.not_installed item=module}
			{include file='controllers/modules/tab_module_line.tpl' class_row={cycle values=",rowalt"}}
		{/foreach}
	</table>
	{/if}
	</div>
{/if}
<p style="text-align:right;text-decoration:underline;padding-right:10px"><a href="{$admin_module_favorites_view}" style="color:#666">{l s='More options'}</a></p>