<section class="product-miniature" itemscope itemtype="http://schema.org/Product">
  <a href="{$product.url}"><img src="{$product.cover.medium.url}" alt="{$product.cover.legend}"></a>
  <h1 class="h2" itemprop="name"><a href="{$product.url}">{$product.name}</a></h1>
  {if $product.has_discount}
    <span class="regular-price">{$product.regular_price}</span>
    {if $product.discount_type === 'percentage'}
      <span class="discount-percentage">{$product.discount_percentage}</span>
    {/if}
  {/if}
  <span itemprop="price" class="price">{$product.price}</span>
      {*hook h="displayProductPriceBlock" product=$product type="before_price"}
      {if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if*}

</section>
