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

<!--  TAX DETAILS -->
{if $tax_exempt}
    {l s='Exempt of VAT according section 259B of the General Tax Code.' pdf='true'}
{elseif (isset($tax_breakdowns) && $tax_breakdowns)}

    <table width="100%" border="1" cellpadding="2">
        <tr>
            <th class="header small">{l s='Tax Detail' pdf='true'}</th>
            <th class="header small">{l s='Tax Rate' pdf='true'}</th>
            {if $display_tax_bases_in_breakdowns}
                <th class="header small">{l s='Total (Tax Excl.)' pdf='true'}</th>
            {/if}
            <th class="header small">{l s='Total Tax' pdf='true'}</th>
        </tr>

        {foreach $tax_breakdowns as $label => $bd}

            {assign var=label_printed value=false}

            {foreach $bd as $line}
                {if $line.rate == 0}
                    {continue}
                {/if}
                <tr>
                    <td>
                        {if !$label_printed}
                            {if $label == 'product_tax'}
                                {l s='Products' pdf='true'}
                            {elseif $label == 'shipping_tax'}
                                {l s='Shipping' pdf='true'}
                            {elseif $label == 'ecotax_tax'}
                                {l s='Ecotax' pdf='true'}
                            {elseif $label == 'wrapping_tax'}
                                {l s='Wrapping' pdf='true'}
                            {/if}
                            {assign var=label_printed value=true}
                        {/if}
                    </td>

                    <td class="center">
                        {$line.rate} %
                    </td>

                    {if $display_tax_bases_in_breakdowns}
                        <td class="right">
                            {if isset($is_order_slip) && $is_order_slip}- {/if}
                            {displayPrice currency=$order->id_currency price=$line.total_tax_excl}
                        </td>
                    {/if}

                    <td class="right">
                        {if isset($is_order_slip) && $is_order_slip}- {/if}
                        {displayPrice currency=$order->id_currency price=$line.total_amount}
                    </td>
                </tr>
            {/foreach}

        {/foreach}

    </table>

    {$tax_label}

{/if}
<!--  / TAX DETAILS -->
