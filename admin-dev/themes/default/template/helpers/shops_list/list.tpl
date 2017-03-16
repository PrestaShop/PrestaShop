{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{strip}
<a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">
    {$current_shop_name} <i class="icon-caret-down"></i>
</a>
<ul class="dropdown-menu">
    <li{if !isset($current_shop_value) || $current_shop_value == ''} class="active"{/if}><a href="{$url|escape:'html':'UTF-8'}">{l s='All shops'}</a></li>
    {foreach key=group_id item=group_data from=$tree}
        {if !isset($multishop_context) || $is_group_context}
            <li class="group{if $current_shop_value == 'g-'|cat:$group_id} active{/if}{if $multishop_context_group == false} disabled{/if}">
                <a href="{if $multishop_context_group == false}#{else}{$url|escape:'html':'UTF-8'}g-{$group_id}{/if}">
                    {l s='%s group' sprintf=[$group_data['name']|escape:'html':'UTF-8']}
                </a>
            </li>
        {elseif !$is_all_context}
            <ul class="group {if $multishop_context_group == false} disabled{/if}">{l s='%s group' sprintf=[$group_data['name']|escape:'html':'UTF-8']}
        {/if}

        {if !isset($multishop_context) || $is_shop_context}
            {foreach key=shop_id item=shop_data from=$group_data['shops']}
                {if ($shop_data['active'])}
                    <li class="shop{if $current_shop_value == 's-'|cat:$shop_id} active{/if}{if $shop_data['uri'] == NULL} disabled{/if}">
                        <a href="{if $shop_data['uri'] == NULL}#{else}{$url|escape:'html':'UTF-8'}s-{$shop_id}{/if}">
                            {$shop_data['name']}
                        </a>

                        {if $shop_data['uri'] == NULL}
                            <a class="link-shop" href="{$link->getAdminLink('AdminShop', true)|escape:'html':'UTF-8'}" target="_blank">
                                <i class="material-icons">&#xE869;</i>
                            </a>
                        {else}
                            <a class="link-shop" href="{$shop_data['uri']}" target="_blank">
                                <i class="material-icons">&#xE8F4;</i>
                            </a>
                        {/if}

                    </li>
                {/if}
            {/foreach}
        {/if}

        {if !(!isset($multishop_context) || $is_group_context)}
            </ul>
        {/if}
    {/foreach}
</ul>
{/strip}
