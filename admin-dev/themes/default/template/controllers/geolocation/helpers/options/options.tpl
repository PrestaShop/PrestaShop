{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

{extends file="helpers/options/options.tpl"}
{block name="field"}
	{if $field['type'] == 'checkbox_table'}
		{*TODO : overflow*}
		<div class="well margin-form" style="height: 300px; overflow-y: auto;">
			<table class="table" style="border-spacing : 0; border-collapse : collapse;">
				<thead>
					<tr>
						<th><input type="checkbox" name="checkAll" onclick="checkDelBoxes(this.form, 'countries[]', this.checked)" /></th>
						<th>{l s='Name' d='Admin.Global'}</th>
					</tr>
				</thead>
				<tbody>
					{foreach $field['list'] as $country}
						<tr>
							<td><input type="checkbox" name="countries[]" value="{$country[$field['identifier']]}" {if in_array(strtoupper($country['iso_code']), $allowed_countries)}checked="checked"{/if} /></td>
							<td>{$country['name']|escape:'html':'UTF-8'}</td>
						</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name="input"}
	{if $field['type'] == 'textarea_newlines'}
		<div class="col-lg-9">
			<textarea name={$key} cols="{$field['cols']}" rows="{$field['rows']}">{$field['value']|replace:';':"\n"|escape:'html':'UTF-8'}</textarea>
		</div>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}
