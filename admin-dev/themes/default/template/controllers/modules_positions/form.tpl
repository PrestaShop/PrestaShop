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

{include file="toolbar.tpl" toolbar_btn=$toolbar_btn toolbar_scroll=$toolbar_scroll title=$title}
<div class="leadin">{block name="leadin"}{/block}</div>

<form action="{$url_submit}" id="{$table}_form" method="post">
	{if $display_key}
		<input type="hidden" name="show_modules" value="{$display_key}" />
	{/if}
	<fieldset>
		<legend><img src="../img/t/AdminModulesPositions.gif" />{l s='Transplant a module'}</legend>
		<label>{l s='Module'} :</label>
		<div class="margin-form">
			<select name="id_module" {if $edit_graft} disabled="disabled"{/if}>
				{foreach $modules as $module}
					<option value="{$module->id}" {if $id_module == $module->id || (!$id_module && $show_modules == $module->id)}selected="selected"{/if}>{$module->displayName|stripslashes}</option>
				{/foreach}
			</select><sup> *</sup>
		</div>
		<label>{l s='Hook into'} :</label>
		<div class="margin-form">
			<select name="id_hook" {if $edit_graft} disabled="disabled"{/if}>
				{foreach $hooks as $hook}
					<option value="{$hook['id_hook']}" {if $id_hook == $hook['id_hook']} selected="selected"{/if}>{$hook['name']}{if $hook['name'] != $hook['title']} ({$hook['title']}){/if}</option>
				{/foreach}
			</select><sup> *</sup>
		</div>
	
		<label>{l s='Exceptions'} :</label>
		<div class="margin-form">
			{l s='Please specify the files for which you do not want the module to be displayed.'}<br />
			{l s='Please input each filename, separated by a comma.'}<br />
			{if !$except_diff}
				{$exception_list}
			{else}
				{foreach $exception_list_diff as $value}
					{$value}
				{/foreach}
			{/if}
		</div>
	
		<div class="margin-form">
			{if $edit_graft}
				<input type="hidden" name="id_module" value="{$id_module}" />
				<input type="hidden" name="id_hook" value="{$id_hook}" />
			{/if}
			<input type="submit" value="{l s='Save'}" name="{if $edit_graft}submitEditGraft{else}submitAddToHook{/if}" id="{$table}_form_submit_btn" class="button" />
		</div>
		<div class="small"><sup>*</sup> {l s='Required field'}</div>
	</fieldset>
</form>
<script type="text/javascript">
	//<![CDATA
	function position_exception_textchange(obj)
	{
		// TODO : Add & Remove automatically the custom pages in the em_list_x
		var shopID = obj.attr('id').replace(/\D/g, '');
		var list = obj.parent().find('#em_list_' + shopID);
		var values = obj.val().split(',');
		var len = values.length;

		list.find('option').prop('selected', false);
		for (var i = 0; i < len; i++)
			list.find('option[value="' + $.trim(values[i]) + '"]').prop('selected', true);			
	}
	
	function position_exception_listchange(obj)
	{
		var shopID = obj.attr('id').replace(/\D/g, '');
		var str = '';
		obj.find('option:selected').each(function(){
			str += obj.val()+', ';
		});

		if (str.length > 2)
			str = str.substr(0, str.length - 2);
		obj.parent().find('#em_text_' + shopID).val(str);
	}
	
	$(document).ready(function()
	{
		$('form[id="hook_module_form"] input[id^="em_text_"]').each(function(){		
			$(this).on('change', position_exception_textchange($(this))).change();
		});
		$('form[id="hook_module_form"] select[id^="em_list_"]').each(function(){				
			$(this).on('change', position_exception_listchange($(this)));
		});
	});
	//]]>
</script>