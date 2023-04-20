{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
