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
			<?php echo t('A new order has been generated on your behalf.'); ?>
		</p>
		<span>
			<?php echo t('Please go on <a href="{order_link}">{order_link}</a> to finalize the payment.'); ?>
		</span>
	</td>
</tr>


<?php include ('footer.php'); ?>