<tr id="product_rule_group_{$product_rule_group_id|intval}_tr">
	<td>
		<a class="btn btn-default" href="javascript:removeProductRuleGroup({$product_rule_group_id|intval});">
			<i class="icon-remove text-danger"></i>
		</a>
	</td>
	<td>


		<div class="form-group">
			<label class="control-label col-lg-3">{l s='The cart must contain at least'}</label>
			<div class="col-lg-9">
				<input class="form-control" type="hidden" name="product_rule_group[]" value="{$product_rule_group_id|intval}" />

				{l s='Product(s) matching the following rules:'}


				<input class="form-control fixed-width-xs" type="text" name="product_rule_group_{$product_rule_group_id|intval}_quantity" value="{$product_rule_group_quantity|intval}" />
			</div>
		</div>

		

		<div class="form-group">

			<label class="control-label col-lg-3">{l s='Add a rule concerning'}</label>
			<div class="col-lg-9">
				<select class="form-control fixed-width-lg" id="product_rule_type_{$product_rule_group_id|intval}">
					<option value="">{l s='-- Choose --'}</option>
					<option value="products">{l s='Products:'}</option>
					<option value="attributes">{l s='Attributes'}</option>
					<option value="categories">{l s='Categories:'}</option>
					<option value="manufacturers">{l s='Manufacturers:'}</option>
					<option value="suppliers">{l s='Suppliers'}</option>
				</select>
				<a class="btn btn-default" href="javascript:addProductRule({$product_rule_group_id|intval});">
					<i class="icon-ok"></i>
					{l s="Add"}
				</a>
			</div>

		</div>


		

		


		<table id="product_rule_table_{$product_rule_group_id|intval}" class="table">
			{if isset($product_rules) && $product_rules|@count}
				{foreach from=$product_rules item='product_rule'}
					{$product_rule}
				{/foreach}
			{/if}
		</table>

	</td>
</tr>