<div class="blockcart cart-preview col-md-2 _relative pull-xs-right" data-refresh-url="{$refresh_url}">
  <div class="header">
    <a rel="nofollow" href="{$cart_url}">
      <i class="material-icons shopping-cart">shopping_cart</i>
      <span>{l s='Cart' mod='blockcart'}</span>
      <span>({$cart.products_count})</span>
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
