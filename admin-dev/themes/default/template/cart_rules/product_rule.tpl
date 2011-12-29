<tr id="product_rule_{$product_rule_id}_tr">
	<td>
		<a href="javascript:removeProductRule({$product_rule_id});">
			<img src="../img/admin/disabled.gif" alt="{l s='Remove'}" title="{l s='Remove'}" />
		</a>
	</td>
	<td>
		<input type="hidden" name="product_rule[]" value="{$product_rule_id}" />
		<input type="hidden" name="product_rule_{$product_rule_id}_type" value="{$product_rule_type}" />
		[{$product_rule_type}] {l s='The cart must contain at least'}
	</td>
	<td style="padding:5px">
		<input type="text" name="product_rule_{$product_rule_id}_quantity" value="{$product_rule_quantity|intval}" style="width:30px" />
	</td>
	<td>
		{l s='product(s) matching'}
	</td>
	<td>
		<input type="text" id="product_rule_{$product_rule_id}_match" value="" disabled="disabled" />
	</td>
	<td>
		<a id="product_rule_{$product_rule_id}_choose_link" href="#product_rule_{$product_rule_id}_choose_content">
			<img src="../img/admin/choose.gif" alt="{l s='Choose'}" title="{l s='Choose'}" /> {l s='Choose'}
		</a>
		<div>
			<div id="product_rule_{$product_rule_id}_choose_content">
				{$product_rule_choose_content}
			</div>
		</div>
	</td>
</tr>

<script type="text/javascript">
	$('#product_rule_{$product_rule_id}_choose_content').parent().hide();
	$("#product_rule_{$product_rule_id}_choose_link").fancybox();
</script>