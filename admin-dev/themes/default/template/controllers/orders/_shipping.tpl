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
<table class="table" width="100%" cellspacing="0" cellpadding="0" id="shipping_table">
<colgroup>
	<col width="15%">
	<col width="15%">
	<col width="">
	<col width="10%">
	<col width="20%">
</colgroup>
	<thead>
	<tr>
		<th>{l s='Date'}</th>
		<th>{l s='Type'}</th>
		<th>{l s='Carrier'}</th>
		<th>{l s='Weight'}</th>
		<th>{l s='Shipping cost'}</th>
		<th>{l s='Tracking number'}</th>
	</tr>
	</thead>
	<tbody>
	{foreach from=$order->getShipping() item=line}
	<tr>
		<td>{dateFormat date=$line.date_add full=true}</td>
		<td>{$line.type}</td>
		<td>{$line.carrier_name}</td>
		<td>{$line.weight|string_format:"%.3f"} {Configuration::get('PS_WEIGHT_UNIT')}</td>
		<td>
			{if $order->getTaxCalculationMethod() == $smarty.const.PS_TAX_INC}
				{displayPrice price=$line.shipping_cost_tax_incl currency=$currency->id}
			{else}
				{displayPrice price=$line.shipping_cost_tax_excl currency=$currency->id}
			{/if}
		</td>
		<td>
			<span id="shipping_number_show">{if $line.url && $line.tracking_number}<a href="{$line.url|replace:'@':$line.tracking_number}">{$line.tracking_number}</a>{else}{$line.tracking_number}{/if}</span>
			{if $line.can_edit}
				<form style="display: inline;" method="post" action="{$link->getAdminLink('AdminOrders')|escape:'htmlall':'UTF-8'}&vieworder&id_order={$order->id|escape:'htmlall':'UTF-8'}">
					<span class="shipping_number_edit" style="display:none;">
						<input type="hidden" name="id_order_carrier" value="{$line.id_order_carrier|htmlentities}" />
						<input type="text" name="tracking_number" value="{$line.tracking_number|htmlentities}" />
						<input type="submit" class="button" name="submitShippingNumber" value="{l s='Update'}" />
					</span>
					<a href="#" class="edit_shipping_number_link"><img src="../img/admin/edit.gif" alt="{l s='Edit'}" /></a>
					<a href="#" class="cancel_shipping_number_link" style="display: none;"><img src="../img/admin/disabled.gif" alt="{l s='Cancel'}" /></a>
				</form>
			{/if}
		</td>
	</tr>
	{/foreach}
	</tbody>
</table>
