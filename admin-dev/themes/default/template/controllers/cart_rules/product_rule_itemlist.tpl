{*
* 2007-2011 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 8971 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<table>
	<tr>
		<td>
			<p><strong>{l s='Selected'}</strong></p>
			<select name="product_rule_select_{$product_rule_id}[]" class="product_rule_toselect" id="product_rule_select_{$product_rule_id}_2" style="border:1px solid #AAAAAA;width:400px;height:160px" multiple>
				{foreach from=$product_rule_itemlist.selected item='item'}
					<option value="{$item.id|intval}">&nbsp;{$item.name}</option>
				{/foreach}
			</select><br /><br />
			<a style="cursor:pointer;text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px" id="product_rule_select_{$product_rule_id}_remove">
				{l s='Remove'} &gt;&gt;
			</a>
		</td>
		<td style="padding-left:20px;">
			<p><strong>{l s='Unselected'}</strong></p>
			<select id="product_rule_select_{$product_rule_id}_1" style="border:1px solid #AAAAAA;width:400px;height:160px" multiple>
				{foreach from=$product_rule_itemlist.unselected item='item'}
					<option value="{$item.id|intval}">&nbsp;{$item.name}</option>
				{/foreach}
			</select><br /><br />
			<a style="cursor:pointer;text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px" id="product_rule_select_{$product_rule_id}_add">
				&lt;&lt; {l s='Add'}
			</a>
		</td>
	</tr>
</table>

<script type="text/javascript">
	$('#product_rule_select_{$product_rule_id}_remove').click(function() { removeCartRuleOption(this); updateProductRuleShortDescription(this); });
	$('#product_rule_select_{$product_rule_id}_add').click(function() { addCartRuleOption(this); updateProductRuleShortDescription(this); });
	$(document).ready(function() { updateProductRuleShortDescription($('#product_rule_select_{$product_rule_id}_add')); });
</script>
