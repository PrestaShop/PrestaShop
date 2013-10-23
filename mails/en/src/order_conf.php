<?php include ('header.php'); ?>

<tr>
	<td align="center">
		<span class="title"><?php echo t('Hi {firstname} {lastname},'); ?></span><br/>
		<span class="subtitle"><?php echo t('Thank you for shopping at {shop_name}!'); ?></span>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td class="box" colspan="3">
		<p>
			<?php echo t('Order details'); ?>
		</p>
		<span>
			<span><strong><?php echo t('Order:'); ?></strong></span> {order_name} <?php echo t('placed on'); ?> {date}<br /><br />
			<span><strong><?php echo t('Payment:'); ?></strong></span> {payment}
		</span>
	</td>
</tr>
<tr>
	<td>
		<table class="table table-recap" bgcolor="#ffffff"><!-- Title -->
			<thead>
				<tr>
					<th><?php echo t('Reference'); ?></th>
					<th width="30%"><?php echo t('Product'); ?></th>
					<th><?php echo t('Unit price'); ?></th>
					<th><?php echo t('Quantity'); ?></th>
					<th><?php echo t('Total price'); ?></th>
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
					<td bgcolor="#fbfbfb" align="right" colspan="4"><strong><?php echo t('Products'); ?></strong></td>
					<td bgcolor="#fbfbfb" align="right">{total_products}</td>
				</tr>
				<tr class="conf_body">
					<td bgcolor="#fbfbfb" align="right" colspan="4"><strong><?php echo t('Discounts'); ?></strong></td>
					<td bgcolor="#fbfbfb" align="right">{total_discounts}</td>
				</tr>
				<tr class="conf_body">
					<td bgcolor="#fbfbfb" align="right" colspan="4"><strong><?php echo t('Gift-wrapping'); ?></strong></td>
					<td bgcolor="#fbfbfb" align="right">{total_wrapping}</td>
				</tr>
				<tr class="conf_body">
					<td bgcolor="#fbfbfb" align="right" colspan="4"><strong><?php echo t('Shipping'); ?></strong></td>
					<td bgcolor="#fbfbfb" align="right">{total_shipping}</td>
				</tr>
				<tr class="conf_body">
					<td bgcolor="#fbfbfb" align="right" colspan="4"><strong><?php echo t('Total Tax paid'); ?></strong></td>
					<td bgcolor="#fbfbfb" align="right">{total_tax_paid}</td>
				</tr>
				<tr class="conf_body">
					<td bgcolor="#fbfbfb" align="right" colspan="4" class="total"><strong><?php echo t('Total paid'); ?></strong></td>
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
			<span><strong><?php echo t('Carrier:'); ?></strong></span> {carrier}<br /><br />
			<span><strong><?php echo t('Payment:'); ?></strong></span> {payment}
		</span>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>
		<table class="table">
			<tr>
				<td class="box" width="310">
					<p>
						<?php echo t('Delivery address'); ?>
					</p>
					<span>
						{delivery_block_html}
					</span>
				</td>
				<td width="20">&nbsp;</td>
				<td class="box" width="310">
					<p>
						<?php echo t('Billing address'); ?>
					</p>
					<span>
						{invoice_block_html}
					</span>
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>
		<span>
			<?php echo t('You can review your order and download your invoice from the <a href="{history_url}">"Order history"</a> section of your customer account by clicking <a href="{my_account_url}">"My account"</a> on our shop.'); ?>
		</span>
	</td>
</tr>
<tr>
	<td>
		<span>
			<?php echo t('If you have a guest account, you can follow your order via the <a href="{guest_tracking_url}">"Guest Tracking"</a> section on our shop.'); ?>
		</span>
	</td>
</tr>

<?php include ('footer.php'); ?>