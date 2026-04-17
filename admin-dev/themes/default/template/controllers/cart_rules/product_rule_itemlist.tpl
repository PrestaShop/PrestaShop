{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
<div class="col-lg-12 bootstrap">
	<div class="col-lg-6">
		{l s='Unselected' d='Admin.Global'}
		<select multiple size="20" id="product_rule_select_{$product_rule_group_id|intval}_{$product_rule_id|intval}_1">
			{foreach from=$product_rule_itemlist.unselected item='item'}
				<option value="{$item.id|intval}" title="{$item.name|escape:'html':'UTF-8'}">&nbsp;{$item.name|escape:'html':'UTF-8'}</option>
			{/foreach}
		</select>
		<div class="clearfix">&nbsp;</div>
		<a id="product_rule_select_{$product_rule_group_id|intval}_{$product_rule_id|intval}_add" class="btn btn-default btn-block" >
			{l s='Add' d='Admin.Actions'}
			<i class="icon-arrow-right"></i>
		</a>
	</div>
	<div class="col-lg-6">
		{l s='Selected' d='Admin.Global'}
		<select multiple size="20" name="product_rule_select_{$product_rule_group_id|intval}_{$product_rule_id|intval}[]" id="product_rule_select_{$product_rule_group_id|intval}_{$product_rule_id|intval}_2" class="product_rule_toselect" >
			{foreach from=$product_rule_itemlist.selected item='item'}
				<option value="{$item.id|intval}" title="{$item.name|escape:'html':'UTF-8'}">&nbsp;{$item.name|escape:'html':'UTF-8'}</option>
			{/foreach}
		</select>
		<div class="clearfix">&nbsp;</div>
		<a id="product_rule_select_{$product_rule_group_id}_{$product_rule_id}_remove" class="btn btn-default btn-block" >
			<i class="icon-arrow-left"></i>
			{l s='Remove' d='Admin.Actions'}
		</a>
	</div>
</div>

<script type="text/javascript">
	$('#product_rule_select_{$product_rule_group_id|intval}_{$product_rule_id|intval}_remove').on('click', function() { removeCartRuleOption(this); updateProductRuleShortDescription(this); });
	$('#product_rule_select_{$product_rule_group_id|intval}_{$product_rule_id|intval}_add').on('click', function() { addCartRuleOption(this); updateProductRuleShortDescription(this); });
	$(function() { updateProductRuleShortDescription($('#product_rule_select_{$product_rule_group_id|intval}_{$product_rule_id|intval}_add')); });
</script>
