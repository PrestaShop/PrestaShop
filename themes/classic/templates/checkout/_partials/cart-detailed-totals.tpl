<div class="cart-detailed-totals">
  {foreach from=$cart.subtotals item="subtotal"}
    {if $subtotal.amount}
      <div class="card-block cart-summary-line" id="cart-subtotal-{$subtotal.type}">
        <span class="label">
          {if 'products' == $subtotal.type}
            {$cart.summary_string}
          {else}
            {$subtotal.label}
          {/if}
        </span>
        <span class="value">{$subtotal.value}</span>
      </div>
    {/if}
  {/foreach}

  <hr/>

  <div class="card-block cart-total cart-summary-line">
    <span class="label">{$cart.total.label} {$cart.labels.tax_short}</span>
    <span class="value">{$cart.total.value}</span>
  </div>

  <hr/>
</div>
