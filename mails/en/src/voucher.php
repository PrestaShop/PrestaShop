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
			<?php echo t('Your order with the reference {order_name} - Voucher created'); ?>
		</p>
		<span>
			<?php echo t('A voucher has been created in your name as a result of your order with the reference <span><strong>{order_name}</strong></span>.'); ?><br/><br/>
			<?php echo t('<span><strong>Voucher code: {voucher_num}</strong></span> in the amount of <span><strong>{voucher_amount}</strong></span></strong>'); ?><br /><br />
			<?php echo t('Simply copy/paste this code during the payment process for your next order.'); ?>
		</span>
	</td>
</tr>

<?php include ('footer.php'); ?>