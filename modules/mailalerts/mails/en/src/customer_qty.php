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
			<?php echo t('{product} is now available.'); ?>
		</p>
		<span>
			<?php echo t('This item is once again in-stock.'); ?><br /><br /> 
			<?php echo t('You can access the product page by clicking on the link:'); ?> <span><strong><a href="{product_link}">{product}</a></strong></span><br />
			<?php echo t('You can order it right now from our online shop.'); ?>
		</span>
	</td>
</tr>

<?php include ('footer.php'); ?>