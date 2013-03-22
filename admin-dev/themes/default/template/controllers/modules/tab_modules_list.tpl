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
		$('.action_tab_module').each( function (){
			$(this).click(function () {
				option = $('#'+$(this).data('option')+' :selected');

				if (typeof($(option).data('onclick')) != 'undefined' || $(option).data('onclick') != '')
				{
					var f = eval("(function(){ "+$(option).data('onclick')+"})");
					if (f.call())
						window.location.href = $(option).data('href');
				}
				else
					window.location.href = $(option).data('href');
				return false;
			});			
		});
		
		$('#nav_tabs_modules_installed').click( function () {
			$('#tab_modules_list_not_installed').hide();
			$('#tab_modules_list_installed').show();
			$(this).parent('li').addClass('active');
			$('#nav_tabs_modules_not_installed').parent('li').removeClass('active');
			return false;
		});
		
		$('#nav_tabs_modules_not_installed').click( function () {
			$('#tab_modules_list_installed').hide();
			$('#tab_modules_list_not_installed').show();
			$(this).parent('li').addClass('active');
			$('#nav_tabs_modules_installed').parent('li').removeClass('active');
			return false;
		});
	});
</script>
	<ul class="nav-tabs-modules">
	  <li class="active"><a id="nav_tabs_modules_installed" href="#" onclick="">{l s='Installed'}</a></li>
	  <li><a href="#" id="nav_tabs_modules_not_installed" >{l s='Not Installed'}</a></li>
	</ul>
	<div id="modules_list_container_content" style="clear:both">
		<table id="tab_modules_list_installed" style="clear:both">
			{if count($tab_modules_list.installed)}
				{foreach from=$tab_modules_list.installed item=module}
					{include file='controllers/modules/tab_module_line.tpl' class_row={cycle values=",rowalt"}}
				{/foreach}
			{else}
				<tr><td><div style="font-weight:700;margin-top:20px">{l s='No modules available in this section.'}</b></td></tr>
			{/if}
		</table>
		<table id="tab_modules_list_not_installed" style="display:none;clear:both">
			{if count($tab_modules_list.not_installed)}
				{foreach from=$tab_modules_list.not_installed item=module}
					{include file='controllers/modules/tab_module_line.tpl' class_row={cycle values=",rowalt"}}
				{/foreach}
			{else}
				<tr><td><div style="font-weight:700;margin-top:20px">{l s='No modules available in this section.'}</b></td></tr>
			{/if}
		</table>
	</div>
{/if}
<p style="text-align:right;text-decoration:underline;padding-right:10px"><a href="{$admin_module_favorites_view}" style="color:#666">{l s='More options'}</a></p>