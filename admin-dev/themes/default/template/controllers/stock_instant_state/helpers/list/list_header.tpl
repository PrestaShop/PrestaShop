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
{extends file="helpers/list/list_header.tpl"}
{block name=override_header}
<div class="filter-stock">
	<form id="stock_instant_state" type="get">
		<input type="hidden" name="controller" value="AdminStockInstantState" />
		<input type="hidden" name="token" value="{$token}" />
	{if count($stock_instant_state_warehouses) > 0}
		<div id="stock_instant_state_form_warehouse">
			<label for="id_warehouse">{l s='Filter by warehouse:'}</label>
			<select name="id_warehouse" onChange="$(this).parent().parent().submit();">
				{foreach from=$stock_instant_state_warehouses key=k item=i}
					<option {if $i.id_warehouse == $stock_instant_state_cur_warehouse} selected="selected"{/if} value="{$i.id_warehouse}">{$i.name}</option>
				{/foreach}
			</select>
		</div>
	{/if}
	</form>
</div>
{/block}