<section class="featured-products">
  <h1 class="h3 products-section-title text-uppercase">{l s='Popular Products'}</h1>
  <div class="products">
    {foreach from=$products item="product"}
      {include file="catalog/product-miniature.tpl" product=$product}
    {/foreach}
  </div>
</section>
