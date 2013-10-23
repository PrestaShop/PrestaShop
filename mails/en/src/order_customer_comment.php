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
			<?php echo t('Message from a customer'); ?>
		</p>
		<span>
			<?php echo t('You have received a new message regarding order with the reference <span><strong>{order_name}</strong></span>.'); ?><br /><br />
			<?php echo t('<span><strong>Customer:</strong></span> {firstname} {lastname} ({email})'); ?><br /><br />
			<?php echo t('{message}'); ?>
		</span>
	</td>
</tr>

<?php include ('footer.php'); ?>