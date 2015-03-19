{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<table width="100%" border="1" cellpadding="4">
    <tr>
        <td class="grey" width="60%">
            {l s='Total (Tax Excl.)' pdf='true'}
        </td>
        <td class="right" width="40%">
            {displayPrice currency=$order->id_currency price=$order_invoice->total_paid_tax_excl}
        </td>
    </tr>
    <tr>
        <td class="grey">
            {l s='Total Tax' pdf='true'}
        </td>
        <td class="right">
            {assign var="total_tax" value=($order_invoice->total_paid_tax_incl - $order_invoice->total_paid_tax_excl)}
            {displayPrice currency=$order->id_currency price=$total_tax}
        </td>
    </tr>
    <tr>
        <td class="grey">
            {l s='Total (Tax Incl.)' pdf='true'}
        </td>
        <td class="right">
            {displayPrice currency=$order->id_currency price=$order_invoice->total_paid_tax_incl}
        </td>
    </tr>
</table>
