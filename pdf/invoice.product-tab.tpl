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
        <th class="product header small" width="15%">{l s='Reference' pdf='true'}</th>
        <th class="product header small" width="47%">{l s='Product' pdf='true'}</th>
        <th class="product header small" width="6%">{l s='Qty' pdf='true'}</th>
        <th class="product header small" width="5%">{l s='Tax' pdf='true'}</th>
        <th class="product header small" width="14%">{l s='Unit Price' pdf='true'} <br /> {l s='(Tax Excl.)' pdf='true'}</th>
        <th class="product header center small" width="14%">{l s='Total' pdf='true'} <br /> {l s='(Tax Excl.)' pdf='true'}</th>
    </tr>
    </thead>

    <tbody>

    <!-- PRODUCTS -->
    {foreach $order_details as $order_detail}
        {cycle values='#FFF,#DDD' assign=bgcolor}
        <tr class="product" style="background-color:{$bgcolor};">

            <td class="product center">
                {$order_detail.reference}
            </td>
            <td class="product left">
                <table width="100%">
                    <tr>
                        <td width="15%">
                            {if $display_product_images}
                                {if isset($order_detail.image) && $order_detail.image->id}
                                    {$order_detail.image_tag}
                                {/if}
                            {/if}
                        </td>
                        <td width="5%">&nbsp;</td>
                        <td width="80%">
                            {$order_detail.product_name}
                        </td>
                    </tr>
                </table>
            </td>
            <td class="product center">
                {$order_detail.product_quantity}
            </td>
            <td class="product center">
                {$order_detail.order_detail_tax_label}
            </td>
            <td class="product center">
                {displayPrice currency=$order->id_currency price=$order_detail.unit_price_tax_excl_including_ecotax}
                {if $order_detail.ecotax_tax_excl > 0}
                    <br>
                    <small>{{displayPrice currency=$order->id_currency price=$order_detail.ecotax_tax_excl}|string_format:{l s='ecotax: %s' pdf='true'}}</small>
                {/if}
            </td>
            {*
            <td>
                {if (isset($order_detail.reduction_amount) && $order_detail.reduction_amount > 0)}
                -{displayPrice currency=$order->id_currency price=$order_detail.reduction_amount}
                {elseif (isset($order_detail.reduction_percent) && $order_detail.reduction_percent > 0)}
                -{$order_detail.reduction_percent}%
                {else}
                --
                {/if}
            </td>
            *}
            <td  class="product right">
                {displayPrice currency=$order->id_currency price=$order_detail.total_price_tax_excl_including_ecotax}
            </td>
        </tr>

        {foreach $order_detail.customizedDatas as $customizationPerAddress}
            {foreach $customizationPerAddress as $customizationId => $customization}
                <tr class="customization_data" style="background-color: {$bgcolor};">
                    <td class=" center"> &nbsp;</td>

                    <td>
                        {if isset($customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_]) && count($customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_]) > 0}
                            <table style="width: 100%;">
                                {foreach $customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_] as $customization_infos}
                                    <tr>
                                        <td>
                                            {$customization_infos.name|string_format:{l s='%s:' pdf='true'}}
                                        </td>
                                        <td>{$customization_infos.value}</td>
                                    </tr>
                                {/foreach}
                            </table>
                        {/if}

                        {if isset($customization.datas[$smarty.const._CUSTOMIZE_FILE_]) && count($customization.datas[$smarty.const._CUSTOMIZE_FILE_]) > 0}
                            <table style="width: 100%;">
                                <tr>
                                    <td>{l s='image(s):' pdf='true'}</td>
                                    <td>{count($customization.datas[$smarty.const._CUSTOMIZE_FILE_])}</td>
                                </tr>
                            </table>
                        {/if}
                    </td>

                    <td class=" center">
                        ({if $customization.quantity == 0}1{else}{$customization.quantity}{/if})
                    </td>
                    <td class="">
                        --
                    </td>
                    <td class=" center">
                        --
                    </td>
                    <td class=" center">
                        --
                    </td>
                </tr>
                <!--if !$smarty.foreach.custo_foreach.last-->
            {/foreach}
        {/foreach}
    {/foreach}
    <!-- END PRODUCTS -->

    <!-- CART RULES -->
    {assign var="shipping_discount_tax_incl" value="0"}
    {foreach from=$cart_rules item=cart_rule name="cart_rules_loop"}
        {cycle values='#FFF,#DDD' assign=bgcolor}
        {if $smarty.foreach.cart_rules_loop.first}
            <tr>
                <td style="text-align:left; font-style: italic;" colspan="{$layout._colCount}">
                    <br><br>
                    {l s='Discounts' pdf='true'}
                </td>
            </tr>
        {/if}
        <tr style="line-height:6px;background-color:{$bgcolor};text-align:left;">
            <td style="text-align:left;vertical-align:top" colspan="{$layout._colCount - 1}">
                {$cart_rule.name}
            </td>
            <td style="text-align:right;vertical-align:top">
                {if $tax_excluded_display}
                    - {displayPrice currency=$order->id_currency price=$cart_rule.value_tax_excl}
                {else}
                    - {displayPrice currency=$order->id_currency price=$cart_rule.value}
                {/if}
            </td>
        </tr>
    {/foreach}

    </tbody>
</table>
