<div class="cart-overview -js-cart" data-refresh-url="{$urls.pages.cart}?refreshajax=1">
  <ul class="list-group list-group-flush">
    {foreach from=$cart.products item=product}
      <li class="list-group-item">{include file='checkout/_partials/cart-detailed-product-line.tpl' product=$product}</li>
      {if $product.customizations|count >1}
      <hr>
      {/if}
    {/foreach}
  </ul>
</div>
