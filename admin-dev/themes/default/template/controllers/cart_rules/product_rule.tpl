{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
 <tr id="product_rule_{$product_rule_group_id|intval}_{$product_rule_id|intval}_tr">
	<td>
		<input type="hidden" name="product_rule_{$product_rule_group_id|intval}[]" value="{$product_rule_id}" />
		<input type="hidden" name="product_rule_{$product_rule_group_id|intval}_{$product_rule_id|intval}_type" value="{$product_rule_type|escape}" />
		{* Everything is on a single line in order to avoid a empty space between the [ ] and the word *}
		[{if $product_rule_type == 'products'}{l s='Products' d='Admin.Global'}{elseif $product_rule_type == 'categories'}{l s='Categories' d='Admin.Global'}{elseif $product_rule_type == 'manufacturers'}{l s='Brands' d='Admin.Global'}{elseif $product_rule_type == 'suppliers'}{l s='Suppliers' d='Admin.Global'}{elseif $product_rule_type == 'attributes'}{l s='Attributes' d='Admin.Global'}{/if}]
	</td>
	<td>
		<input type="text" id="product_rule_{$product_rule_group_id|intval}_{$product_rule_id|intval}_match" value="" disabled="disabled" />
	</td>
	<td>
		<a class="btn btn-default" id="product_rule_{$product_rule_group_id|intval}_{$product_rule_id|intval}_choose_link" href="#product_rule_{$product_rule_group_id|intval}_{$product_rule_id|intval}_choose_content">
			<i class="icon-list-ul"></i>
			{l s='Choose' d='Admin.Actions'}
		</a>
		<div>
			<div id="product_rule_{$product_rule_group_id|intval}_{$product_rule_id|intval}_choose_content">
				{$product_rule_choose_content}
			</div>
		</div>
	</td>
	<td class="text-right">
		<a class="btn btn-default" href="javascript:removeProductRule({$product_rule_group_id|intval}, {$product_rule_id|intval});">
			<i class="icon-remove"></i>
		</a>
	</td>
</tr>

<script type="text/javascript">
	$('#product_rule_{$product_rule_group_id|intval}_{$product_rule_id|intval}_choose_content').parent().hide();
  $("#product_rule_{$product_rule_group_id|intval}_{$product_rule_id|intval}_choose_link").fancybox({
    autoDimensions: false,
    autoSize: false,
    width: 600,
    autoHeight: true,
  });
	$(document).ready(function() { updateProductRuleShortDescription($('#product_rule_select_{$product_rule_group_id|intval}_{$product_rule_id|intval}_add')); });
</script>
