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
		<td style="width: 20%; padding-right: 7px; text-align: right; vertical-align: top">
			<!-- CUSTOMER INFORMATIONS -->
			<b>{l s='Order Number:' pdf='true'}</b><br />
			{'%06d'|sprintf:$order->id}<br />
			<br />
			<b>{l s='Order Date:' pdf='true'}</b><br />
			{$order->date_add|date_format:"%d-%m-%Y %H:%M"}<br />
			<br />
			<b>{l s='Payment Method:' pdf='true'}</b><br />
			{$order->payment}<br />
			<br />
			<!-- / CUSTOMER INFORMATIONS -->
		</td>
		<td style="width: 80%; text-align: right">
			<table style="width: 100%">
				<tr style="line-height:6px;">
					<td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 10px; font-weight: bold; width: 60%">{l s='ITEMS MARKED AS RETURNED' pdf='true'}</td>
					<td style="background-color: #4D4D4D; color: #FFF; text-align: left; font-weight: bold; width: 20%">{l s='REFERENCE' pdf='true'}</td>
					<td style="background-color: #4D4D4D; color: #FFF; text-align: center; font-weight: bold; width: 20%">{l s='QTY' pdf='true'}</td>
				</tr>
				{foreach $order_details as $product}
				{cycle values='#FFF,#DDD' assign=bgcolor}
				<tr style="line-height:6px;background-color:{$bgcolor};">
					<td style="text-align: left; width: 60%">{$product.product_name}</td>
					<td style="text-align: left; width: 20%">
                        {if empty($product.product_reference)}
                            ---
                        {else}
                            {$product.product_reference}
                        {/if}
                    </td>
					<td style="text-align: center; width: 20%">{$product.product_quantity}</td>
				</tr>
				{/foreach}
			</table>
		</td>
	</tr>
</table>
<!-- / PRODUCTS TAB -->

<table>
	<tr><td style="line-height: 8px">&nbsp;</td></tr>
</table>

</div>

