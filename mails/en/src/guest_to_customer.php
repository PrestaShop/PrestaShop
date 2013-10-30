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
			<?php echo t('Your customer account creation'); ?>
		</p>
		<span>
			<?php echo t('Your guest account for <span><strong>{shop_name}</strong></span> has been transformed into a customer account.'); ?> <br /><br />
			<span><strong><?php echo t('E-mail address:'); ?></strong></span> {email}<br /><br />
			<span><strong><?php echo t('Password:'); ?></strong></span> {passwd}
		</span>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>
		<span>
			<?php echo t('Please be careful when sharing these login details with others.'); ?>
		</span>
	</td>
</tr>
<tr>
	<td>
		<span>
			<?php echo t('You can access your customer account on our shop:'); ?> <strong>{shop_url}</strong>
		</span>
	</td>
</tr>

<?php include ('footer.php'); ?>
