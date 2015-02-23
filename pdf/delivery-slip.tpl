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
<div style="font-size: 9pt; color: #444">

<table>
	<tr><td>&nbsp;</td></tr>
</table>

<!-- ADDRESSES -->
<table style="width: 100%">
	<tr>
		<td style="width: 20%"></td>
		<td style="width: 80%">
			{if !empty($invoice_address)}
				<table style="width: 100%">
					<tr>
						<td style="width: 50%">
							<span style="font-weight: bold; font-size: 11pt; color: #9E9F9E">{l s='Delivery Address' pdf='true'}</span><br />
							 {$delivery_address}
						</td>
						<td style="width: 50%">
							<span style="font-weight: bold; font-size: 11pt; color: #9E9F9E">{l s='Billing Address' pdf='true'}</span><br />
							 {$invoice_address}
						</td>
					</tr>
				</table>
			{else}
				<table style="width: 100%">
					<tr>
						<td style="width: 50%">
							<span style="font-weight: bold; font-size: 11pt; color: #9E9F9E">{l s='Billing & Delivery Address' pdf='true'}</span><br />
							 {$delivery_address}
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

<table>
	<tr><td style="line-height: 8px">&nbsp;</td></tr>
</table>

<!-- PRODUCTS TAB -->
<table style="width: 100%">
	<tr>
		<td style="width: 22%; padding-right: 7px; text-align: right; vertical-align: top">
			<!-- CUSTOMER INFORMATIONS -->
			<b>{l s='Order Number:' pdf='true'}</b><br />
			{$order->getUniqReference()}<br />
			<br />
			<b>{l s='Order Date:' pdf='true'}</b><br />
			{dateFormat date=$order->date_add full=0}<br />
			<br />
			<b>{l s='Payment Method:' pdf='true'}</b><br />
			<table style="width: 100%;">
			{foreach from=$order_invoice->getOrderPaymentCollection() item=payment}
				<tr>
					<td style="width: 50%">{$payment->payment_method}</td>
					<td style="width: 50%">{displayPrice price=$payment->amount currency=$order->id_currency}</td>
				</tr>
			{foreachelse}
				<tr>
					<td>{l s='No payment'}</td>
				</tr>
			{/foreach}
			</table>
			<br />
			{if isset($carrier)}
			<b>{l s='Carrier:' pdf='true'}</b><br />
			{$carrier->name}<br />
			<br />
			{/if}			
			<!-- / CUSTOMER INFORMATIONS -->
		</td>
		<td style="width: 78%; text-align: right">
			<table style="width: 100%">
				<tr style="line-height:6px;">
					{if Configuration::get('PS_PDF_IMG_DELIVERY')}
						<td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 10px; font-weight: bold; width: 10%">{l s='IMAGE' pdf='true'}</td>
					{/if}
					<td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 10px; font-weight: bold; width: 60%">{l s='ITEMS TO BE DELIVERED' pdf='true'}</td>
					<td style="background-color: #4D4D4D; color: #FFF; text-align: left; font-weight: bold; width: 20%">{l s='REFERENCE' pdf='true'}</td>
					<td style="background-color: #4D4D4D; color: #FFF; text-align: center; font-weight: bold; width: 20%">{l s='QTY' pdf='true'}</td>
				</tr>
				{foreach $order_details as $order_detail}
				{cycle values='#FFF,#DDD' assign=bgcolor}
				<tr style="line-height:6px;background-color:{$bgcolor};" {if Configuration::get('PS_PDF_IMG_DELIVERY') && isset($order_detail.image) && $order_detail.image->id && isset($order_detail.image_size)}height="{$order_detail['image_size'][1]}"{/if}>
					{if Configuration::get('PS_PDF_IMG_DELIVERY')}
						<td style="text-align: left;">{if isset($order_detail.image) && $order_detail.image->id}{$order_detail.image_tag}{/if}</td>
					{/if}
					<td style="text-align: left; width: 60%">{$order_detail.product_name}</td>
					<td style="text-align: left; width: 20%">
						{if empty($order_detail.product_reference)}
							---
						{else}
							{$order_detail.product_reference}
						{/if}
					</td>
					<td style="text-align: center; width: 20%">{$order_detail.product_quantity}</td>
				</tr>
					{foreach $order_detail.customizedDatas as $customizationPerAddress}
						{foreach $customizationPerAddress as $customizationId => $customization}
							<tr style="line-height:6px;background-color:{$bgcolor};">
								<td style="line-height:3px; text-align: left; width: 60%; vertical-align: top">
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
								</td>
								<td style="text-align: right; width: 20%"></td>
								<td style="text-align: center; width: 20%; vertical-align: top">({$customization.quantity})</td>
							</tr>
						{/foreach}
					{/foreach}
				{/foreach}
			</table>
		</td>
	</tr>
</table>
<!-- / PRODUCTS TAB -->

<table>
	<tr><td style="line-height: 8px">&nbsp;</td></tr>
</table>

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

