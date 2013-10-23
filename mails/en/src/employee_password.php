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
			<?php echo t('Your {shop_name} login information'); ?>
		</p>
		<span>
			<?php echo t('Here is your personal login information for <span><strong>{shop_name}</strong></span>:'); ?><br/><br/>
			<span><strong><?php echo t('First name:'); ?></strong></span> {firstname}<br />
			<span><strong><?php echo t('Last name:'); ?></strong></span> {lastname}<br />
			<span><strong><?php echo t('Password:'); ?></strong></span> {passwd}<br />
			<span><strong><?php echo t('E-mail address:'); ?></strong></span> {email}
		</span>
	</td>
</tr>

<?php include ('footer.php'); ?>