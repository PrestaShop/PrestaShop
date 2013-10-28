<?php include ('header.php'); ?>

<tr>
	<td align="center">
		<span class="title"><?php echo t('Hi,'); ?></span>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td class="box">
		<p>
			<?php echo t('{product} is almost out of stock.'); ?>
		</p>
		<span>
			<?php echo t('The stock cover is now less than the specified minimum of:'); ?> <strong><span>{warning_coverage}.</span></strong><br /><br /> 
			<strong><span><?php echo t('Current stock cover:'); ?></span></strong> {current_coverage}
		</span>
	</td>
</tr>

<?php include ('footer.php'); ?>