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
*  @version  Release: $Revision: 6753 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div style="font-size: 8pt; color: #444">

<table>
	<tr><td>&nbsp;</td></tr>
</table>

<!-- ADDRESSES -->
<table style="width: 100%">
	<tr>
		<td style="width: 15%"></td>
		<td style="width: 85%">
			{if !empty($delivery_address)}
				<table style="width: 100%">
					<tr>
						<td style="width: 50%">
							<span style="font-weight: bold; font-size: 10pt; color: #9E9F9E">{l s='Delivery Address' pdf='true'}</span><br />
							 {$delivery_address}
						</td>
						<td style="width: 50%">
							<span style="font-weight: bold; font-size: 10pt; color: #9E9F9E">{l s='Billing Address' pdf='true'}</span><br />
							 {$invoice_address}
						</td>
					</tr>
				</table>
			{else}
				<table style="width: 100%">
					<tr>
						<td style="width: 50%">
							<span style="font-weight: bold; font-size: 10pt; color: #9E9F9E">{l s='Billing & Delivery Address.' pdf='true'}</span><br />
							 {$invoice_address}
						</td>
						<td style="width: 50%">

						</td>
					</tr>
				</table>
			{/if}
		</td>
	</tr>
</table>
<!-- / ADDRESSES -->

<div style="line-height: 1pt">&nbsp;</div>

<!-- PRODUCTS TAB -->
<table style="width: 100%">
	<tr>
		<td style="width: 15%; padding-right: 7px; text-align: right; vertical-align: top; font-size: 7pt;">
			<!-- CUSTOMER INFORMATIONS -->
			<b>{l s='Order Number:' pdf='true'}</b><br />
			{$order->getUniqReference()}<br />
			<br />
			<b>{l s='Order Date:' pdf='true'}</b><br />
			{$order->date_add|date_format:"%d-%m-%Y %H:%M"}<br />
			<br />
			<b>{l s='Payment Method:' pdf='true'}</b><br />
			{$order->payment}<br />
			<br />
			<!-- / CUSTOMER INFORMATIONS -->
		</td>
		<td style="width: 85%; text-align: right">
			<table style="width: 100%; font-size: 8pt;">
				<tr style="line-height:4px;">
					<td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 10px; font-weight: bold; width: 60%">{l s='Product / Reference' pdf='true'}</td>
                    <!-- unit price tax excluded is mandatory -->
					<td style="background-color: #4D4D4D; color: #FFF; text-align: center; font-weight: bold; width: 10%">{l s='Qty' pdf='true'}</td>
				    <td style="background-color: #4D4D4D; color: #FFF; text-align: right; font-weight: bold; width: 15%">{l s='Price' pdf='true'}<br />{l s='(Tax Excl.)' pdf='true'}</td>
				    <td style="background-color: #4D4D4D; color: #FFF; text-align: right; font-weight: bold; width: 15%">{l s='Price' pdf='true'}<br />{l s='(Tax Incl.)' pdf='true'}</td>
				</tr>
				{foreach $order_details as $order_detail}
				{cycle values='#FFF,#DDD' assign=bgcolor}
				<tr style="line-height:6px;background-color:{$bgcolor};">
					<td style="text-align: left; width: 60%">{$order_detail.product_name}</td>

					<td style="text-align: center; width: 10%">{$order_detail.product_quantity}</td>

				    <td style="text-align: right; width: 15%">
						- {displayPrice currency=$order->id_currency price=$order_detail.total_price_tax_excl}
                    </td>

					<td style="text-align: right; width: 15%">
						- {displayPrice currency=$order->id_currency price=$order_detail.total_price_tax_incl}
					</td>
				</tr>
					{foreach $order_detail.customizedDatas as $customizationPerAddress}
						{foreach $customizationPerAddress as $customizationId => $customization}
							<tr style="line-height:6px;background-color:{$bgcolor}; ">
								<td style="line-height:3px; text-align: left; width: 60%; vertical-align: top">
									{foreach $customization.datas as $customization_types}
										<blockquote>
											{if isset($customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_]) && count($customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_]) > 0}
												{foreach $customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_] as $customization_infos}
													{$customization_infos.name}: {$customization_infos.value}
													{if !$smarty.foreach.custo_foreach.last}<br />
													{else}
													<div style="line-height:0.4pt">&nbsp;</div>
													{/if}
												{/foreach}
											{/if}

											{if isset($customization.datas[$smarty.const._CUSTOMIZE_FILE_]) && count($customization.datas[$smarty.const._CUSTOMIZE_FILE_]) > 0}
												{count($customization.datas[$smarty.const._CUSTOMIZE_FILE_])} {l s='image(s)' pdf='true'}
											{/if}
										</blockquote>
									{/foreach}
								</td>
								<td style="text-align: right; width: 15%"></td>
								<td style="text-align: center; width: 10%; vertical-align: top">({$customization.quantity})</td>
								<td style="width: 15%; text-align: right;"></td>
							</tr>
						{/foreach}
					{/foreach}
				{/foreach}
			</table>

			<table style="width: 100%">
				{if $order_slip->shipping_cost_amount}
				<tr style="line-height:5px;">
					<td style="width: 85%; text-align: right; font-weight: bold">{l s='Shipping' pdf='true'}</td>
					<td style="width: 15%; text-align: right;">- {displayPrice currency=$order->id_currency price=$order_slip->shipping_cost_amount}</td>
				</tr>
				{/if}

				{if (($order->total_paid_tax_incl - $order->total_paid_tax_excl) > 0)}
				<tr style="line-height:5px;">
					<td style="width: 85%; text-align: right; font-weight: bold">{l s='Product Total (Tax Excl.)' pdf='true'}</td>
					<td style="width: 15%; text-align: right;">- {displayPrice currency=$order->id_currency price=$order->total_products}</td>
				</tr>

				<tr style="line-height:5px;">
					<td style="width: 85%; text-align: right; font-weight: bold">{l s='Product Total (Tax Incl.)' pdf='true'}</td>
					<td style="width: 15%; text-align: right;">- {displayPrice currency=$order->id_currency price=$order->total_products_wt}</td>
				</tr>
				{else}
				<tr style="line-height:5px;">
					<td style="width: 85%; text-align: right; font-weight: bold">{l s='Product Total' pdf='true'}</td>
					<td style="width: 15%; text-align: right;">- {displayPrice currency=$order->id_currency price=$order->total_products}</td>
				</tr>
				{/if}

				{if ($order->total_paid_tax_incl - $order->total_paid_tax_excl) > 0}
				<tr style="line-height:5px;">
					<td style="text-align: right; font-weight: bold">{l s='Total Tax' pdf='true'}</td>
					<td style="width: 15%; text-align: right;">- {displayPrice currency=$order->id_currency price=($order->total_paid_tax_incl - $order->total_paid_tax_excl)}</td>
				</tr>
				{/if}

				<tr style="line-height:5px;">
					<td style="text-align: right; font-weight: bold">{l s='Total ' pdf='true'}</td>
					<td style="width: 15%; text-align: right;">- {displayPrice currency=$order->id_currency price=$order->total_paid_tax_incl}</td>
				</tr>
			</table>

		</td>
	</tr>
</table>
<!-- / PRODUCTS TAB -->

<div style="line-height: 1pt">&nbsp;</div>

{$tax_tab}

{if isset($HOOK_DISPLAY_PDF)}
<div style="line-height: 1pt">&nbsp;</div>
<table style="width: 100%">
    <tr>
        <td style="width: 15%"></td>
        <td style="width: 85%">
            {$HOOK_DISPLAY_PDF}
        </td>
    </tr>
</table>
{/if}

</div>

