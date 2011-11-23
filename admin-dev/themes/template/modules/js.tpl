{*
* 2007-2011 PrestaShop
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 9771 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}


<link href="{$smarty.const._PS_CSS_DIR_}jquery.fancybox-1.3.4.css" rel="stylesheet" type="text/css" media="screen" />
<script type="text/javascript">{$autocompleteList}</script>
<script type="text/javascript" src="{$smarty.const._PS_JS_DIR_}jquery/plugins/autocomplete/jquery.autocomplete.js"></script>
<script type="text/javascript" src="{$smarty.const._PS_JS_DIR_}jquery/plugins/fancybox/jquery.fancybox.js"></script>
<script type="text/javascript">
	var token = '{$token}';
	var currentIndex = '{$currentIndex}';
	var dirNameCurrentIndex = '{$dirNameCurrentIndex}';
	var ajaxCurrentIndex = '{$ajaxCurrentIndex}';
	var by = '{l s='by'}';
	var errorLogin = '{l s='Could not login to Addons'}';
	
	{if isset($smarty.get.anchor)}
	$('document').ready( function() {
		$.scrollTo('#{$smarty.get.anchor|htmlentities}', 1200, {literal}{offset: -100}{/literal});
	});
	{/if}

	{literal}
	function getPrestaStore(){if(getE("prestastore").style.display!='block')return;$.post(dirNameCurrentIndex+"/ajax.php",{page:"prestastore"},function(a){getE("prestastore-content").innerHTML=a;})}
	function truncate_author(author){return ((author.length > 20) ? author.substring(0, 20)+"..." : author);}
	function modules_management(action)
	{
		var modules = document.getElementsByName('modules');
		var module_list = '';
		for (var i = 0; i < modules.length; i++)
		{
			if (modules[i].checked == true)
			{
				rel = modules[i].getAttribute('rel');
				if (rel != "false" && action == "uninstall")
				{
					if (!confirm(rel))
						return false;
				}
				module_list += '|'+modules[i].value;
			}
		}
		document.location.href=currentIndex+'&token='+token+'&'+action+'='+module_list.substring(1, module_list.length);
	}
	$('document').ready( function() {
		$('input[name="filtername"]').autocomplete(moduleList, {
				minChars: 0,
				width: 310,
				matchContains: true,
				highlightItem: true,
				formatItem: function(row, i, max, term) {
					var image = '../modules/'+row.name+'/logo.gif';
					if (row.image != '')
						image = row.image;
					return '<img src="'+image+'" style="float:left;margin:5px;width:16px;height:16px"><strong>'+row.displayName+'</strong>'+((row.author != '') ? ' ' + by + ' ' + truncate_author(row.author) : '') + '<br /><span style="font-size: 80%;">'+ row.desc +'</span><br/><div style="height:15px;padding-top:5px">'+ row.option +'</div>';
				},
				formatResult: function(row) {
					return row.displayName;
				}
		});
		$('input[name="filtername"]').result(function(event, data, formatted) {
			 $('#filternameForm').submit();
		});
	});




	// Method to reload filter in ajax
	$(document).ready(function(){
		$('.categoryModuleFilterLink').click(function()
		{
			$('.categoryModuleFilterLink').css('background-color', 'white');
			$(this).css('background-color', '#EBEDF4');
			var ajaxReloadCurrentIndex = $(this).find('a').attr('href').replace('index.php', 'ajax-tab.php');
			try
			{
				resAjax = $.ajax({
						type:"POST",
						url : ajaxReloadCurrentIndex,
						async: true,
						data : {
							ajax : "1",
							token : token,
							controller : "AdminModules",
							action : "reloadModulesList",
						},
 						beforeSend: function(xhr)
						{
							$('#moduleContainer').html('<img src="../img/loader.gif" border="0">');
						},
						success : function(data)
						{
							$('#moduleContainer').html(data);
						},
						error: function(res,textStatus,jqXHR)
						{
							//alert("TECHNICAL ERROR"+res);
						}
				});
			}
			catch(e){}
			return false;
		});
	});




	// Method to get modules_list.xml from prestashop.com and default_country_modules_list.xml from addons.prestashop.com
	$(document).ready(function(){
			try
			{
				resAjax = $.ajax({
						type:"POST",
						url : ajaxCurrentIndex,
						async: true,
						data : {
						ajaxMode : "1",
						ajax : "1",
						token : token,
						controller : "AdminModules",
						action : "refreshModuleList"
				},
				success : function(res,textStatus,jqXHR)
				{
					// res.status  = cache or refresh
				},
				error: function(res,textStatus,jqXHR)
				{
					//alert("TECHNICAL ERROR"+res);
				}
			});
		}
		catch(e){}
	});


	// Method to log on PrestaShop Addons WebServices
	$(document).ready(function(){
		$('#addons_login_button').click(function()
		{
			var username_addons = $("#username_addons").val();
			var password_addons = $("#password_addons").val();
			try
			{
				resAjax = $.ajax({
						type:"POST",
						url : ajaxCurrentIndex,
						async: true,
						data : {
							ajax : "1",
							token : token,
							controller : "AdminModules",
							action : "logOnAddonsWebservices",
							username_addons : username_addons,
							password_addons : password_addons
						},
 						beforeSend: function(xhr)
						{
							$('#addons_loading').html('<img src="../img/loader.gif" border="0">');
						},
						success : function(data)
						{
							// res.status  = cache or refresh
							if (data == 'OK')
							{
								$('#addons_loading').html('');
								$('#addons_login_div').fadeOut();
								window.location.href = window.location.href;
							}
							else
								$('#addons_loading').html(errorLogin);
						},
						error: function(res,textStatus,jqXHR)
						{
							//alert("TECHNICAL ERROR"+res);
						}
				});
			}
			catch(e){}
			return false;
		});
	});
	{/literal}
</script>
