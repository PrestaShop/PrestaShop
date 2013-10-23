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
			<?php echo t('Message from a {shop_name} customer'); ?>
		</p>
		<span>
			<?php echo t('<span><strong>Customer e-mail address: <a href="mailto:{email}">{email}</a></strong></span>'); ?><br /><br />
			<?php echo t('<span><strong>Customer message:</strong></span> {message}'); ?><br /><br />
			<?php echo t('<span><strong>Order ID:</strong></span> {order_name}'); ?><br />
			<?php echo t('<span><strong>Attached file:</strong></span> {attached_file}'); ?>
		</span>
	</td>
</tr>

<?php include ('footer.php'); ?>