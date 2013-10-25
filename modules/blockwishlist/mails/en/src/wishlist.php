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
			<?php echo t('<span><strong>{firstname} {lastname}</strong></span> indicated you may want to see his/her wishlist:'); ?> <span><strong>{wishlist}</strong></span><br /><br />
			<a title="WishList" href="{message}">{wishlist}</a>
		</span>
	</td>
</tr>

<?php include ('footer.php'); ?>