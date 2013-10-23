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
			<?php echo t('Password reset request for {shop_name}'); ?>
		</p>
		<span>
			<?php echo t('You have requested to reset your <span><strong>{shop_name}</strong></span> login details.'); ?><br/><br/>
			<?php echo t('Please note that this will change your current password.'); ?><br/><br/>
			<?php echo t('To confirm this action, please use the following link:<br /> <a href="{url}">{url}</a>'); ?>
		</span>
	</td>
</tr>

<?php include ('footer.php'); ?>
