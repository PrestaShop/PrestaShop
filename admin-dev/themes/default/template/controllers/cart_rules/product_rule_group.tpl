<tr id="product_rule_group_{$product_rule_group_id|intval}_tr">
	<td style="vertical-align:center;padding-right:10px">
		<a href="javascript:removeProductRuleGroup({$product_rule_group_id|intval});">
			<img src="../img/admin/disabled.gif" alt="{l s='Remove'}" title="{l s='Remove'}" />
		</a>
	</td>
	<td style="padding-bottom:15px">
		<input type="hidden" name="product_rule_group[]" value="{$product_rule_group_id|intval}" />
		{l s='The cart must contain at least'}
		<input type="text" name="product_rule_group_{$product_rule_group_id|intval}_quantity" value="{$product_rule_group_quantity|intval}" style="width:30px" />
		{l s='Product(s) matching the following rules:'}
		<br />
		<a href="javascript:addProductRule({$product_rule_group_id|intval});">
			<img src="../img/admin/add.gif" alt="{l s='Add'}" title="{l s='Add'}" />
			{l s='Add a rule concerning'}
		</a>
		<select id="product_rule_type_{$product_rule_group_id|intval}">
			<option value="">{l s='-- Choose --'}</option>
			<option value="products">{l s='Products:'}</option>
			<option value="attributes">{l s='Attributes'}</option>
			<option value="categories">{l s='Categories:'}</option>
			<option value="manufacturers">{l s='Manufacturers:'}</option>
			<option value="suppliers">{l s='Suppliers'}</option>
		</select>
		<a href="javascript:addProductRule({$product_rule_group_id|intval});">
			<input type="button" class="button" value="OK" />
		</a>
		<table id="product_rule_table_{$product_rule_group_id|intval}" class="table" cellpadding="0" cellspacing="0">
			{if isset($product_rules) && $product_rules|@count}
				{foreach from=$product_rules item='product_rule'}
					{$product_rule}
				{/foreach}
			{/if}
		</table>
	</td>
</tr>