<section id="products">
  {if $products|count}
      <div class="row products-selection">
        <div class="col-md-4">
          <h1 class="h1 title">{$label}</h1>
        </div>
        <div class="col-md-8">
          <div class="products-select">
            <div>
              {if $products|count > 1}
                <p>{l s='There are %s products.' d='Shop.Theme.Catalog' sprintf=$products|count}</p>
              {else}
                <p>{l s='There is %s products.' d='Shop.Theme.Catalog' sprintf=$products|count}</p>
              {/if}
            </div>
            <div>
              {block name='sort_by'}
                {include file='catalog/_partials/sort-orders.tpl' sort_orders=$sort_orders}
              {/block}
            </div>
          </div>
        </div>
      </div>

      {if isset($rendered_active_filters)}
        {$rendered_active_filters nofilter}
      {/if}

      <div class="products row">
        {foreach from=$products item="product"}
          {block name='product_miniature'}
            {include file='catalog/_partials/miniatures/product.tpl' product=$product}
          {/block}
        {/foreach}
      </div>

      {block name='pagination'}
        {include file='catalog/_partials/pagination.tpl' pagination=$pagination}
      {/block}
  {/if}
</section>
