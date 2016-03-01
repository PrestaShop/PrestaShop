<div class="blockcart cart-preview {if $cart.total.raw_amount > 0}active{/if}" data-refresh-url="{$refresh_url}">
  <div class="header">
    <a rel="nofollow" href="{if $cart.total.raw_amount > 0}{$cart_url}{else}#{/if}" title="{l s='cart'}">
      <i class="material-icons shopping-cart">shopping_cart</i>
      <span>{l s='Cart' mod='blockcart'}</span>
      <span class="cart-products-count">({$cart.products_count})</span>
    </a>
  </div>
  <div class="body">
    <ul>
      {foreach from=$cart.products item=product}
        <li>{include './blockcart-product-line.tpl' product=$product}</li>
      {/foreach}
    </ul>
    <div class="cart-subtotals">
      {foreach from=$cart.subtotals item="subtotal"}
        <div class="{$subtotal.type}">
          <span class="label">{$subtotal.label}</span>
          <span class="value">{$subtotal.amount}</span>
        </div>
      {/foreach}
    </div>
    <div class="cart-total">
      <span class="label">{$cart.total.label}</span>
      <span class="value">{$cart.total.amount}</span>
    </div>
  </div>
</div>
