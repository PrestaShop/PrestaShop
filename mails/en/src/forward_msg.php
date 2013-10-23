<?php include ('header.php'); ?>

<tr>
	<td align="center">
		<span class="title"><?php echo t('Hi {firstname} {lastname},'); ?></span>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td class="box">
		<p>
			<?php echo t('Customer service - Forwarded discussion'); ?>
		</p>
		<span>
			<span><strong>{employee}</strong></span> <?php echo t('wanted to forward this discussion to you.'); ?> <br /><br />
			<span><strong><?php echo t('Discussion history:'); ?></strong></span> {messages}<br /><br />
			<span><strong>{employee}</strong></span> <?php echo t('added'); ?> <span><strong>"{comment}"</strong></span>
		</span>
	</td>
</tr>

<?php include ('footer.php'); ?>
