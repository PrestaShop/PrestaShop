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
			<?php echo t('Your order with the reference {order_name} - Order cancelled'); ?>
		</p>
		<span>
			<?php echo t('Your order with the reference {order_name} from {shop_name} has been cancelled.'); ?><br />
			<?php echo t('You can check for it in the <span><strong>"Tools" &gt; "Logs"</strong></span> section of your Back Office.'); ?>
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
