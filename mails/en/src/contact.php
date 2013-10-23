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
			<?php echo t('Message from a {shop_name} customer'); ?>
		</p>
		<span>
			<span><strong><?php echo t('Customer e-mail address:'); ?> <a href="mailto:{email}">{email}</a></strong></span><br /><br />
			<span><strong><?php echo t('Customer message:'); ?></strong></span> {message}<br /><br />
			<span><strong><?php echo t('Order ID:'); ?></strong></span> {order_name}<br />
			<span><strong><?php echo t('Attached file:'); ?></strong></span> {attached_file}
		</span>
	</td>
</tr>

<?php include ('footer.php'); ?>