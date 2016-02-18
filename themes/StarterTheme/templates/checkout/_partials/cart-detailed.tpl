<div class="cart-overview -js-cart" data-refresh-url="{$urls.pages.cart}?ajax=1">
  <div class="body">
    <ul>
      {foreach from=$cart.products item=product}
        <li>{include file='checkout/_partials/cart-detailed-product-line.tpl' product=$product}</li>
      {/foreach}
    </ul>
  </div>
</div>
