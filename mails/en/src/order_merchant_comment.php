<?php include ('header.php'); ?>

<tr>
	<td align="center" colspan="3">
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
			<?php echo t('New message from {shop_name}'); ?>
		</p>
		<span>
			<?php echo t('You have received a new message from {shop_name} regarding order with the reference {order_name}.'); ?><br /><br />
			<?php echo t('<span><strong>Message:</strong></span> {message}'); ?>
		</span>
	</td>
</tr>

<?php include ('footer.php'); ?>

