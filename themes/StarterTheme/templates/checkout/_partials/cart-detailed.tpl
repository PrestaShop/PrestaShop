<div class="cart-overview">
  <div class="body">
    <ul>
      {foreach from=$cart.products item=product}
        <li>{include file='checkout/_partials/cart-detailed-product-line.tpl' product=$product}</li>
      {/foreach}
    </ul>
  </div>
</div>
