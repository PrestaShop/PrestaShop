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
{if isset($warehouses) && count($warehouses) > 0 && isset($filter_status)}
<div class="filter-stock-extended">
	<form id="supply_orders" type="get">
		<input type="hidden" name="controller" value="AdminSupplyOrders" />
		<input type="hidden" name="token" value="{$token}" />
		<div>
			<label for="id_warehouse">{l s='Filter by warehouse:'}</label>
			<select name="id_warehouse" onChange="$(this).parent().parent().submit();">
			{foreach from=$warehouses key=k item=i}
				<option {if $i.id_warehouse == $current_warehouse} selected="selected"{/if} value="{$i.id_warehouse}">{$i.name}</option>
			{/foreach}
			</select>
		</div>
		<div style="margin-top: 5px;">
			<label for="filter_status">{l s='Choose not to display completed/canceled orders:'}</label>
			<input type="checkbox" name="filter_status" class="noborder" onChange="$(this).parent().parent().submit();" {if $filter_status == 1}value="on" checked{/if}></input>
		</div>
	</form>
</div>
{/if}
{/block}


		