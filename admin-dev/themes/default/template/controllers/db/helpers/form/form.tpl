{*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 8971 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{extends file="helpers/form/form.tpl"}

{block name="label"}
	{if $input.type == 'table'}
		<table cellspacing="0" cellpadding="0" class="table width2 clear">
			<tr>
				<th><input type="checkbox" onclick="checkDelBoxes(this.form, 'tablesBox[]', this.checked)" class="noborder" name="checkme"></th>
				<th>{l s='Table'}</th><th>{l s='Table Engine'}</th>
			</tr>
			{foreach $table_status AS $table}
				<tr class="{if $table@iteration is even}alt_row{/if}">
					<td class="noborder"><input type="checkbox" name="tablesBox[]" value="{$table['Name']}"/></td><td>{$table['Name']}</td><td>{$table['Engine']}</td>
				</tr>
			{/foreach}
		</table>
	{/if}
	{if isset($input.label)}
		<label>{$input.label} </label>
	{/if}
{/block}
