<?php include ('header.php'); ?>

<tr>
	<td align="center">
		<?php echo t('<span class="title">Hi {firstname} {lastname},</span><br/>
			<span class="subtitle">Thank you for your order from <strong>{shop_name}</strong></span>'); 
		?>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td class="box">
		<p>
			<?php echo t('Product(s) now available for download'); ?>
		</p>
		<span>
			<?php echo t('You have <strong>{nbProducts}</strong> product(s) now available for download using the following link(s):'); ?><br/><br/>
			<?php echo t('{virtualProducts}'); ?>
		</span>
	</td>
</tr>
<tr>
	<td>
		<?php echo t('<span>You can review your order and download your invoice from the <a href="{history_url}">"Order history"</a> section of your customer account by clicking <a href="{my_account_url}">"My account"</a> on our shop.</span>'); ?>
	</td>
</tr>
<tr>
	<td>
		<?php echo t('<span>If you have a guest account, you can follow your order via the <a href="{guest_tracking_url}?id_order={order_name}">"Guest Tracking"</a> section on our shop.</span>'); ?>
	</td>
</tr>

<?php include ('footer.php'); ?>
