{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
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
			{dateFormat date=$order->date_add full=0}<br />
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
				    <td style="background-color: #4D4D4D; color: #FFF; text-align: right; font-weight: bold; width: 15%">{l s='Unit price' pdf='true'}<br />{if $tax_excluded_display}{l s='(Tax Excl.)' pdf='true'}{else}{l s='(Tax Incl.)' pdf='true'}{/if}</td>
				    <td style="background-color: #4D4D4D; color: #FFF; text-align: right; font-weight: bold; width: 15%">{l s='Price' pdf='true'}<br />{if $tax_excluded_display}{l s='(Tax Excl.)' pdf='true'}{else}{l s='(Tax Incl.)' pdf='true'}{/if}</td>
				</tr>
				{if !isset($order_details) || count($order_details) == 0}
				<tr style="line-height:6px;background-color:{$bgcolor};" colspan="4">
					<td>{l s='No details' pdf='true'}</td>
				</tr>
				{else}
					{foreach $order_details as $order_detail}
					{cycle values='#FFF,#DDD' assign=bgcolor}
					<tr style="line-height:6px;background-color:{$bgcolor};">
						<td style="text-align: left; width: 60%">{$order_detail.product_name}</td>

						<td style="text-align: center; width: 10%">{$order_detail.product_quantity}</td>

					    <td style="text-align: right; width: 15%">
							{if $tax_excluded_display}
							- {displayPrice currency=$order->id_currency price=$order_detail.unit_price_tax_excl}
							{else}
							- {displayPrice currency=$order->id_currency price=$order_detail.unit_price_tax_incl}
							{/if}
	                    </td>
						<td style="text-align: right; width: 15%">
							{if $tax_excluded_display}
							- {displayPrice currency=$order->id_currency price=$order_detail.total_price_tax_excl}
							{else}
							- {displayPrice currency=$order->id_currency price=$order_detail.total_price_tax_incl}
							{/if}
						</td>
					</tr>
						{foreach $order_detail.customizedDatas as $customizationPerAddress}
							{foreach $customizationPerAddress as $customizationId => $customization}
								<tr style="line-height:6px;background-color:{$bgcolor}; ">
									<td style="line-height:3px; text-align: left; width: 60%; vertical-align: top">
										{foreach $customization.datas as $customization_types}
											<blockquote>
												{if isset($customization.datas[Product::CUSTOMIZE_TEXTFIELD]) && count($customization.datas[Product::CUSTOMIZE_FILE]) > 0}
													{foreach $customization.datas[Product::CUSTOMIZE_TEXTFIELD] as $customization_infos}
														{$customization_infos.name}: {$customization_infos.value}
														{if !$smarty.foreach.custo_foreach.last}<br />
														{else}
														<div style="line-height:0.4pt">&nbsp;</div>
														{/if}
													{/foreach}
												{/if}

												{if isset($customization.datas[Product::CUSTOMIZE_FILE]) && count($customization.datas[Product::CUSTOMIZE_FILE]) > 0}
													{count($customization.datas[Product::CUSTOMIZE_FILE])} {l s='image(s)' pdf='true'}
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
				{/if}
				{assign var=total_cart_rule value=0}
				{if is_array($cart_rules) && count($cart_rules)}
					{foreach $cart_rules as $cart_rule}
						{cycle values='#FFF,#DDD' assign=bgcolor}
						<tr style="line-height:6px;background-color:{$bgcolor};text-align:left;">
							<td style="line-height:3px;text-align:left;width:85%;vertical-align:top" colspan="{if !$tax_excluded_display}5{else}4{/if}">{$cart_rule.name}</td>
							<td style="text-align: right; width: 15%">
								{if $tax_excluded_display}
									{$total_cart_rule = $total_cart_rule + $cart_rule.value_tax_excl}
									+ {$cart_rule.value_tax_excl}
								{else}
									{$total_cart_rule = $total_cart_rule + $cart_rule.value}
									+ {$cart_rule.value}
								{/if}
							</td>
						</tr>
					{/foreach}
				{/if}
			</table>

			<table style="width: 100%">
				{if $order_slip->shipping_cost_amount > 0}
				<tr style="line-height:5px;">
					{if $tax_excluded_display}
						<td style="width: 85%; text-align: right; font-weight: bold">{l s='Shipping (Tax Excl.)' pdf='true'}</td>
					{else}
						<td style="width: 85%; text-align: right; font-weight: bold">{l s='Shipping (Tax Incl.)' pdf='true'}</td>
					{/if}
					<td style="width: 15%; text-align: right;">- {displayPrice currency=$order->id_currency price=$order_slip->shipping_cost_amount}</td>
				</tr>
				{/if}
				{if isset($order_details) && count($order_details) > 0}
					{if (($order->total_paid_tax_incl - $order->total_paid_tax_excl) > 0)}
						{if $tax_excluded_display}
							<tr style="line-height:5px;">
								<td style="width: 85%; text-align: right; font-weight: bold">{l s='Product Total (Tax Excl.)' pdf='true'}</td>
								<td style="width: 15%; text-align: right;">- {displayPrice currency=$order->id_currency price=$order->total_products}</td>
							</tr>
						{else}
							<tr style="line-height:5px;">
								<td style="width: 85%; text-align: right; font-weight: bold">{l s='Product Total (Tax Incl.)' pdf='true'}</td>
								<td style="width: 15%; text-align: right;">- {displayPrice currency=$order->id_currency price=$order->total_products_wt}</td>
							</tr>
						{/if}
					{else}
					<tr style="line-height:5px;">
						<td style="width: 85%; text-align: right; font-weight: bold">{l s='Product Total' pdf='true'}</td>
						<td style="width: 15%; text-align: right;">- {displayPrice currency=$order->id_currency price=$order->total_products}</td>
					</tr>
					{/if}
				{/if}

				{if ($order->total_paid_tax_incl - $order->total_paid_tax_excl) > 0}
				<tr style="line-height:5px;">
					<td style="text-align: right; font-weight: bold">{l s='Total Tax' pdf='true'}</td>
					<td style="width: 15%; text-align: right;">- {displayPrice currency=$order->id_currency price=($order->total_paid_tax_incl - $order->total_paid_tax_excl)}</td>
				</tr>
				{/if}

				<tr style="line-height:5px;">
					{if $tax_excluded_display}
						<td style="text-align: right; font-weight: bold">{l s='Total (Tax Excl.)' pdf='true'}</td>
					{else}
						<td style="text-align: right; font-weight: bold">{l s='Total (Tax Incl.)' pdf='true'}</td>
					{/if}
					{if $total_cart_rule}
						{assign var=total_paid value=0}
						{if $tax_excluded_display}
							{$total_paid = $order->total_paid_tax_excl - $total_cart_rule}
						{else}
							{$total_paid = $order->total_paid_tax_incl - $total_cart_rule}
						{/if}
						<td style="width: 15%; text-align: right;">- {displayPrice currency=$order->id_currency price=$total_paid}</td>
					{elseif $amount_choosen}
						<td style="width: 15%; text-align: right;">- {displayPrice currency=$order->id_currency price=($order_slip->amount+$order_slip->shipping_cost_amount)}</td>
					{else}
						{if $tax_excluded_display}
							<td style="width: 15%; text-align: right;">- {displayPrice currency=$order->id_currency price=$order->total_paid_tax_excl}</td>
						{else}
							<td style="width: 15%; text-align: right;">- {displayPrice currency=$order->id_currency price=$order->total_paid_tax_incl}</td>
						{/if}
					{/if}
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
