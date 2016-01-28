<section id="products">
  {if $products|count}
      <div class="row _margin-top-large _margin-bottom-medium">
        <div class="col-md-5">
          <h1 class="h4 text-uppercase _bolder _gray-dark">{$category.name}</h1>
        </div>
        <div class="col-md-7">
          <div class="_display-table pull-xs-right">
            <div class="_display-table-cell">
              {if $products|count > 1}
                <p>{l s='There are %s products.' sprintf=$products|count} |</p>
              {else}
                <p>{l s='There is %s products.' sprintf=$products|count} |</p>
              {/if}
            </div>
            <div class="_display-table-cell">
              {block name='sort_by'}
                {include file='catalog/_partials/sort-orders.tpl' sort_orders=$sort_orders}
              {/block}
            </div>
          </div>
        </div>

      </div>
      <div class="products row">
        {foreach from=$products item="product"}
          {block name='product_miniature'}
            {include file='catalog/product-miniature.tpl' product=$product columns=4}
          {/block}
        {/foreach}
      </div>

      {block name='pagination'}
        {include file='catalog/_partials/pagination.tpl' pagination=$pagination}
      {/block}
  {/if}
</section>
