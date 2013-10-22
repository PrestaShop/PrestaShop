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
			<?php echo t('Your message to {shop_name} Customer Service'); ?>
		</p>
		<span>
			<?php echo t('Your message has been sent successfully.'); ?><br /><br />
			<?php echo t('<span><strong>Message:</strong></span> {message}'); ?><br /><br />
			<?php echo t('<span><strong>Order ID:</strong></span> {order_name}'); ?><br />
			<?php echo t('<span><strong>Product:</strong></span> {product_name}'); ?><br />
			<?php echo t('<span><strong>Attached file:</strong></span> {attached_file}'); ?>
		</span>
	</td>
</tr>
<tr>
	<td>
		<?php echo t('<span>We will answer as soon as possible.</span>'); ?>
	</td>
</tr>

<?php include ('footer.php'); ?>