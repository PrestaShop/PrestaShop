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
<table class="product" width="100%" cellpadding="4" cellspacing="0">
    <thead>
    <tr>
        <th class="product header small" width="10%">{l s='Reference' pdf='true'}</th>
        <th class="product header small" width="50%">{l s='Product' pdf='true'}</th>
        <th class="product header small" width="5%">{l s='Qty' pdf='true'}</th>
        <th class="product header small" width="5%">{l s='Tax' pdf='true'}</th>
        <th class="product header small" width="15%">{l s='Unit Price' pdf='true'} <br /> {l s='(Tax Excl.)' pdf='true'}</th>
        <th class="product header center small" width="15%">{l s='Total' pdf='true'} <br /> {l s='(Tax Excl.)' pdf='true'}</th>
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
