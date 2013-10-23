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
			<?php echo t('Your {shop_name} login information'); ?>
		</p>
		<span>
			<?php echo t('Here is your personal login information for <span><strong>{shop_name}</strong></span>:'); ?><br/>
			<?php echo t('<span><strong>First name:</strong></span> {firstname}'); ?><br />
			<?php echo t('<span><strong>Last name:</strong></span> {lastname}'); ?><br />
			<?php echo t('<span><strong>Password:</strong></span> {passwd}'); ?><br />
			<?php echo t('<span><strong>E-mail address:</strong></span> {email}'); ?>
		</span>
	</td>
</tr>

<?php include ('footer.php'); ?>