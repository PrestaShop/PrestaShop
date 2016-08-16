<section id="products">

  {if $listing.products|count}
      <div class="row products-selection">
        <div class="col-lg-3 hidden-md-down total-products">
          {if $listing.products|count > 1}
            <p>{l s='There are %product_count% products.' d='Shop.Theme.Catalog' sprintf=['%product_count%' => $listing.products|count]}</p>
          {else}
            <p>{l s='There is %products_count% products.' d='Shop.Theme.Catalog' sprintf=['%products_count%' => $listing.products|count]}</p>
          {/if}
        </div>
        <div class="col-lg-5 col-md-6">
          <div class="row">
            {block name='sort_by'}
              {include file='catalog/_partials/sort-orders.tpl' sort_orders=$listing.sort_orders}
            {/block}
            {if !empty($listing.rendered_facets)}
            <div class="col-sm-3 col-xs-4 hidden-md-up">
              <button id="search_filter_toggler" class="btn btn-secondary">Filter</button>
            </div>
            {/if}
          </div>
        </div>
        <div class="col-sm-12 hidden-lg-up text-xs-center showing">
          {l s='Showing %from%-%to% of %total% item(s)' d='Shop.Theme.Catalog' sprintf=['%from%' => $listing.pagination.items_shown_from ,'%to%' => $listing.pagination.items_shown_to, '%total%' => $listing.pagination.total_items]}
        </div>
      </div>


    <div class="hidden-sm-down">{$listing.rendered_active_filters nofilter}</div>

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
