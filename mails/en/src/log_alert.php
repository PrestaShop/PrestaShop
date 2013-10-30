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
			<?php echo t('You have received a new log alert'); ?>
		</p>
		<span>
			<?php echo t('<span><strong>Warning:</strong></span> you have received a new log alert in your Back Office.'); ?><br /><br />
			<?php echo t('You can check for it in the <span><strong>"Tools" &gt; "Logs"</strong></span> section of your Back Office.'); ?>
		</span>
	</td>
</tr>

<?php include ('footer.php'); ?>
