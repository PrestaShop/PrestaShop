<?php include ('header.php'); ?>

<tr>
	<td align="center">
		<span class="title"><?php echo t('Hi {firstname} {lastname},'); ?></span><br/>
		<span class="subtitle"><?php echo t('Thank you for shopping with {shop_name}!'); ?></span>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td class="box">
		<p>
			<?php echo t('Order {order_name}'); ?>&nbsp;-&nbsp;<?php echo t('Shipped'); ?>
		</p>
		<span>
			<?php echo t('Your order with the reference <span><strong>{order_name}</strong></span> has been shipped.'); ?><br /> 
			<?php echo t('You will soon receive a URL to track the delivery progress of your package.'); ?>
		</span>
	</td>
</tr>
<tr>
	<td>
		<span>
			<?php echo t('You can now place orders on our shop:'); ?> <a href="{shop_url}">{shop_name}</a>
		</span>
	</td>
</tr>

<?php include ('footer.php'); ?>
