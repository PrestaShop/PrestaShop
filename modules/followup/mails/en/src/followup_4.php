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
			<?php echo t('Your cart at {shop_name}'); ?>
		</p>
		<span>
			<?php echo t('You are one of our best customers, however you have not placed an order in {days_threshold} days.'); ?><br /> <br /> 
			<?php echo t('Your cart has been saved, you can resume your order by visiting our shop:'); ?> <span><strong><a title="{shop_name}" href="{shop_url}">{shop_url}</a></strong></span><br /><br />
			<?php echo t('We wish to thank you for the trust you have placed in us and want to give you a discount of {amount}% valid on your next order! This offer is valid for <span><strong>{days}</strong></span> days, so do not waste a moment!'); ?>
		</span>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td class="box">
		<span>
			<span><strong><?php echo t('Here is your coupon:'); ?></strong></span> {voucher_num}<br /><br />
			<?php echo t('Enter this code in your shopping cart to get your discount.'); ?>
		</span>
	</td>
</tr>

<?php include ('footer.php'); ?>