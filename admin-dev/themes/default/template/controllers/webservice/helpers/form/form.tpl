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
{extends file="helpers/form/form.tpl"}

{block name="description"}
	{$smarty.block.parent}
	{if $input.type == 'text' && $input.name == 'key'}
		<input type="button" value="{l s='Generate!   '}" class="button" onclick="gencode(32)" />
	{/if}
{/block}

{block name="input"}
	{if $input.type == 'resources'}
		<p>{l s='Set the resource permissions for this key:'}</p>
		<table border="0" cellspacing="0" cellpadding="0" class="table accesses">
			<thead>
				<tr>
					<th class="center">{l s='Resource'}</th>
					<th width="30" class="center"></th>
					<th width="50" class="center">{l s='View (GET)'}</th>
					<th width="50" class="center">{l s='Modify (PUT)'}</th>
					<th width="50" class="center">{l s='Add (POST)'}</th>
					<th width="50" class="center">{l s='Delete (DELETE)'}</th>
					<th width="50" class="center">{l s='Fast view (HEAD)'}</th>
				</tr>
			</thead>
			<tbody>
				<tr class="all" style="vertical-align:middle">
					<th></th>
					<th></th>
					<th class="center"><input type="checkbox" class="all_get get " /></th>
					<th class="center"><input type="checkbox" class="all_put put " /></th>
					<th class="center"><input type="checkbox" class="all_post post " /></th>
					<th class="center"><input type="checkbox" class="all_delete delete" /></th>
					<th class="center"><input type="checkbox" class="all_head head" /></th>
				</tr>
				{foreach $ressources as $resource_name => $resource}
					<tr>
						<th class="center">{$resource_name}</th>
						<th class="center"><input type="checkbox" class="all"/></th>
						<td class="center"><input type="checkbox" {if isset($ressources[$resource_name]['forbidden_method']) && in_array('GET', $ressources[$resource_name]['forbidden_method'])}disabled="disabled"{/if} class="get" name="resources[{$resource_name}][GET]" {if isset($permissions[$resource_name]) && in_array('GET', $permissions[$resource_name])}checked="checked"{/if} /></td>
						<td class="center"><input type="checkbox" {if isset($ressources[$resource_name]['forbidden_method']) && in_array('PUT', $ressources[$resource_name]['forbidden_method'])}disabled="disabled"{/if} class="put" name="resources[{$resource_name}][PUT]" {if isset($permissions[$resource_name]) && in_array('PUT', $permissions[$resource_name])}checked="checked"{/if}/></td>
						<td class="center"><input type="checkbox" {if isset($ressources[$resource_name]['forbidden_method']) && in_array('POST', $ressources[$resource_name]['forbidden_method'])}disabled="disabled"{/if} class="post" name="resources[{$resource_name}][POST]" {if isset($permissions[$resource_name]) && in_array('POST', $permissions[$resource_name])}checked="checked"{/if}/></td>
						<td class="center"><input type="checkbox" {if isset($ressources[$resource_name]['forbidden_method']) && in_array('DELETE', $ressources[$resource_name]['forbidden_method'])}disabled="disabled"{/if} class="delete" name="resources[{$resource_name}][DELETE]" {if isset($permissions[$resource_name]) && in_array('DELETE', $permissions[$resource_name])}checked="checked"{/if}/></td>
						<td class="center"><input type="checkbox" {if isset($ressources[$resource_name]['forbidden_method']) && in_array('HEAD', $ressources[$resource_name]['forbidden_method'])}disabled="disabled"{/if} class="head" name="resources[{$resource_name}][HEAD]" {if isset($permissions[$resource_name]) && in_array('HEAD', $permissions[$resource_name])}checked="checked"{/if}/></td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name="script"}
	$(function() {
		$('table.accesses input.all').click(function() {
			if($(this).is(':checked'))
				$(this).parent().parent().find('input.get:not(:checked), input.put:not(:checked), input.post:not(:checked), input.delete:not(:checked), input.head:not(:checked)').click();
			else
				$(this).parent().parent().find('input.get:checked, input.put:checked, input.post:checked, input.delete:checked, input.head:checked').click();
		});
		$('table.accesses .all_get').click(function() {
			if($(this).is(':checked'))
				$(this).parent().parent().parent().find('input.get:not(:checked)').click();
			else
				$(this).parent().parent().parent().find('input.get:checked').click();
		});
		$('table.accesses .all_put').click(function() {
			if($(this).is(':checked'))
				$(this).parent().parent().parent().find('input.put:not(:checked)').click();
			else
				$(this).parent().parent().parent().find('input.put:checked').click();
		});
		$('table.accesses .all_post').click(function() {
			if($(this).is(':checked'))
				$(this).parent().parent().parent().find('input.post:not(:checked)').click();
			else
				$(this).parent().parent().parent().find('input.post:checked').click();
		});
		$('table.accesses .all_delete').click(function() {
			if($(this).is(':checked'))
				$(this).parent().parent().parent().find('input.delete:not(:checked)').click();
			else
				$(this).parent().parent().parent().find('input.delete:checked').click();
		});
		$('table.accesses .all_head').click(function() {
			if($(this).is(':checked'))
				$(this).parent().parent().parent().find('input.head:not(:checked)').click();
			else
				$(this).parent().parent().parent().find('input.head:checked').click();
		});
	});
{/block}
