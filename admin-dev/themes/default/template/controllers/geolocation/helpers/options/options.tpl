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

{extends file="helpers/options/options.tpl"}
{block name="field"}
	{if $field['type'] == 'checkbox_table'}
		<div class="margin-form" style="float: left; padding-left: 0; width: 317px; margin-top: 6px; height: 300px; overflow-y: auto;">
			<table class="table" cellspacing="0">
				<thead>
					<tr>
						<th><input type="checkbox" name="checkAll" onclick="checkDelBoxes(this.form, 'countries[]', this.checked)" /></th>
						<th>{l s='Name'}</th>
					<tr>
				</thead>
				<tbody>
					{foreach $field['list'] as $country}
						<tr>
							<td><input type="checkbox" name="countries[]" value="{$country[$field['identifier']]}" {if in_array(strtoupper($country['iso_code']), $allowed_countries)}checked="checked"{/if} /></td>
							<td>{$country['name']|escape:'htmlall':'UTF-8'}</td>
						</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
		<div class="clear"></div>
		<br />
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name="input"}
	{if $field['type'] == 'textarea_newlines'}
		<textarea name={$key} cols="{$field['cols']}" rows="{$field['rows']}">{$field['value']|replace:';':"\n"|escape:'htmlall':'UTF-8'}</textarea>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}
