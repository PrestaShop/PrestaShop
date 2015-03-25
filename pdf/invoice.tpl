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

{include file="./invoice.style-tab.tpl"}

<table width="100%" id="body" border="0" cellspacing="0">
    <tr>
        <td colspan="8">&nbsp;</td>
        <td colspan="4" class="center bold"><h1>{l s='INVOICE' pdf='true'}</h1></td>
    </tr>
</table>


<table width="100%" id="body" border="0" cellpadding="2">
    <!-- Invoicing -->
    <tr>
        <td colspan="12">

            {include file="./invoice.addresses-tab.tpl"}

        </td>
    </tr>

    <tr>
        <td colspan="12" height="30">&nbsp;</td>
    </tr>

    <!-- TVA Info -->
    <tr>
        <td colspan="10" >

            {include file="./invoice.summary-tab.tpl"}

        </td>
        <td colspan="2">&nbsp;</td>
    </tr>

    <tr>
        <td colspan="12" height="20">&nbsp;</td>
    </tr>

    <!-- Product -->
    <tr>
        <td colspan="12">

            {include file="./invoice.product-tab.tpl"}

        </td>
    </tr>

    <tr>
        <td colspan="12" height="10">&nbsp;</td>
    </tr>

    <!-- TVA -->
    <tr>
        <!-- Code TVA -->
        <td colspan="6" class="left">

            {$tax_tab}

        </td>
        <td colspan="1">&nbsp;</td>
        <!-- Calcule TVA -->
        <td colspan="5" rowspan="5" class="right">

            {include file="./invoice.total-tab.tpl"}

        </td>
    </tr>

    <tr>
        <td colspan="12" height="10">&nbsp;</td>
    </tr>

    <tr>
        <td colspan="6" class="left">
            {include file="./invoice.payment-tab.tpl"}
        </td>
        <td colspan="1">&nbsp;</td>
    </tr>

    <tr>
        <td colspan="12" height="10">&nbsp;</td>
    </tr>

    <tr>
        <td colspan="7" class="left small">
            {$legal_free_text|escape:'html':'UTF-8'|nl2br}
        </td>
    </tr>

</table>
