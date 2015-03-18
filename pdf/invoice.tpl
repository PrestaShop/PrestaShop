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
<style>
    table, th, td
    {
        margin: 0!important;
        padding: 0!important;
        /*border: 1px solid green;*/
        vertical-align: middle;

    }

    table.product
    {
        border: 1px solid black;
        border-collapse: collapse;
    }

    th.product
    {
        border: 1px solid black;
    }

    th.header
    {
        height: 22px;
        background-color: #f0f0f0;
        vertical-align: middle;
        text-align: center;
        /*line-height:22px;*/
        font-weight: bold;
    }

    th.payment
    {
        height: 30px;
        background-color: #f0f0f0;
        vertical-align: middle;
        /*line-height:15px;*/
        font-weight: bold;
    }

    th.tva
    {
        height: 35px;
        background-color: #f0f0f0;
        vertical-align: middle;
        /*line-height:25px;*/
        font-weight: bold;
    }

    td.header
    {
        height: 25px;
        /*line-height:25px;*/
        vertical-align: middle;
        text-align: center;
    }

    td.product
    {
        height: 10px;
        /*line-height:10px;*/
        vertical-align: middle;
        /*border-top:hidden;*/
        /*border-bottom:1px solid black;*/
        border-left:1px solid black;
        border-right:1px solid black;
    }

    td.payment
    {
        height: 35px;
        vertical-align: middle;
        /*line-height:15px;*/
    }

    td.tva
    {
        height: 35px;
        vertical-align: middle;
        /*line-height:25px;*/
    }

    .fleft
    {
        float: left;
    }

    .left
    {
        text-align: left;
    }

    .fright
    {
        float: right;
    }

    .right
    {
        text-align: right;
    }

    .center
    {
        text-align: center;
    }

    .bold
    {
        font-weight: bold;
    }

    .border
    {
        border: 1px solid black;
    }

    .no_top_border
    {
        border-top:hidden;
        border-bottom:1px solid black;
        border-left:1px solid black;
        border-right:1px solid black;
    }

    .grey
    {
        background-color: #f0f0f0;
    }

    .small
    {
        font-size:small;
    }
</style>


<table width="100%" id="body" border="0" cellspacing="15">
    <tr>
        <td colspan="8">&nbsp;</td>
        <td colspan="4" class="center bold"><h1>{l s='INVOICE' pdf='true'}</h1></td>
    </tr>
</table>


<table width="100%" id="body" border="0" cellpadding="2">
    <!-- Invoicing -->
    <tr>
        <td colspan="4">
            <p class="small">&nbsp;</p>
            <p class="small">
                PrestaShop<br />
                55 rue Raspail<br />
                92300 Levallois-Perret<br />
                +33 (0)1 40 18 30 04<br />
                +33 (0)9 72 11 18 78<br />
                www.prestashop.com
            </p>
        </td>
        <td colspan="4">&nbsp;</td>
        <td colspan="4">
            <p class="small bold">{l s='Billing Address' pdf='true'}</p>
            <p class="small">
                {$invoice_address}
            </p>
        </td>
    </tr>

    <tr>
        <td colspan="12" height="30">&nbsp;</td>
    </tr>

    <!-- TVA Info -->
    <tr>
        <td colspan="8" >
            <table width="100%" border="1">
                <tr>
                    <th class="header small" width="15%" valign="middle">{l s='Invoice Number' pdf='true'}</th>
                    <th class="header small" width="20%" valign="middle">{l s='Order Date' pdf='true'}</th>
                    <th class="header small" width="16%" valign="middle">{l s='Order Number' pdf='true'}</th>
                    <th class="header small" width="49%" valign="middle">{l s='VAT Number' pdf='true'}</th>
                </tr>
                <tr>
                    <td class="header small">{$title|escape:'html':'UTF-8'}</td>
                    <td class="header small">{dateFormat date=$order->date_add full=0}</td>
                    <td class="header small">{$order->getUniqReference()}</td>
                    <td class="header small">{$data.addresses.invoice->vat_number}</td>
                </tr>
            </table>
        </td>
        <td colspan="4">&nbsp;</td>
    </tr>

    <tr>
        <td colspan="12" height="20">&nbsp;</td>
    </tr>

    <!-- Product -->
    <tr>
        <td colspan="12">
            <table class="product" width="100%" cellpadding="4" cellspacing="0">
                <thead>
                <tr>
                    <th class="product header small" width="10%">{l s='Reference' pdf='true'}</th>
                    <th class="product header small" width="51%">{l s='Product' pdf='true'}</th>
                    <th class="product header small" width="5%">{l s='Qty' pdf='true'}</th>
                    <th class="product header small" width="5%">{l s='Tax' pdf='true'}</th>
                    <th class="product header small" width="17%">{l s='Unit Price' pdf='true'} <br /> {l s='(Tax Excl.)' pdf='true'}</th>
                    <th class="product header center small" width="17%">{l s='Total' pdf='true'} <br /> {l s='(Tax Excl.)' pdf='true'}</th>
                </tr>
                </thead>
                <tbody>

                {foreach $order_details as $order_detail}
                    <tr>
                        <td class="product">
                            {$order_detail.reference}
                        </td>
                        <td>
                            {$order_detail.product_name}<br/>
                            {foreach $order_detail.customizedDatas as $customizationPerAddress}
                                {foreach $customizationPerAddress as $customizationId => $customization}
                                    {if isset($customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_]) && count($customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_]) > 0}
                                        {foreach $customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_] as $customization_infos}
                                            {l s='Domain name:'} {$customization_infos.value}
                                        {/foreach}
                                    {/if}
                                {/foreach}
                            {/foreach}
                        </td>
                        <td class="product center">
                            {$order_detail.product_quantity}
                        </td>
                        <td class="product center">
                            {$order_detail.order_detail_tax_label}
                        </td>
                        <td class="product center">
                            {displayPrice currency=$order->id_currency price=$order_detail.unit_price_tax_excl}
                        </td>
                        <td class="product right">
                            {displayPrice currency=$order->id_currency price=$order_detail.total_price_tax_excl}
                        </td>
                    </tr>
                {/foreach}

                </tbody>
            </table>
        </td>
    </tr>

    <tr>
        <td colspan="12" height="10">&nbsp;</td>
    </tr>

    <!-- Payment method + Discount -->
    <tr>
        <td colspan="12">
            <table width="100%" border="0">
                <tr>
                    <td colspan="6">
                        <table width="100%" border="1" cellpadding="2">
                            <tr>
                                <td class="payment center small grey bold" width="44%">{l s='Payment Method' pdf='true'}</td>
                                <td class="payment left" width="56%">
                                    <table width="100%" border="0" cellpadding="2">
                                        {foreach from=$order_invoice->getOrderPaymentCollection() item=payment}
                                            <tr>
                                                <td class="right small">{$payment->payment_method}</td>
                                                <td class="right small">{displayPrice currency=$payment->id_currency price=$payment->amount}</td>
                                            </tr>
                                        {/foreach}
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td colspan="1">&nbsp;</td>
                    <td colspan="5">
                        <table width="100%" border="1" cellpadding="2">
                            <tr>
                                <td class="payment center small grey bold" width="60%">{l s='Discount' pdf='true'}</td>
                                <td class="payment right" width="40%">
                                    {assign var="total_other" value=($order_invoice->total_paid_tax_incl - $order_invoice->total_products - $total_tax)}
                                    {displayPrice currency=$order->id_currency price=$total_other}
                                </td>
                            </tr>
                        </table>


                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td colspan="12" height="1">&nbsp;</td>
    </tr>

    <!-- TVA -->
    <tr>
        <!-- Code TVA -->
        <td colspan="6" class="left">

            {$tax_tab}

            <p class="small bold">{*vat_text*}</p>
        </td>
        <td colspan="1">&nbsp;</td>
        <!-- Calcule TVA -->
        <td colspan="5" class="right">
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
        </td>
    </tr>

</table>
