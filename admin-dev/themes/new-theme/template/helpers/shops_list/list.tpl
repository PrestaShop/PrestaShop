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
{strip}
<ul class="items-list">
    <li{if !isset($current_shop_value) || $current_shop_value == ''} class="active"{/if}>
      <a class="dropdown-item" href="{$url|escape:'html':'UTF-8'}">{l s='All shops' d='Admin.Global'}</a>
    </li>
    {foreach key=group_id item=group_data from=$tree}
        {if !isset($multishop_context) || $is_group_context}
            <li class="group{if $current_shop_value == 'g-'|cat:$group_id} active{/if}">
                <a class="dropdown-item{if $multishop_context_group == false} disabled{/if}" href="{if $multishop_context_group == false}#{else}{$url|escape:'html':'UTF-8'}g-{$group_id}{/if}">
                    {l s='%s group' sprintf=[$group_data['name']|escape:'html':'UTF-8']}
                </a>
            </li>
        {elseif !$is_all_context}
            <ul class="group {if $multishop_context_group == false} disabled{/if}">{l s='%s group' sprintf=[$group_data['name']|escape:'html':'UTF-8']}
        {/if}

        {if !isset($multishop_context) || $is_shop_context}
            {foreach key=shop_id item=shop_data from=$group_data['shops']}
                {if ($shop_data['active'])}
                    <li class="shop{if $current_shop_value == 's-'|cat:$shop_id} active{/if}">
                        <a class="dropdown-item {if $shop_data['uri'] == NULL} disabled{/if}" href="{if $shop_data['uri'] == NULL}#{else}{$url|escape:'html':'UTF-8'}s-{$shop_id}{/if}">
                            {$shop_data['name']}
                        </a>
                        {if $shop_data['uri'] == NULL}
                            <a class="link-shop" href="{$link->getAdminLink('AdminShop', true)|escape:'html':'UTF-8'}" target="_blank">
                              <i class="material-icons">&#xE869;</i>
                            </a>
                        {else}
                            <a class="link-shop" href="{$link->getBaseLink($shop_data['id_shop'])}" target="_blank">
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
