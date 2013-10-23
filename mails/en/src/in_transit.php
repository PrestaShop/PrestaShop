<?php include ('header.php'); ?>

<tr>
	<td align="center">
		<?php echo t('<span class="title">Hi {firstname} {lastname},</span>'); 
		?>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td class="box">
		<p>
			<?php echo t('Your order with the reference {order_name} - In transit'); ?>
		</p>
		<span>
			<?php echo t('Your order with the reference <span><strong>{order_name}</strong></span> is currently in transit.'); ?> <br /><br />
			<?php echo t('You can track your package using the following link: <a href="{followup}">{followup}</a>'); ?>
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
		<?php echo t('<span>If you have a guest account, you can follow your order via the <a href="{guest_tracking_url}">"Guest Tracking"</a> section on our shop.</span>'); ?>
	</td>
</tr>

<?php include ('footer.php'); ?>
