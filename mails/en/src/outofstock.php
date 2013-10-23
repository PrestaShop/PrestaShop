<?php include ('header.php'); ?>

<tr>
	<td align="center">
		<span class="title"><?php echo t('Hi {firstname} {lastname},'); ?></span><br/>
		<span class="subtitle"><?php echo t('Thanks for your order with the reference {order_name} from {shop_name}.'); ?></span>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td class="box">
		<p>
			<?php echo t('Your order with the reference {order_name} - Item(s) out of stock'); ?>
		</p>
		<span>
			<?php echo t('Unfortunately, one or more items are currently out of stock. This may cause a slight delay in your delivery. Please accept our apologies and rest assured that we are working hard to rectify this.'); ?>
		</span>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>
		<span>
			<?php echo t('You can review your order and download your invoice from the <a href="{history_url}">"Order history"</a> section of your customer account by clicking <a href="{my_account_url}">"My account"</a> on our shop.'); ?>
		</span>
	</td>
</tr>
<tr>
	<td>
		<span>
			<?php echo t('If you have a guest account, you can follow your order via the <a href="{guest_tracking_url}">"Guest Tracking"</a> section on our shop.'); ?>
		</span>
	</td>
</tr>

<?php include ('footer.php'); ?>