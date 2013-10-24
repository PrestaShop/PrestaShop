<?php include ('header.php'); ?>

<tr>
	<td align="center">
		<span class="title"><?php echo t('Hi {firstname} {lastname},'); ?></span><br/>
		<span class="subtitle"><?php echo t('Thanks for your trust.'); ?></span>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td class="box">
		<p>
			<?php echo t('You are one of our best customers and as such we want to thank you for your continued patronage.'); ?>
		</p>
		<span>
			<?php echo t('As appreciation for your loyalty, we want to give you a discount of <span><strong>{amount}</strong></span>% valid on your next order! This offer is valid for <span><strong>{days}</strong></span> days, so do not waste a moment!'); ?><br /> <br /> 
			<span><strong><?php echo t('Here is your coupon:'); ?></strong></span> {voucher_num}<br /><br />
			<?php echo t('Enter this code in your shopping cart to get your discount.'); ?>
		</span>
	</td>
</tr>

<?php include ('footer.php'); ?>
