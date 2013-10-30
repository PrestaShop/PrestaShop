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
			<?php echo t('Message from {shop_name}'); ?>
		</p>
		<span>
			<?php echo t('You have received a new message from <span><strong>{shop_name}</strong></span> regarding order with the reference <span><strong>{order_name}</strong></span>.'); ?><br /><br />
			<span><strong><?php echo t('Message:'); ?></strong></span> {message}
		</span>
	</td>
</tr>

<?php include ('footer.php'); ?>

