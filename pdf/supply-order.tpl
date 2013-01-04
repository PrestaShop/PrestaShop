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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div style="font-size: 9pt; color: #444">

	<!-- SHOP ADDRESS -->
	<div>
		<table style="width: 100%">
			<tr>
				<td style="font-size: 13pt; font-weight: bold">{$shop_name}</td>
			</tr>
			<tr>
				<td style="font-size: 13pt; font-weight: bold">{$address_warehouse->address1}</td>
			</tr>
			{* if the address has two parts *}
			{if !empty($address_warehouse->address2)}
			<tr>
				<td style="font-size: 13pt; font-weight: bold">{$address_warehouse->address2}</td>
			</tr>
			{/if}
			<tr>
				<td style="font-size: 13pt; font-weight: bold">{$address_warehouse->postcode} {$address_warehouse->city}</td>
			</tr>
		</table>
	</div>
	<!-- / SHOP ADDRESS -->
	
	<!-- SUPPLIER ADDRESS -->
	<div style="text-align: right;">
		<table style="width: 70%">
			<tr>
				<td style="font-size: 13pt; font-weight: bold">{$supply_order->supplier_name}</td>
			</tr>
			<tr>
				<td style="font-size: 13pt; font-weight: bold">{$address_supplier->address1}</td>
			</tr>
			{* if the address has two parts *}
			{if !empty($address_supplier->address2)}
			<tr>
				<td style="font-size: 13pt; font-weight: bold">{$address_supplier->address2}</td>
			</tr>
			{/if}
			<tr>
				<td style="font-size: 13pt; font-weight: bold">{$address_supplier->postcode} {$address_supplier->city}</td>
			</tr>
			<tr>
				<td style="font-size: 13pt; font-weight: bold">{$address_supplier->country}</td>
			</tr>
		</table>
	</div>
	<!-- / SUPPLIER ADDRESS -->

	<table>
		<tr><td style="line-height: 8px">&nbsp;</td></tr>
	</table>

	{l s='Products ordered:' pdf='true'}
	<!-- PRODUCTS -->
	<div style="font-size: 5pt;">
		<table style="width: 100%;">
			<tr style="line-height:6px; border: none">
				<td style="width: 14%; text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 2px; font-weight: bold;">{l s='Reference' pdf='true'}</td>
				<td style="width: 21%; text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 2px; font-weight: bold;">{l s='Designation' pdf='true'}</td>
				<td style="width: 5%; text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 2px; font-weight: bold;">{l s='Qty' pdf='true'}</td>
				<td style="width: 10%; text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 2px; font-weight: bold;">{l s='Unit Price TE' pdf='true'}</td>
				<td style="width: 11%; text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 2px; font-weight: bold;">{l s='Total TE' pdf='true'} <br /> {l s='Before discount' pdf='true'}</td>
				<td style="width: 9%; text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 2px; font-weight: bold;">{l s='Discount Rate' pdf='true'}</td>
				<td style="width: 11%; text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 2px; font-weight: bold;">{l s='Total TE' pdf='true'} <br /> {l s='After discount' pdf='true'}</td>
				<td style="width: 9%; text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 2px; font-weight: bold;">{l s='Tax rate' pdf='true'}</td>
				<td style="width: 10%; text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 2px; font-weight: bold;">{l s='Total TI' pdf='true'}</td>
			</tr>
			{* for each product ordered *}
			{foreach $supply_order_details as $supply_order_detail}
			<tr>
				<td style="text-align: left; padding-left: 1px;">{$supply_order_detail->supplier_reference}</td>
				<td style="text-align: left; padding-left: 1px;">{$supply_order_detail->name}</td>
				<td style="text-align: right; padding-right: 1px;">{$supply_order_detail->quantity_expected}</td>
				<td style="text-align: right; padding-right: 1px;">{$currency->prefix} {$supply_order_detail->unit_price_te} {$currency->suffix}</td>
				<td style="text-align: right; padding-right: 1px;">{$currency->prefix} {$supply_order_detail->price_te} {$currency->suffix}</td>
				<td style="text-align: right; padding-right: 1px;">{$supply_order_detail->discount_rate}</td>
				<td style="text-align: right; padding-right: 1px;">{$currency->prefix} {$supply_order_detail->price_with_discount_te} {$currency->suffix}</td>
				<td style="text-align: right; padding-right: 1px;">{$supply_order_detail->tax_rate}</td>
				<td style="text-align: right; padding-right: 1px;">{$currency->prefix} {$supply_order_detail->price_ti} {$currency->suffix}</td>
			</tr>
			{/foreach}
		</table>
	</div>
	<!-- / PRODUCTS -->
	
	<table>
		<tr><td style="line-height: 8px">&nbsp;</td></tr>
	</table>

	{l s='Taxes:' pdf='true'}
	<!-- PRODUCTS TAXES -->
	<div style="font-size: 6pt;">
		<table style="width: 30%;">
				<tr style="line-height:6px; border: none">
					<td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 2px; font-weight: bold;">{l s='Base TE' pdf='true'}</td>
					<td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 2px; font-weight: bold;">{l s='Tax Rate' pdf='true'}</td>
					<td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 2px; font-weight: bold;">{l s='Tax Value' pdf='true'}</td>
				</tr>
				{foreach $tax_order_summary as $entry}
				<tr style="line-height:6px; border: none">
					<td style="text-align: right; padding-right: 1px;">{$currency->prefix} {$entry['base_te']} {$currency->suffix}</td>
					<td style="text-align: right; padding-right: 1px;">{$entry['tax_rate']}</td>
					<td style="text-align: right; padding-right: 1px;">{$currency->prefix} {$entry['total_tax_value']} {$currency->suffix}</td>
				</tr>
				{/foreach}
		</table>
	</div>
	<!-- / PRODUCTS TAXES -->
	
	<table>
		<tr><td style="line-height: 8px">&nbsp;</td></tr>
	</table>
	
	{l s='Summary:' pdf='true'}
	<!-- TOTAL -->
	<div style="font-size: 6pt;">
		<table style="width: 30%;">
				<tr style="line-height:6px; border: none">
					<td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 2px; font-weight: bold;">{l s='Total TE' pdf='true'} <br /> {l s='(Discount excluded)' pdf='true'}</td>
					<td width="43px" style="text-align: right;">{$currency->prefix} {$supply_order->total_te} {$currency->suffix}</td>
				</tr>
				<tr style="line-height:6px; border: none">
					<td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 2px; font-weight: bold;">{l s='Order Discount' pdf='true'}</td>
					<td width="43px" style="text-align: right;">{$currency->prefix} {$supply_order->discount_value_te} {$currency->suffix}</td>
				</tr>
				<tr style="line-height:6px; border: none">
					<td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 2px; font-weight: bold;">{l s='Total TE' pdf='true'} <br /> {l s='(Discount included)' pdf='true'}</td>
					<td width="43px" style="text-align: right;">{$currency->prefix} {$supply_order->total_with_discount_te} {$currency->suffix}</td>
				</tr>
				<tr style="line-height:6px; border: none">
					<td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 2px; font-weight: bold;">{l s='Tax value' pdf='true'}</td>
					<td width="43px" style="text-align: right;">{$currency->prefix} {$supply_order->total_tax} {$currency->suffix}</td>
				</tr>
				<tr style="line-height:6px; border: none">
					<td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 2px; font-weight: bold;">{l s='Total TI' pdf='true'}</td>
					<td width="43px" style="text-align: right;">{$currency->prefix} {$supply_order->total_ti} {$currency->suffix}</td>
				</tr>
				<tr style="line-height:6px; border: none">
					<td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 2px; font-weight: bold;">{l s='TOTAL TO PAY' pdf='true'}</td>
					<td width="43px" style="text-align: right;">{$currency->prefix} {$supply_order->total_ti} {$currency->suffix}</td>
				</tr>
		</table>
	</div>
	<!-- / TOTAL -->
</div>
