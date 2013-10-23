<?php include ('header.php'); ?>

<tr>
	<td align="center">
		<span class="title"><?php echo t('Hi {firstname} {lastname},'); ?></span><br/>
		<span class="subtitle"><?php echo t('Thank you for creating a customer account at {shop_name}.'); ?></span>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td class="box">
		<p>
			<?php echo t('Your {shop_name} login details'); ?>
		</p>
		<span>
			<?php echo t('Here are your login details:'); ?><br /> 
			<span><strong><?php echo t('E-mail address: <a href="mailto:{email}">{email}</a>'); ?></strong></span><br />
			<span><strong><?php echo t('Password:'); ?></strong></span> {passwd}
		</span>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td class="box">
		<p><?php echo t('Important Security Tips:'); ?></p>
		<ol>
			<li><?php echo t('Always keep your account details safe.'); ?></li>
			<li><?php echo t('Never disclose your login details to anyone.'); ?></li>
			<li><?php echo t('Change your password regularly.'); ?></li>
			<li><?php echo t('Should you suspect someone is using your account illegally, please notify us immediately.'); ?></li>
		</ol>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>
		<span><?php echo t('You can now place orders on our shop: <a href="{shop_url}">{shop_name}</a>'); ?>.</span>
	</td>
</tr>

<?php include ('footer.php'); ?>