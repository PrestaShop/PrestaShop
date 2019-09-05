{**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

<script type="text/javascript">
	var summary_translation_undefined = '{l s='[undefined]' js=1}';
	var summary_translation_meta_informations = '{l s='This carrier is %1$s and the transit time is %2$s.' js=1 d='Admin.Shipping.Feature'}';
	var summary_translation_free = '{l s='free' js=1 d='Admin.Shipping.Feature'}';
	var summary_translation_paid = '{l s='not free' js=1 d='Admin.Shipping.Feature'}';
	var summary_translation_range = '{l s='This carrier can deliver orders from %1$s to %2$s.' js=1 d='Admin.Shipping.Feature'}';
	var summary_translation_range_limit =  '{l s='If the order is out of range, the behavior is to %3$s.' js=1 d='Admin.Shipping.Feature'}';
	var summary_translation_shipping_cost = '{l s='The shipping cost is calculated %1$s and the tax rule %2$s will be applied.' js=1 d='Admin.Shipping.Feature'}';
	var summary_translation_price = '{l s='according to the price' js=1 d='Admin.Shipping.Feature'}';
	var summary_translation_weight = '{l s='according to the weight' js=1 d='Admin.Shipping.Feature'}';
</script>

<div class="defaultForm">
	<div class="panel">
		<div class="panel-heading">{l s='Carrier name:' d='Admin.Shipping.Feature'} <strong id="summary_name"></strong></div>
		<div class="panel-body">
			<p id="summary_meta_informations"></p>
			<p id="summary_shipping_cost"></p>
			<p id="summary_range"></p>
			<div>
			{l s='This carrier will be proposed for those delivery zones:' d='Admin.Shipping.Feature'}
				<ul id="summary_zones"></ul>
			</div>
			<div>
				{l s='And it will be proposed for those client groups:' d='Admin.Shipping.Feature'}
				<ul id="summary_groups"></ul>
			</div>
			{if $is_multishop}
			<div>
				{l s='Finally, this carrier will be proposed in those shops:' d='Admin.Shipping.Feature'}
				<ul id="summary_shops"></ul>
			</div>
			{/if}
		</div>
	</div>
	{$active_form}
</div>
