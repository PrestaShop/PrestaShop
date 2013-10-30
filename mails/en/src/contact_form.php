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
			<?php echo t('Your message to {shop_name} Customer Service'); ?>
		</p>
		<span>
			<?php echo t('Your message has been sent successfully.'); ?><br /><br />
			<span><strong><?php echo t('Message:'); ?></strong></span> {message}<br /><br />
			<span><strong><?php echo t('Order ID:'); ?></strong></span> {order_name}<br />
			<span><strong><?php echo t('Product:'); ?></strong></span> {product_name}<br />
			<span><strong><?php echo t('Attached file:'); ?></strong></span> {attached_file}
		</span>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>
		<span>
			<?php echo t('We will answer as soon as possible.'); ?>
		</span>
	</td>
</tr>

<?php include ('footer.php'); ?>