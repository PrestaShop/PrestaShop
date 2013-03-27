<tr id="product_rule_{$product_rule_group_id|intval}_{$product_rule_id|intval}_tr">
	<td>
		<a href="javascript:removeProductRule({$product_rule_group_id|intval}, {$product_rule_id|intval});">
			<img src="../img/admin/disabled.gif" alt="{l s='Remove'}" title="{l s='Remove'}" />
		</a>
	</td>
	<td>
		<input type="hidden" name="product_rule_{$product_rule_group_id|intval}[]" value="{$product_rule_id}" />
		<input type="hidden" name="product_rule_{$product_rule_group_id|intval}_{$product_rule_id|intval}_type" value="{$product_rule_type|escape}" />
		{* Everything is on a single line in order to avoid a empty space between the [ ] and the word *}
		[{if $product_rule_type == 'products'}{l s='Products:'}{elseif $product_rule_type == 'categories'}{l s='Categories:'}{elseif $product_rule_type == 'manufacturers'}{l s='Manufacturers:'}{elseif $product_rule_type == 'suppliers'}{l s='Suppliers'}{elseif $product_rule_type == 'attributes'}{l s='Attributes'}{/if}]
	</td>
	<td>
		{l s='The product(s) are matching on of these'}
	</td>
	<td>
		<input type="text" id="product_rule_{$product_rule_group_id|intval}_{$product_rule_id|intval}_match" value="" disabled="disabled" />
	</td>
	<td>
		<a id="product_rule_{$product_rule_group_id|intval}_{$product_rule_id|intval}_choose_link" href="#product_rule_{$product_rule_group_id|intval}_{$product_rule_id|intval}_choose_content">
			<img src="../img/admin/choose.gif" alt="{l s='Choose'}" title="{l s='Choose'}" /> {l s='Choose'}
		</a>
		<div>
			<div id="product_rule_{$product_rule_group_id|intval}_{$product_rule_id|intval}_choose_content">
				{$product_rule_choose_content}
			</div>
		</div>
	</td>
</tr>

<script type="text/javascript">
	$('#product_rule_{$product_rule_group_id|intval}_{$product_rule_id|intval}_choose_content').parent().hide();
	$("#product_rule_{$product_rule_group_id|intval}_{$product_rule_id|intval}_choose_link").fancybox();
	$(document).ready(function() { updateProductRuleShortDescription($('#product_rule_select_{$product_rule_group_id|intval}_{$product_rule_id|intval}_add')); });
</script>