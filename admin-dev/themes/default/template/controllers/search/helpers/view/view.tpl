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

<script type="text/javascript">
$(function() {
    $('#content .panel').highlight('{$query}');
});
</script>

{if $query}
    <h2>
    {if isset($nb_results) && $nb_results == 0}
        <h2>{l s='There are no results matching your query "%s".' sprintf=[$query] html=1 d='Admin.Navigation.Search'}</h2>
    {elseif isset($nb_results) && $nb_results == 1}
        {l s='1 result matches your query "%s".' sprintf=[$query] html=1 d='Admin.Navigation.Search'}
    {elseif isset($nb_results)}
        {l s='%d results match your query "%s".' sprintf=[$nb_results|intval, $query] html=1 d='Admin.Navigation.Search'}
    {/if}
    </h2>
{/if}

{if $query && isset($nb_results) && $nb_results}

    {if isset($features)}
    <div class="panel" data-role="features">
        <h3>
            {if $features|@count == 1}
                {l s='1 feature' d='Admin.Navigation.Search'}
            {else}
                {l s='%d features' sprintf=[$features|@count] d='Admin.Navigation.Search'}
            {/if}
        </h3>
        <table class="table">
            <tbody>
            {foreach $features key=key item=feature}
                {foreach $feature key=k item=val name=feature_list}
                    <tr>
                        <td><a href="{$val.link}"><strong>{$key}</strong></a></td>
                    </tr>
                {/foreach}
            {/foreach}
            </tbody>
        </table>
    </div>
    {/if}

    {if isset($modules) && $modules}
    <div class="panel" data-role="modules">
        <h3>
            {if $modules|@count == 1}
                {l s='1 module' d='Admin.Navigation.Search'}
            {else}
                {l s='%d modules' sprintf=[$modules|@count] d='Admin.Navigation.Search'}
            {/if}
        </h3>
        <table class="table">
            <tbody>
            {foreach $modules key=key item=module}
                <tr>
                    <td><a href="{$module->linkto|escape:'html':'UTF-8'}"><strong>{$module->displayName}</strong></a></td>
                    <td><a href="{$module->linkto|escape:'html':'UTF-8'}">{$module->description}</a></td>
                </tr>
            {/foreach}
        </tbody>
        </table>
    </div>
    {/if}

    {if isset($categories) && $categories}
    <div class="panel" data-role="categories">
        <h3>
            {if $categories|@count == 1}
                {l s='1 category' d='Admin.Navigation.Search'}
            {else}
                {l s='%d categories' sprintf=[$categories|@count] d='Admin.Navigation.Search'}
            {/if}
        </h3>
        <table class="table" style="border-spacing : 0; border-collapse : collapse;">
            {foreach $categories key=key item=category}
                <tr>
                    <td>{$category}</td>
                </tr>
            {/foreach}
        </table>
    </div>
    {/if}

    {if isset($products) && $products &&
        isset($productsCount) && $productsCount}
    <div class="panel" data-role="products">
        <h3>
            {if $productsCount == 1}
                {l s='1 product' d='Admin.Navigation.Search'}
            {else}
                {l s='%d products' sprintf=[$productsCount] d='Admin.Navigation.Search'}
            {/if}
        </h3>
        {$products}
    </div>
    {/if}

    {if isset($customers) && $customers &&
        isset($customerCount) && $customerCount}
    <div class="panel" data-role="customers">
        <h3>
            {if $customerCount == 1}
                {l s='1 customer' d='Admin.Navigation.Search'}
            {else}
                {l s='%d customers' sprintf=[$customerCount] d='Admin.Navigation.Search'}
            {/if}
        </h3>
        {$customers}
    </div>
    {/if}

    {if isset($orders) && $orders &&
    isset($orderCount) && $orderCount}
    <div class="panel" data-role="orders">
        <h3>
            {if $orderCount == 1}
                {l s='1 order' d='Admin.Navigation.Search'}
            {else}
                {l s='%d orders' sprintf=[$orderCount] d='Admin.Navigation.Search'}
            {/if}
        </h3>
        {$orders}
    </div>
    {/if}

{/if}
<div class="row" data-role="search-panels">
    {foreach $searchPanels key=key item=searchPanel}
        <div class="col-lg-{if $searchPanels|@count <= 2}6{else}4{/if}">
            <div class="panel">
                <h3>{$searchPanel.title}</h3>
                <a href="{$searchPanel.link}" class="btn btn-default{if !isset($searchPanel.is_external_link) || true === $searchPanel.is_external_link} _blank{/if}">{$searchPanel.button_label}</a>
            </div>
        </div>
    {/foreach}
</div>
