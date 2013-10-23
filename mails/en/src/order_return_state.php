<?php include ('header.php'); ?>

<tr>
	<td align="center" colspan="3">
		<span class="title"><?php echo t('Hi {firstname} {lastname},'); ?></span>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td class="box">
		<p>
			<?php echo t('Return #{id_order_return} - update'); ?>
		</p>
		<span>
			<?php echo t('We have updated the progress on your return #{id_order_return}, the new status is:'); ?> <span><strong>"{state_order_return}"</strong></span><br /><br />
			<span><strong><?php echo t('Message:'); ?></strong></span> {message}
		</span>
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

<?php include ('footer.php'); ?>
