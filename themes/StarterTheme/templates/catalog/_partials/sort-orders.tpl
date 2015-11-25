<div class="products-sort-order">
    <span>{l s='Sort by:'}</span>
    <form>
        {if $ps_search_encoded_facets}
            <input type="hidden" name="q" value="{$ps_search_encoded_facets}">
        {/if}
        {foreach from=$sort_orders item=sort_order}
            {if $sort_order.current}
                <button type="submit" class="current">{$sort_order.label}</button>
            {else}
                <button type="submit" name="order" value="{$sort_order.urlParameter}">{$sort_order.label}</button>
            {/if}

        {/foreach}
    </form>
</div>
