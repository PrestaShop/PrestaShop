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

<form action="{$url_submit}" id={$table}_form method="post" class="form-horizontal">
	{if $display_key}
		<input type="hidden" name="show_modules" value="{$display_key}" />
	{/if}
	<fieldset>
		<h3>
			<i class="icon-paste"></i>
			{l s='Transplant a module'}
		</h3>
		<div class="row">
			<label class="control-label col-lg-3 required"> {l s='Module:'}</label>
			<div class="col-lg-6">
				<select name="id_module" {if $edit_graft} disabled="disabled"{/if}>
					{foreach $modules as $module}
						<option value="{$module->id}" {if $id_module == $module->id || (!$id_module && $show_modules == $module->id)}selected="selected"{/if}>{$module->displayName|stripslashes}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="row">
			<label class="control-label col-lg-3 required"> {l s='Hook into'} :</label>
			<div class="col-lg-6">
				<select name="id_hook" {if $edit_graft} disabled="disabled"{/if}>
					{foreach $hooks as $hook}
						<option value="{$hook['id_hook']}" {if $id_hook == $hook['id_hook']} selected="selected"{/if}>{$hook['name']}{if $hook['name'] != $hook['title']} ({$hook['title']}){/if}</option>
					{/foreach}
				</select>
			</div>
		</div>
	
		<div class="row">
			<label class="control-label col-lg-3">{l s='Exceptions'} :</label>
			<div class="col-lg-6">
				{if !$except_diff}
					{$exception_list}
				{else}
					{foreach $exception_list_diff as $value}
						{$value}
					{/foreach}
				{/if}
				<div class="alert alert-block">
					<p>{l s='Please specify the files for which you do not want the module to be displayed.'}.</p>
					<p>{l s='Please input each filename, separated by a comma.'}.</p>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12">
				{if $edit_graft}
					<input type="hidden" name="id_module" value="{$id_module}" />
					<input type="hidden" name="id_hook" value="{$id_hook}" />
				{/if}
				<input type="submit" value="{l s='Save'}" name="{if $edit_graft}submitEditGraft{else}submitAddToHook{/if}" id="{$table}_form_submit_btn" class="btn btn-primary" />
				<p>
					<span class="text-danger">*</span> 
					{l s='Required field'}
				</p>
			</div>
		</div>

		<script type="text/javascript">
			//<![CDATA
			function position_exception_add(shopID)
			{
				var listValue = $('#em_list_'+shopID).val();
				var inputValue = $('#em_text_'+shopID).val();
				var r = new RegExp('(^|,) *'+listValue+' *(,|$)');
				if (!r.test(inputValue))
					$('#em_text_'+shopID).val(inputValue + ((inputValue.trim()) ? ', ' : '') + listValue);
			}
		
			function position_exception_remove(shopID)
			{
				var listValue = $('#em_list_'+shopID).val();
				var inputValue = $('#em_text_'+shopID).val();
				var r = new RegExp('(^|,) *'+listValue+' *(,|$)');
				if (r.test(inputValue))
				{
					var rep = '';
					if (new RegExp(', *'+listValue+' *,').test(inputValue))
						$('#em_text_'+shopID).val(inputValue.replace(r, ','));
					else if (new RegExp(listValue+' *,').test(inputValue))
						$('#em_text_'+shopID).val(inputValue.replace(r, ''));
					else
						$('#em_text_'+shopID).val(inputValue.replace(r, ''));
				}
			}
			//]]>
		</script>

	</fieldset>
</form>