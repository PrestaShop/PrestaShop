<article class="product-miniature" itemscope itemtype="http://schema.org/Product">
  <a href="{$product.url}"><img src="{$product.cover.medium.url}" alt="{$product.cover.legend}"></a>
  <h1 class="h2" itemprop="name"><a href="{$product.url}">{$product.name}</a></h1>
  {if $product.add_to_cart_url}
      <a
        class = "add-to-cart"
        href  = "{$product.add_to_cart_url}"
        rel   = "nofollow"
        data-id-product="{$product.id_product}"
        data-id-product-attribute="{$product.id_product_attribute}"
      >{l s='Add to cart'}</a>
  {/if}
  <div class="price-container">
    {if $product.has_discount}
      <span class="regular-price">{$product.regular_price}</span>
      {if $product.discount_type === 'percentage'}
        <span class="discount-percentage">{$product.discount_percentage}</span>
      {/if}
    {/if}
    <span itemprop="price" class="price">{$product.price}</span>
  </div>
  {* TODO: Hooks *}

</article>
