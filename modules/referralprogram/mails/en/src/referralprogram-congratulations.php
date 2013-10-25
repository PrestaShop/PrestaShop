<?php include ('header.php'); ?>

<tr>
	<td align="center">
		<span class="title"><?php echo t('Congratulations!'); ?></span>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td class="box">
		<span>
			<?php echo t('Your referred friend,'); ?> <span><strong>{sponsored_firstname} {sponsored_lastname}</strong></span> <?php echo t('has placed his or her first order on <a href="{shop_url}">{shop_name}</a>!'); ?><br /><br /> 
			<?php echo t('We are pleased to offer you a voucher worth'); ?> <span><strong>{discount_display} (voucher # {discount_name})</strong></span> <?php echo t('that you can use on your next order.'); ?>
		</span>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>
		<span><?php echo t('Best regards,'); ?></span>
	</td>
</tr>

<?php include ('footer.php'); ?>