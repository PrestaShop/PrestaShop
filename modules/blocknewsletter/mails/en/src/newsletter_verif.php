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
		<span>
			<?php echo t('Thank you for subscribing to our newsletter, please confirm your request by clicking the link below :'); ?><br/>
			<span><strong><a href="{verif_url}">{verif_url}</a></strong></span>
		</span>
	</td>
</tr>

<?php include ('footer.php'); ?>