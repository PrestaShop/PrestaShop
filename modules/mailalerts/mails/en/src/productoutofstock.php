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
			{product} <?php echo t('is nearly out of stock.'); ?>
		</p>
		<span>
			<?php echo t('The remaining stock is now less than the specified minimum of'); ?> <strong><span>{last_qty}.</span></strong><br /><br /> 
			<strong><span><?php echo t('Remaining stock:'); ?></span></strong> {qty}<br/><br/>
			<?php echo t('You are advised to open the product&#039;s admin Product Page in order to replenish your inventory.'); ?>
		</span>
	</td>
</tr>

<?php include ('footer.php'); ?>