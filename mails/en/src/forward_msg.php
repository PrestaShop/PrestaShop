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
			<?php echo t('Customer service - Forwarded discussion'); ?>
		</p>
		<span>
			<?php echo t('<span><strong>{employee}</strong></span> wanted to forward this discussion to you.'); ?> <br /><br />
			<?php echo t('<span><strong>Discussion history:</strong></span> {messages}'); ?><br /><br />
			<?php echo t('<span><strong>{employee}</strong></span> added <span><strong>"{comment}"</strong></span>'); ?>
		</span>
	</td>
</tr>

<?php include ('footer.php'); ?>
