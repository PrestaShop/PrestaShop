<div class="card-block cart-summary-totals">

  {block name='cart_summary_total'}
    <div class="cart-summary-line cart-total">
      <span class="label">{$cart.total.label} {$cart.labels.tax_short}</span>
      <span class="value">{$cart.total.value}</span>
    </div>
  {/block}

  {block name='cart_summary_tax'}
    <div class="cart-summary-line">
      <span class="label sub">{$cart.subtotals.tax.label}</span>
      <span class="value sub">{$cart.subtotals.tax.value}</span>
    </div>
  {/block}

</div>
