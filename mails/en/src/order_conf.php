<?php include ('header.php'); ?>

<tr>
	<td align="center" colspan="3">
		<?php echo t('<span class="title">Hi {firstname} {lastname},</span><br/>
			<span class="subtitle">Thank you for shopping at {shop_name}!</span>'); 
		?>
	</td>
</tr>
<tr>
	<td colspan="3">&nbsp;</td>
</tr>
<tr>
	<td class="box" colspan="3">
		<p>
			<?php echo t('Order details'); ?>
		</p>
		<span>
			<?php echo t('<span><strong>Order:</strong></span> {order_name} placed on {date}'); ?><br /><br />
			<?php echo t('<span><strong>Payment:</strong></span> {payment}'); ?>
		</span>
	</td>
</tr>
<tr>
	<td colspan="3">
		<table class="table table-recap" bgcolor="#ffffff"><!-- Title -->
			<thead>
				<tr>
					<th>Reference</th>
					<th width="30%">Product</th>
					<th>Unit price</th>
					<th>Quantity</th>
					<th>Total price</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="5">
						{products}
					</td>
				</tr>
				<tr>
					<td colspan="5">
						{discounts}
					</td>
				</tr>
				<tr class="conf_body">
					<td bgcolor="#fbfbfb" align="right" colspan="4"><strong>Products</strong></td>
					<td bgcolor="#fbfbfb" align="right">{total_products}</td>
				</tr>
				<tr class="conf_body">
					<td bgcolor="#fbfbfb" align="right" colspan="4"><strong>Discounts</strong></td>
					<td bgcolor="#fbfbfb" align="right">{total_discounts}</td>
				</tr>
				<tr class="conf_body">
					<td bgcolor="#fbfbfb" align="right" colspan="4"><strong>Gift-wrapping</strong></td>
					<td bgcolor="#fbfbfb" align="right">{total_wrapping}</td>
				</tr>
				<tr class="conf_body">
					<td bgcolor="#fbfbfb" align="right" colspan="4"><strong>Shipping</strong></td>
					<td bgcolor="#fbfbfb" align="right">{total_shipping}</td>
				</tr>
				<tr class="conf_body">
					<td bgcolor="#fbfbfb" align="right" colspan="4"><strong>Total Tax paid</strong></td>
					<td bgcolor="#fbfbfb" align="right">{total_tax_paid}</td>
				</tr>
				<tr class="conf_body">
					<td bgcolor="#fbfbfb" align="right" colspan="4" class="total"><strong>Total paid</strong></td>
					<td bgcolor="#fbfbfb" align="right" class="total_amount">{total_paid}</td>
				</tr>
			</tbody>
		</table>
	</td>
</tr>
<tr>
	<td class="box" colspan="3">
		<p>
			<?php echo t('Shipping'); ?>
		</p>
		<span>
			<?php echo t('<span><strong>Carrier:</strong></span> {carrier}'); ?><br /><br />
			<?php echo t('<span><strong>Payment:</strong></span> {payment}'); ?>
		</span>
	</td>
</tr>
<tr>
	<td colspan="3">&nbsp;</td>
</tr>
<tr>
	<td class="box" width="310">
		<p>
			<?php echo t('Delivery address'); ?>
		</p>
		<span>
			<?php echo t('{delivery_block_html}'); ?>
		</span>
	</td>
	<td width="20">&nbsp;</td>
	<td class="box" width="310">
		<p>
			<?php echo t('Billing address'); ?>
		</p>
		<span>
			<?php echo t('{invoice_block_html}'); ?>
		</span>
	</td>
</tr>
<tr>
	<td colspan="3">
		<?php echo t('<span>You can review your order and download your invoice from the <a href="{history_url}">"Order history"</a> section of your customer account by clicking <a href="{my_account_url}">"My account"</a> on our shop.</span>'); ?>
	</td>
</tr>
<tr>
	<td colspan="3">
		<?php echo t('<span>If you have a guest account, you can follow your order via the <a href="{guest_tracking_url}">"Guest Tracking"</a> section on our shop.</span>'); ?>
	</td>
</tr>

<?php include ('footer.php'); ?>