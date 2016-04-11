<div class="cart-overview -js-cart" data-refresh-url="{url entity='cart' params=['ajax' => true, 'action' => 'refresh']}">
  {if $cart.products}
  <ul class="list-group list-group-flush">
    {foreach from=$cart.products item=product}
      <li class="list-group-item">{include file='checkout/_partials/cart-detailed-product-line.tpl' product=$product}</li>
      {if $product.customizations|count >1}
      <hr>
      {/if}
    {/foreach}
  </ul>
  {else}
    <p>{l s='There are no more items in your cart'}</p>
  {/if}
</div>
