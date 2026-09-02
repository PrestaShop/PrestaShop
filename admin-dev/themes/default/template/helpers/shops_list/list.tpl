{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
{strip}
<a href="javascript:void(0)" class="multistore-toggle dropdown-toggle" data-toggle="dropdown">
    <i class="material-icons">visibility</i>
    <span>{$current_shop_name|escape:'html':'UTF-8'}</span>
</a>
<ul class="dropdown-menu dropdown-menu-right list-dropdown-menu">
    <li class="{if !isset($current_shop_value) || $current_shop_value == ''} active {/if} all-stores">
        <a href="{$url|escape:'html':'UTF-8'}">{l s='All stores'}</a>
    </li>
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
                            {$shop_data['name']|escape:'html':'UTF-8'}
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
