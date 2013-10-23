<?php include ('header.php'); ?>

<tr>
	<td align="center">
		<span class="title"><?php echo t('Hi {firstname} {lastname},'); ?></span>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td class="box">
		<p>
			<?php echo t('Your order with the reference {order_name} - Credit slip created'); ?>
		</p>
		<span>
			<?php echo t('We have generated a credit slip in your name for order with the reference <span><strong>{order_name}</strong></span>.'); ?>
		</span>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>
		<span>
			<?php echo t('You can review this credit slip and download your invoice from the <a href="{history_url}">"My credit slips"</a> section of your account by clicking <a href="{my_account_url}">"My account"</a> on our shop.'); ?>
		</span>
	</td>
</tr>

<?php include ('footer.php'); ?>