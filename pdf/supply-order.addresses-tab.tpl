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
<table id="addresses-tab" cellspacing="0" cellpadding="0">
	<tr>
		<td width="40%"><span class="bold"> </span><br/><br/>
			{$shop_name}<br/>
			{$address_warehouse->address1}<br/>
			{if !empty($address_warehouse->address2)}{$address_warehouse->address2}<br/>{/if}
			{$address_warehouse->postcode} {$address_warehouse->city}
		</td>
		<td width="20%">&nbsp;</td>
		<td width="40%"><span class="bold"> </span><br/><br/>
			{$supply_order->supplier_name}<br/>
			{$address_supplier->address1}<br/>
			{if !empty($address_supplier->address2)}{$address_supplier->address2}<br/>{/if}
			{$address_supplier->postcode} {$address_supplier->city}<br/>
			{$address_supplier->country}
		</td>
	</tr>
</table>
