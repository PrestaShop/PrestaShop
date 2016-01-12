<section id="products">
  {if $products|count}
      <h1>{l s='Products'}</h1>

      {block name='sort_by'}
        {include file='catalog/_partials/sort-orders.tpl' sort_orders=$sort_orders}
      {/block}

      <div class="products row">
        {foreach from=$products item="product"}
          {block name='product_miniature'}
            {include file='catalog/product-miniature.tpl' product=$product}
          {/block}
        {/foreach}
      </div>

      {block name='pagination'}
        {include file='catalog/_partials/pagination.tpl' pagination=$pagination}
      {/block}
  {/if}
</section>
