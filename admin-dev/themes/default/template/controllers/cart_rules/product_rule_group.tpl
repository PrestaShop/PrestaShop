{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
<tr id="product_rule_group_{$product_rule_group_id|intval}_tr">
	<td>
		<a class="btn btn-default" href="javascript:removeProductRuleGroup({$product_rule_group_id|intval});">
			<i class="icon-remove text-danger"></i>
		</a>
	</td>
	<td>

		<div class="form-group">
			<label class="control-label col-lg-4">{l s='Number of products required in the cart to enjoy the discount:' d='Admin.Catalog.Feature'}</label>
			<div class="col-lg-1 pull-left">
				<input type="hidden" name="product_rule_group[]" value="{$product_rule_group_id|intval}" />
				<input class="form-control" type="text" name="product_rule_group_{$product_rule_group_id|intval}_quantity" value="{$product_rule_group_quantity|intval}" />
			</div>
		</div>



		<div class="form-group">

			<label class="control-label col-lg-4">{l s='Add a rule concerning'}</label>
			<div class="col-lg-4">
				<select class="form-control" id="product_rule_type_{$product_rule_group_id|intval}">
					<option value="">{l s='-- Choose --' d='Admin.Actions'}</option>
					<option value="products">{l s='Products' d='Admin.Global'}</option>
					<option value="attributes">{l s='Attributes' d='Admin.Global'}</option>
					<option value="categories">{l s='Categories' d='Admin.Global'}</option>
					<option value="manufacturers">{l s='Brands' d='Admin.Global'}</option>
					<option value="suppliers">{l s='Suppliers' d='Admin.Global'}</option>
				</select>
			</div>
			<div class="col-lg-4">
				<a class="btn btn-default" href="javascript:addProductRule({$product_rule_group_id|intval});">
					<i class="icon-plus-sign"></i>
					{l s="Add" d='Admin.Actions'}
				</a>
			</div>

		</div>

		{l s='The product(s) are matching one of these:'}
		<table id="product_rule_table_{$product_rule_group_id|intval}" class="table table-bordered">
			{if isset($product_rules) && $product_rules|@count}
				{foreach from=$product_rules item='product_rule'}
					{$product_rule}
				{/foreach}
			{/if}
		</table>

	</td>
</tr>
