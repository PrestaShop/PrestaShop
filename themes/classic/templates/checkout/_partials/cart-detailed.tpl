<div class="cart-overview js-cart" data-refresh-url="{url entity='cart' params=['ajax' => true, 'action' => 'refresh']}">
  {if $cart.products}
  <ul class="cart-items">
    {foreach from=$cart.products item=product}
      <li class="cart-item">{include file='checkout/_partials/cart-detailed-product-line.tpl' product=$product}</li>
      {if $product.customizations|count >1}
      <hr>
      {/if}
    {/foreach}
  </ul>
  {else}
    <span class="no-items">{l s='There are no more items in your cart' d='Shop.Theme.Checkout'}</span>
  {/if}
</div>
