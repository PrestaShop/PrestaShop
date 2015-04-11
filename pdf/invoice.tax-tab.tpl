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

{if $tax_exempt || ((isset($product_tax_breakdown) && $product_tax_breakdown|@count > 0) || (isset($ecotax_tax_breakdown) && $ecotax_tax_breakdown|@count > 0) || (isset($shipping_tax_breakdown) && $shipping_tax_breakdown|@count > 0))}
<!--  TAX DETAILS -->
<table style="width: 100%">
	<tr>
		<td style="width: 15%"></td>
		<td style="width: 60%">
			{if $tax_exempt}
				{l s='Exempt of VAT according section 259B of the General Tax Code.' pdf='true'}
			{else}
			{capture name=separator_row}
				<tr>
					<td style="line-height: 2px" colspan="{if $use_one_after_another_method}4{else}5{/if}"></td>
				</tr>
			{/capture}
			<table>
				<tr style="line-height:5px;">
					<td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 10px; font-weight: bold; width: {if !$use_one_after_another_method}30%{else}60%{/if}">{l s='Tax Detail' pdf='true'}</td>
					<td style="text-align: right; background-color: #4D4D4D; color: #FFF; padding-left: 10px; font-weight: bold; width: 20%">{l s='Tax Rate' pdf='true'}</td>
					{if !$use_one_after_another_method}
						<td style="text-align: right; background-color: #4D4D4D; color: #FFF; padding-left: 10px; font-weight: bold; width: 30%">{l s='Total (Tax Excl.)' pdf='true'}</td>
					{/if}
					<td style="text-align: right; background-color: #4D4D4D; color: #FFF; padding-left: 10px; font-weight: bold; width: 20%">{l s='Total Tax' pdf='true'}</td>
				</tr>

				{if isset($product_tax_breakdown)}
					{foreach $product_tax_breakdown as $rate => $product_tax_infos}
					<tr style="line-height:6px;background-color:{cycle values='#FFF,#DDD'};">
						 <td style="width: {if !$use_one_after_another_method}30%{else}60%{/if}">
							{if !isset($pdf_product_tax_written)}
								{l s='Products' pdf='true'}
								{assign var=pdf_product_tax_written value=1}
							{/if}
						</td>
						 <td style="width: 20%; text-align: right;">{$rate} %</td>
						{if !$use_one_after_another_method}
						 <td style="width: 30%; text-align: right;">
							 {if isset($is_order_slip) && $is_order_slip}- {/if}{displayPrice currency=$order->id_currency price=$product_tax_infos.total_price_tax_excl}
						 </td>
						{/if}
						 <td style="width: 20%; text-align: right;">{if isset($is_order_slip) && $is_order_slip}- {/if}{displayPrice currency=$order->id_currency price=$product_tax_infos.total_amount}</td>
					</tr>
					{/foreach}
				{/if}

				{if isset($ecotax_tax_breakdown)}
					{foreach $ecotax_tax_breakdown as $ecotax_tax_infos}
						{if $ecotax_tax_infos.ecotax_tax_excl > 0}
						{$smarty.capture.separator_row}
						<tr style="line-height:6px;background-color:{cycle values='#FFF,#DDD'};">
							<td style="width: {if !$use_one_after_another_method}30%{else}60%{/if}">{l s='Ecotax' pdf='true'}</td>
							<td style="width: 20%; text-align: right;">{$ecotax_tax_infos.rate  } %</td>
							{if !$use_one_after_another_method}
								<td style="width: 30%; text-align: right;">{if isset($is_order_slip) && $is_order_slip}- {/if}{displayPrice currency=$order->id_currency price=$ecotax_tax_infos.ecotax_tax_excl}</td>
							{/if}
							<td style="width: 20%; text-align: right;">{if isset($is_order_slip) && $is_order_slip}- {/if}{displayPrice currency=$order->id_currency price=($ecotax_tax_infos.ecotax_tax_incl - $ecotax_tax_infos.ecotax_tax_excl)}</td>
						</tr>
						{/if}
					{/foreach}
				{/if}

				{if isset($shipping_tax_breakdown)}
					{$smarty.capture.separator_row}
					{foreach $shipping_tax_breakdown as $shipping_tax_infos}
					<tr style="line-height:6px;background-color:{cycle values='#FFF,#DDD'};">
						 <td style="width: {if !$use_one_after_another_method}30%{else}60%{/if}">
							{if !isset($pdf_shipping_tax_written)}
								{l s='Shipping' pdf='true'}
								{assign var=pdf_shipping_tax_written value=1}
							{/if}
						 </td>
						 <td style="width: 20%; text-align: right;">{$shipping_tax_infos.rate} %</td>
						{if !$use_one_after_another_method}
							<td style="width: 30%; text-align: right;">{if isset($is_order_slip) && $is_order_slip}- {/if}{displayPrice currency=$order->id_currency price=$shipping_tax_infos.total_tax_excl}</td>
						{/if}
					 	<td style="width: 20%; text-align: right;">{if isset($is_order_slip) && $is_order_slip}- {/if}{displayPrice currency=$order->id_currency price=$shipping_tax_infos.total_amount}</td>
					</tr>
					{/foreach}
				{/if}

				{if isset($wrapping_tax_breakdown)}
					{$smarty.capture.separator_row}
					{foreach $wrapping_tax_breakdown as $wrapping_tax_infos}
						<tr style="line-height:6px;background-color:{cycle values='#FFF,#DDD'};">
							<td style="width: {if !$use_one_after_another_method}30%{else}60%{/if}">
								{if !isset($pdf_wrapping_tax_written)}
									{l s='Wrapping' pdf='true'}
									{assign var=pdf_wrapping_tax_written value=1}
								{/if}
							</td>
							<td style="width: 20%; text-align: right;">{$wrapping_tax_infos.rate} %</td>
							{if !$use_one_after_another_method}
								<td style="width: 30%; text-align: right;">{if isset($is_order_slip) && $is_order_slip}- {/if}{displayPrice currency=$order->id_currency price=$wrapping_tax_infos.total_tax_excl}</td>
							{/if}
							<td style="width: 20%; text-align: right;">{if isset($is_order_slip) && $is_order_slip}- {/if}{displayPrice currency=$order->id_currency price=$wrapping_tax_infos.total_amount}</td>
						</tr>
					{/foreach}
				{/if}

			</table>
			{/if}
		</td>
	</tr>
</table>
<!--  / TAX DETAILS -->
{/if}
