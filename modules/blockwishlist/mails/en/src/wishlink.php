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
			<?php echo t('Message from {shop_name}'); ?>
		</p>
		<span>
			<?php echo t('<span><strong>{shop_name}</strong></span> invites you to send this link to your friends, so they can see your wishlist:'); ?> <span><strong>{wishlist}</strong></span><br /><br />
			<a title="WishList" href="{message}">{message}</a>
		</span>
	</td>
</tr>

<?php include ('footer.php'); ?>