<ul class="list-group list-group-flush">
  {foreach from=$cart.products item=product}
    <li class="list-group-item">{include file='checkout/_partials/cart-detailed-product-line.tpl' product=$product}</li>
  {/foreach}
</ul>
