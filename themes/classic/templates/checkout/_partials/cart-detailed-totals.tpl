<div class="cart-detailed-totals">
  {foreach from=$cart.subtotals item="subtotal"}
    {if $subtotal.amount}
      <div class="card-block cart-summary-line" id="cart-subtotal-{$subtotal.type}">
        <span class="label">{$subtotal.label}</span>
        <span class="value">{$subtotal.value}</span>
      </div>
    {/if}
  {/foreach}

  <hr/>

  <div class="card-block cart-total cart-summary-line">
    <span class="label">{$cart.total.label}: </span>
    <span class="value">{$cart.total.value}</span>
  </div>

  <hr/>
</div>
