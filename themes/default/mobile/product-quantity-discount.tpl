{*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license	http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if (isset($quantity_discounts) && count($quantity_discounts) > 0)}
<!-- quantity discount -->
<ul class="idTabs clearfix">
	<li><a href="#discount" style="cursor: pointer" class="selected" data-ajax="false">{l s='Sliding scale pricing'}</a></li>
</ul>
<div id="quantityDiscount">
	<table class="std">
		<thead>
			<tr>
				<th>{l s='product'}</th>
				<th>{l s='from (qty)'}</th>
				<th>{l s='discount'}</th>
			</tr>
		</thead>
		<tbody>
			<tr id="noQuantityDiscount">
				<td colspan='3'>{l s='There is no quantity discount for this product.'}</td>
			</tr>
			{foreach from=$quantity_discounts item='quantity_discount' name='quantity_discounts'}
			<tr id="quantityDiscount_{$quantity_discount.id_product_attribute}">
				<td>
					{if (isset($quantity_discount.attributes) && ($quantity_discount.attributes))}
						{$product->getProductName($quantity_discount.id_product, $quantity_discount.id_product_attribute)}
					{else}
						{$product->getProductName($quantity_discount.id_product)}
					{/if}
				</td>
				<td>{$quantity_discount.quantity|intval}</td>
				<td>
					{if $quantity_discount.price != 0 OR $quantity_discount.reduction_type == 'amount'}
						-{convertPrice price=$quantity_discount.real_value|floatval}
					{else}
						-{$quantity_discount.real_value|floatval}%
					{/if}
				</td>
			</tr>
			{/foreach}
		</tbody>
	</table>
</div>
{/if}
