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
			<?php echo t('Referral Program'); ?>
		</p>
		<span>
			<?php echo t('We have created a voucher in your name for referring a friend.'); ?><br /> 
			<?php echo t('Here is the code of your voucher:'); ?> <span><strong>{voucher_num}</strong></span><?php echo t(', with an amount of'); ?> <span><strong>{voucher_amount}</strong></span>.<br /><br />
			<?php echo t('Simply copy/paste this code during the payment process for your next order.'); ?>
		</span>
	</td>
</tr>

<?php include ('footer.php'); ?>