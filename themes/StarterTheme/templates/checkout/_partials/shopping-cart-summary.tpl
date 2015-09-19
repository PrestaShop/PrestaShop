<div class="cart-preview cart-summary">
  <div class="header">
    <span>{l s='Cart'}</span>
    <span>{$cart.summary_string}</span>
  </div>
  <div class="body">
    <ul>
      {foreach from=$cart.products item=product}
        <li>{include './shopping-cart-product-line.tpl' product=$product}</li>
      {/foreach}
    </ul>
    <div class="cart-totals">
      {foreach from=$cart.totals item="total"}
        <div class="{$total.type}">
          <span class="label">{$total.label}</span>
          <span class="value">{$total.amount}</span>
        </div>
      {/foreach}
    </div>
  </div>
</div>
