{**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

{* Generate HTML code for printing Invoice Icon with link *}
<span class="btn-group-action">
	<span class="btn-group">
	{if Configuration::get('PS_INVOICE') && $order->invoice_number}
		<a class="btn btn-default _blank" href="{$link->getAdminLink('AdminPdf', true, [], ['submitAction' => 'generateInvoicePDF', 'id_order' => $order->id])|escape:'html':'UTF-8'}">
			<i class="icon-file-text"></i>
		</a>
	{/if}
	{* Generate HTML code for printing Delivery Icon with link *}
	{if $order->delivery_number}
		<a class="btn btn-default _blank" href="{$link->getAdminLink('AdminPdf', true, [], ['submitAction' => 'generateDeliverySlipPDF', 'id_order' => $order->id])|escape:'html':'UTF-8'}">
			<i class="icon-truck"></i>
		</a>
	{/if}
	</span>
</span>
