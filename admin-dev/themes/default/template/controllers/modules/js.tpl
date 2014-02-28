{*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA

*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">{$autocompleteList}</script>
<script type="text/javascript">
    var header_confirm_reset = '{l s='Confirm reset'}';
    var body_confirm_reset = '{l s='Would you like to delete the content related to this module ?'}';
    var left_button_confirm_reset = '{l s='No'}';
    var right_button_confirm_reset = '{l s='Yes'}';
	var currentIndex = '{$currentIndex}';
	var currentIndexWithToken = '{$currentIndex}&token={$token}';
	var dirNameCurrentIndex = '{$dirNameCurrentIndex}';
	var ajaxCurrentIndex = '{$ajaxCurrentIndex}';
	var installed_modules = {if isset($installed_modules) && count($installed_modules)}{$installed_modules}{else}false{/if};
	var by = '{l s='by'}';
	var errorLogin = '{l s='PrestaShop was unable to login to Addons. Please check your credentials and your internet connection.'}';
	var confirmPreferencesSaved = '{l s='Preferences saved'}';
	{if isset($smarty.get.anchor) && !isset($error_module)}var anchor = '{$smarty.get.anchor|htmlentities|replace:'(':''|replace:')':''|replace:'{':''|replace:'}':''|replace:'\'':''|replace:'/':''}';{else}var anchor = '';{/if}

	{if isset($smarty.get.module_name) && !isset($error_module)}var module_name = '{$smarty.get.module_name|htmlentities|replace:'(':''|replace:')':''|replace:'{':''|replace:'}':''|replace:'\'':''|replace:'/':''}';{else}var module_name = '';{/if}

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
		// ScrollTo
		if (anchor != '')
			$.uiTableFilter($('#moduleContainer').find('table'), anchor);

		if (module_name != '')
			$.uiTableFilter($('#moduleContainer').find('table'), module_name);
		
		$('#moduleQuicksearch').on('keyup', function(){
			$.uiTableFilter($('#moduleContainer').find('table'), this.value);
		}).on('keydown', function(e){
			if (e.keyCode == 13)
				return false;
		});
		
		$('input[name="filtername"]').result(function(event, data, formatted) {
			$('#filternameForm').submit();
		});

		// Method to check / uncheck all modules checkbox
		$('#moduleContainer').on("click", "#checkme", function()
		{
			if ($(this).attr("rel") == 'false')
			{
				$(this).attr("checked", true);
				$(this).attr("rel", "true");
				$("input[name=modules]").attr("checked", true);
			}
			else
			{
				$(this).removeAttr("checked");
				$(this).attr("rel", "false");
				$("input[name=modules]").removeAttr("checked");
			}
		});		

		// Method to reload filter in ajax
		$('.categoryModuleFilterLink').click(function()
		{
			$('.categoryModuleFilterLink').removeClass('active');
			$(this).addClass('active');
			try
			{
				resAjax = $.ajax({
					type:"POST",
					url : $(this).attr('href')+'&rand=' + new Date().getTime(),
					headers: {"cache-control": "no-cache"},
					async: true,
					cache: false,
					data : {
						ajax : "1",
						token : token,
						controller : "AdminModules",
						action : "reloadModulesList"
					},
					beforeSend: function(xhr){
						$('#moduleContainer').html('<img src="../img/loader.gif" alt="" border="0" />');
					},
					success: function(data, status, request){
						if (request.getResponseHeader('Login') === 'true')
							return window.location.reload();

						$('#moduleContainer').html(data);
						$('.dropdown-toggle').dropdown();
					}
				});
			}
			catch(e){}
			return false;
		});

		// Method to get modules_list.xml from prestashop.com and default_country_modules_list.xml from addons.prestashop.com
		try
		{
			resAjax = $.ajax({
				type:"POST",
				url: ajaxCurrentIndex,
				headers: {"cache-control": "no-cache"},
				async: true,
				cache: false,
				data: {
					ajaxMode : "1",
					ajax : "1",
					token : token,
					controller : "AdminModules",
					action : "refreshModuleList"
				},
				success: function(data){
					if (data == '{"status":"refresh"}')
						window.location.href = window.location.href;
				}
			});
		}
		catch(e) { }

		// Method to log on PrestaShop Addons WebServices
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
					beforeSend: function(xhr){
						$('#addons_loading').html('<img src="../img/loader.gif" alt="" border="0" />');
					},
					success : function(data){
						if (data == 'OK')
						{
							$('#addons_loading').html('');
							$('#addons_login_div').fadeOut();
							window.location.href = currentIndexWithToken + '&conf=32';
						}
						else
							$('#addons_loading').html(errorLogin);
					}
				});
			}
			catch(e){}
			return false;
		});

		// Method to log out PrestaShop Addons WebServices
		$('#addons_logout_button').click(function()
		{
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
						action : "logOutAddonsWebservices"
					},
					beforeSend: function(xhr){
						$('#addons_loading').html('<img src="../img/loader.gif" alt="" border="0" />');
					},
					success: function(data) {
						if (data == 'OK')
						{
							$('#addons_loading').html('');
							$('#addons_login_div').fadeOut();
							window.location.href = currentIndexWithToken;
						}
						else
							$('#addons_loading').html(errorLogin);
					}
				});
			}
			catch(e){}
			return false;
		});

		// Method to set filter on modules
		function setFilter()
		{
			var module_type = $("#module_type_filter").val();
			var module_install = $("#module_install_filter").val();
			var module_status = $("#module_status_filter").val();
			var country_module_value = $("#country_module_value_filter").val();
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
						action : "setFilter",
						module_type : module_type,
						module_install : module_install,
						module_status : module_status,
						country_module_value : country_module_value,
						filterModules : 'Filter'
					},
					success : function(data){
						if (data == 'OK')
							window.location.href = currentIndexWithToken;
					}
				});
			}
			catch(e){}
			return false;
		}

		$(document).on('change', '#module_type_filter, #module_install_filter, #module_status_filter, #country_module_value_filter', function() { 
			setFilter(); 
		});

		$('.moduleTabPreferencesChoise').change(function()
		{			
			var value_pref = $(this).val();
			var module_pref = $(this).attr('name');
			module_pref = module_pref.substring(2, module_pref.length);

			$.ajax({
				type:"POST",
				url : ajaxCurrentIndex,
				async: true,
				data : {
					ajax : "1",
					token : token,
					controller : "AdminModules",
					action : "saveTabModulePreferences",
					module_pref : module_pref,
					value_pref : value_pref
				},
				success : function(data){
					if (data == 'OK')
						showSuccessMessage(confirmPreferencesSaved);
				}
			});
		});
		
		// Method to save favorites preferences
		$('.moduleFavorite').change(function()
		{
			var value_pref = $(this).val();
			var module_pref = $(this).attr('name');
			var action_pref = module_pref.substring(0, 1);
			module_pref = module_pref.substring(2, module_pref.length);
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
						action : "saveFavoritePreferences",
						action_pref : action_pref,
						module_pref : module_pref,
						value_pref : value_pref
					},
					success : function(data){
						if (data == 'OK')
							showSuccessMessage(confirmPreferencesSaved);
					}
				});
			}
			catch(e){}
			return false;
		});

		$('#moduleContainer').on("click", ".toggle_favorite", function()
	    {
	      var el = $(this);
	      var value_pref = el.data('value');
	      var module_pref = el.data('module');
	      var action_pref = 'f';
	      var total_favorites = parseInt($('#favorite-count').html());

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
	              action : "saveFavoritePreferences",
	              action_pref : action_pref,
	              module_pref : module_pref,
	              value_pref : value_pref
	            },
	            success : function(data)
	            {
	              // res.status  = cache or refresh
	              if (data == 'OK')
	              {
	                el.parent('li').find('.toggle_favorite').toggle();


					if (value_pref)
						$('#favorite-count').html(total_favorites+1);
					else
						$('#favorite-count').html(total_favorites-1);
	              }
	                
	            },
	            error: function(res,textStatus,jqXHR)
	            {
	              //jAlert("TECHNICAL ERROR"+res);
	            }
	        });
	      }
	      catch(e){}
	      return false;
	    });
	});

	{/literal}
</script>
