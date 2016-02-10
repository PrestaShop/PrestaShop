<div class="cart-detailed-totals">
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
