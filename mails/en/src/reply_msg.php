<?php include ('header.php'); ?>

<tr>
	<td align="center">
		<?php echo t('<span class="title">Hi {firstname} {lastname},</span>'); 
		?>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>
		<?php echo t('<span>{reply}</span>'); ?>
	</td>
</tr>
<tr>
	<td>
		<?php echo t('<span><strong>Please do not reply to this message</strong>, we will not receive it.</span>'); ?>
	</td>
</tr>
<tr>
	<td>
		<?php echo t('<span>In order to reply, please use the following link: <a href="{link}">{link}</a>.</span>'); ?>
	</td>
</tr>

<?php include ('footer.php'); ?>
