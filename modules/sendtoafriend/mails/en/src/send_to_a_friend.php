<?php include ('header.php'); ?>

<tr>
	<td align="center">
		<span class="title"><?php echo t('Hi {name},'); ?></span><br/>
		<span class="subtitle"><?php echo t('Thank you for creating a customer account at {shop_name}.'); ?></span>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td class="box">
		<p>
			{customer} <?php echo t('has sent you a link to a product that (s)he thinks may interest you.'); ?>
		</p>
		<span>
			<?php echo t('Click here to view this item:'); ?> <a href="{product_link}">{product}</a>
		</span>
	</td>
</tr>

<?php include ('footer.php'); ?>