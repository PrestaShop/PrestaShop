<?php include ('header.php'); ?>

<tr>
	<td align="center">
		<?php echo t('<span class="title">Hi {firstname} {lastname},</span><br/>
			<span class="subtitle">Thank you for shopping at {shop_name}!</span>'); 
		?>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td class="box">
		<p>
			<?php echo t('Your order with the reference {order_name} - Awaiting check payment'); ?>
		</p>
		<span>
			<?php echo t('Your order with the reference <span><strong>{order_name}</strong></span> has been placed successfully and will be <strong>shipped as soon as we receive your payment</strong>.'); ?>
		</span>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td class="box">
		<p>
			<?php echo t('You have selected to pay by check. Please include on your check.'); ?>
		</p>
		<span>
			<?php echo t('Here are the bank details for your wire or transfer:'); ?><br /> 
			<?php echo t('<span><strong>Amount:</strong></span> {total_paid}'); ?><br />
			<?php echo t('<span><strong>Payable to the order of:</strong></span> {cheque_name}'); ?><br />
			<?php echo t('<span><strong>Please mail your check to:</strong></span> {cheque_address_html}'); ?>

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