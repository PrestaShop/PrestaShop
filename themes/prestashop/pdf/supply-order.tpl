{*
* 2007-2011 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
		</table>
	</div>
	<!-- / SUPPLIER ADDRESS -->

	<table>
		<tr><td style="line-height: 8px">&nbsp;</td></tr>
	</table>

	{l s='Products to order:'}
	<!-- PRODUCTS -->
	<div style="font-size: 6pt;">
		<table style="width: 100%;">
			<tr style="line-height:6px; border: none">
				<td style="width: 8%; text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 5px; font-weight: bold;">{l s='Reference'}</td>
				<td style="width: 34%; text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 5px; font-weight: bold;">{l s='Designation'}</td>
				<td style="width: 3%; text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 5px; font-weight: bold;">{l s='Qty'}</td>
				<td style="width: 9%; text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 5px; font-weight: bold;">{l s='Unit Price TE'}</td>
				<td style="width: 9%; text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 5px; font-weight: bold;">{l s='Total TE BD'}</td>
				<td style="width: 10%; text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 5px; font-weight: bold;">{l s='Discount Rate'}</td>
				<td style="width: 9%; text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 5px; font-weight: bold;">{l s='Total TE AD'}</td>
				<td style="width: 9%; text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 5px; font-weight: bold;">{l s='Tax rate'}</td>
				<td style="width: 9%; text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 5px; font-weight: bold;">{l s='Total TI'}</td>
			</tr>
			{* for each product ordered *}
			{foreach $supply_order_details as $supply_order_detail}
			<tr>
				<td>{$supply_order_detail->supplier_reference}</td>
				<td>{$supply_order_detail->name}</td>
				<td>{$supply_order_detail->quantity_expected}</td>
				<td>{$supply_order_detail->unit_price_te}</td>
				<td>{$supply_order_detail->price_te}</td>
				<td>{$supply_order_detail->discount_rate}</td>
				<td>{$supply_order_detail->price_with_discount_te}</td>
				<td>{$supply_order_detail->tax_rate}</td>
				<td>{$supply_order_detail->price_ti}</td>
			</tr>
			{/foreach}
		</table>
	</div>
	<!-- / PRODUCTS -->
	
	<table>
		<tr><td style="line-height: 8px">&nbsp;</td></tr>
	</table>

	{l s='Taxes:'}
	<!-- PRODUCTS TAXES -->
	<div style="font-size: 6pt;">
		<table style="width: 30%;">
				<tr style="line-height:6px; border: none">
					<td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 5px; font-weight: bold;">{l s='Base TE'}</td>
					<td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 5px; font-weight: bold;">{l s='Tax Rate'}</td>
					<td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 5px; font-weight: bold;">{l s='Tax Value'}</td>
				</tr>
				{foreach $tax_order_summary as $entry}
				<tr style="line-height:6px; border: none">
					<td>{$entry['base_te']}</td>
					<td>{$entry['tax_rate']}</td>
					<td>{$entry['total_tax_value']}</td>
				</tr>
				{/foreach}
		</table>
	</div>
	<!-- / PRODUCTS TAXES -->
	
	<table>
		<tr><td style="line-height: 8px">&nbsp;</td></tr>
	</table>
	
	{l s='Summary'}
	<!-- TOTAL -->
	<div style="font-size: 6pt;">
		<table style="width: 30%;">
				<tr style="line-height:6px; border: none">
					<td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 5px; font-weight: bold;">{l s='Total TE'}</td>
					<td>{$supply_order->total_te}</td>
				</tr>
				<tr style="line-height:6px; border: none">
					<td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 5px; font-weight: bold;">{l s='Order Discount'}</td>
					<td>{$supply_order->discount_value_te}</td>
				</tr>
				<tr style="line-height:6px; border: none">
					<td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 5px; font-weight: bold;">{l s='Total AD TE'}</td>
					<td>{$supply_order->total_with_discount_te}</td>
				</tr>
				<tr style="line-height:6px; border: none">
					<td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 5px; font-weight: bold;">{l s='Tax value'}</td>
					<td>{$supply_order->total_tax}</td>
				</tr>
				<tr style="line-height:6px; border: none">
					<td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 5px; font-weight: bold;">{l s='Total TI'}</td>
					<td>{$supply_order->total_ti}</td>
				</tr>
				<tr style="line-height:6px; border: none">
					<td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 5px; font-weight: bold;">{l s='TOTAL TO PAY'}</td>
					<td>{$currency->prefix} {$supply_order->total_ti} {$currency->suffix}</td>
				</tr>
		</table>
	</div>
	<!-- / TOTAL -->

	
</div>