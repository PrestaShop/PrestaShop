<section class="featured-products">
  <h1 class="h3 products-section-title text-uppercase">{l s='Popular Products'}</h1>
  <div class="products">
    {foreach from=$products item="product"}
      {include file="catalog/product-miniature.tpl" product=$product}
    {/foreach}
    <a class="all-product-link pull-xs-right" href="{$allProductsLink}">{l s='All products' mod='homefeatured'}<i class="material-icons">&#xE315;</i></a>
  </div>
</section>
