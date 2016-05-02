<div class="media-left">
  <a href="{$product.url}" title="{$product.name}">
    <img class="media-object" src="{$product.cover.small.url}" alt="{$product.name}" />
  </a>
</div>
<div class="media-body">
  <span class="product-name">{$product.name}</span>
  <span class="product-quantity">x{$product.quantity}</span>
  <span class="product-price pull-xs-right">{$product.price}</span>
  {hook h='displayProductPriceBlock' product=$product type="unit_price"}
</div>
