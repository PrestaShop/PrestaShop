<table>
	<tr>
		<td style="padding-left:20px">
			<p><strong>{l s='Unselected'}</strong></p>
			<select
				id="product_rule_select_{$product_rule_group_id|intval}_{$product_rule_id|intval}_1"
				style="border:1px solid #AAAAAA;width:400px;height:160px"
				multiple
			>
				{foreach from=$product_rule_itemlist.unselected item='item'}
					<option value="{$item.id|intval}">&nbsp;{$item.name}</option>
				{/foreach}
			</select><br /><br />
			<a
				id="product_rule_select_{$product_rule_group_id|intval}_{$product_rule_id|intval}_add"
				style="cursor:pointer;text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px"
			>
				{l s='Add'} &gt;&gt;
			</a>
		</td>
		<td>
			<p><strong>{l s='Selected'}</strong></p>
			<select
				name="product_rule_select_{$product_rule_group_id|intval}_{$product_rule_id|intval}[]"
				id="product_rule_select_{$product_rule_group_id|intval}_{$product_rule_id|intval}_2"
				class="product_rule_toselect"
				style="border:1px solid #AAAAAA;width:400px;height:160px"
				multiple
			>
				{foreach from=$product_rule_itemlist.selected item='item'}
					<option value="{$item.id|intval}">&nbsp;{$item.name}</option>
				{/foreach}
			</select><br /><br />
			<a
				id="product_rule_select_{$product_rule_group_id}_{$product_rule_id}_remove"
				style="cursor:pointer;text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px"
			>
				&lt;&lt; {l s='Remove'}
			</a>
		</td>
	</tr>
</table>

<script type="text/javascript">
	$('#product_rule_select_{$product_rule_group_id|intval}_{$product_rule_id|intval}_remove').click(function() { removeCartRuleOption(this); updateProductRuleShortDescription(this); });
	$('#product_rule_select_{$product_rule_group_id|intval}_{$product_rule_id|intval}_add').click(function() { addCartRuleOption(this); updateProductRuleShortDescription(this); });
	$(document).ready(function() { updateProductRuleShortDescription($('#product_rule_select_{$product_rule_group_id|intval}_{$product_rule_id|intval}_add')); });
</script>