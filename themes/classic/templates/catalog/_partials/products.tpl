<section id="products">
  {if $listing.products|count}
    <div class="row products-selection">
      <div class="col-md-4">
        {if $listing.label}
          <h1 class="h1 title">{$listing.label}</h1>
        {/if}
      </div>
      <div class="col-md-8">
        <div class="products-select">
          <div>
            {if $listing.products|count > 1}
              <p>{l s='There are %product_count% products.' d='Shop.Theme.Catalog' sprintf=['%product_count%' => $listing.products|count]}</p>
            {else}
              <p>{l s='There is %products_count% products.' d='Shop.Theme.Catalog' sprintf=['%products_count%' => $listing.products|count]}</p>
            {/if}
          </div>
          <div>
            {block name='sort_by'}
              {include file='catalog/_partials/sort-orders.tpl' sort_orders=$listing.sort_orders}
            {/block}
          </div>
        </div>
      </div>
    </div>

    {$listing.rendered_active_filters nofilter}

    <div class="products row">
      {foreach from=$listing.products item="product"}
        {block name='product_miniature'}
          {include file='catalog/_partials/miniatures/product.tpl' product=$product}
        {/block}
      {/foreach}
    </div>

    {block name='pagination'}
      {include file='catalog/_partials/pagination.tpl' pagination=$listing.pagination}
    {/block}
  {/if}
</section>
