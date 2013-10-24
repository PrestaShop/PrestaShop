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
			<?php echo t('This is to inform you about the creation of a voucher.'); ?>
		</p>
		<span>
			<?php echo t('Here is the code of your voucher:'); ?> <span><strong>{voucher_num}</strong></span><br/><br/>
			<?php echo t('Simply copy/paste this code during the payment process for your next order.'); ?>
	</td>
</tr>

<?php include ('footer.php'); ?>