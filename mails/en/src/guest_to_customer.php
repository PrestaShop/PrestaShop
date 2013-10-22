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
			<?php echo t('Your customer account creation'); ?>
		</p>
		<span>
			<?php echo t('Your guest account for {shop_name} has been transformed into a customer account.'); ?> <br /><br />
			<?php echo t('<span><strong>E-mail address:</strong></span> {email}'); ?><br /><br />
			<?php echo t('<span><strong>Password:</strong></span> {passwd}'); ?>
		</span>
	</td>
</tr>
<tr>
	<td>
		<?php echo t('<span>Please be careful when sharing these login details with others.</span>'); ?>
	</td>
</tr>
<tr>
	<td>
		<?php echo t('<span>You can access your customer account on our shop: {shop_url}</span>'); ?>
	</td>
</tr>

<?php include ('footer.php'); ?>
