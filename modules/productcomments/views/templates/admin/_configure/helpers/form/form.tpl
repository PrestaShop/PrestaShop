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

{extends file="helpers/form/form.tpl"}

{block name="input"}
	{if $input.type == 'products'}
		<table id="{$input.name}">
			<tr>
				<th></th>
				<th>ID</th>
				<th width="80%">{l s='Product Name'}</th>
			</tr>
			{foreach $input.values as $value}
				<tr>
					<td>
						<input type="checkbox" name="{$input.name}[]" value="{$value.id_product}" 
						{if isset($value.selected) && $value.selected == 1} checked {/if} />
					</td>
					<td>{$value.id_product}</td>
					<td width="80%">{$value.name}</td>
				</tr>
			{/foreach}
		</table>
    {elseif $input.type == 'switch' && $smarty.const._PS_VERSION_|@addcslashes:'\'' < '1.6'}
		{foreach $input.values as $value}
			<input type="radio" name="{$input.name}" id="{$value.id}" value="{$value.value|escape:'html':'UTF-8'}"
					{if $fields_value[$input.name] == $value.value}checked="checked"{/if}
					{if isset($input.disabled) && $input.disabled}disabled="disabled"{/if} />
			<label class="t" for="{$value.id}">
			 {if isset($input.is_bool) && $input.is_bool == true}
				{if $value.value == 1}
					<img src="../img/admin/enabled.gif" alt="{$value.label}" title="{$value.label}" />
				{else}
					<img src="../img/admin/disabled.gif" alt="{$value.label}" title="{$value.label}" />
				{/if}
			 {else}
				{$value.label}
			 {/if}
			</label>
			{if isset($input.br) && $input.br}<br />{/if}
			{if isset($value.p) && $value.p}<p>{$value.p}</p>{/if}
		{/foreach}
	{else}
		{$smarty.block.parent}
    {/if}

{/block}
