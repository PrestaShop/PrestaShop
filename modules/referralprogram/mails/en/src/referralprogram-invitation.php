<?php include ('header.php'); ?>

<tr>
	<td align="center">
		<span class="title">{firstname_friend} {lastname_friend}, <?php echo t('join us!'); ?></span>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td class="box">
		<span>
			<?php echo t('Your friend,'); ?> <span><strong>{firstname} {lastname}</strong></span> <?php echo t('wants to refer you on <a href="{shop_url}">{shop_name}</a>!'); ?><br /><br /> 
			<?php echo t('We are pleased to offer you a voucher worth'); ?> <span><strong>{discount_display} (voucher # {discount_name})</strong></span> <?php echo t('that you can use on your next order.'); ?><br /><br />
			<?php echo t('Get referred and earn a discount voucher of'); ?> <span><strong>{discount}!</strong></span>
			<a title="Register" href="{link}"><?php echo t('It&#039;s very easy to sign up. Just click here!'); ?></a>
		</span>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>
		<span><?php echo t('When signing up, don&#039;t forget to provide the e-mail address of your referring friend:'); ?> <span><strong>{email}</strong></span>.<br/><br/>
		<span><?php echo t('Best regards,'); ?>
	</td>
</tr>

<?php include ('footer.php'); ?>