<?php include ('header.php'); ?>

<tr>
	<td align="center">
		<?php echo t('<span class="title">Hi {firstname} {lastname},</span>'); ?>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td class="box">
		<p>
			<?php echo t('A new command has been generated to you.'); ?>
		</p>
		<span>
			<?php echo t('Go on <a href="{order_link}">{order_link}</a> to finalize the payment.'); ?>
		</span>
	</td>
</tr>


<?php include ('footer.php'); ?>