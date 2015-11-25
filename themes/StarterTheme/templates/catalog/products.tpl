{if $products|count}
  <section id="products">
    <h1>{l s='Products'}</h1>

    {block name="sort_by"}
      {include './_partials/sort-orders.tpl' sort_orders=$sort_orders}
    {/block}

    <div class="products">
      {foreach from=$products item="product"}
        {block name="product_miniature"}
          {include './product-miniature.tpl' product=$product}
        {/block}
      {/foreach}
    </div>

    {block name="pagination"}
      {include './_partials/pagination.tpl' pagination=$pagination}
    {/block}

  </section>
{/if}
