<div class="card-block cart-summary-totals">
  {block name='cart_summary_body'}
    {foreach from=$cart.subtotals item="subtotal"}
      {if $subtotal.amount && $subtotal.type !== 'tax'}
        <div class="cart-summary-line" id="cart-subtotal-{$subtotal.type}">
          <span class="label">{$subtotal.label}</span>
          <span class="value">{$subtotal.value}</span>
        </div>
      {/if}
    {/foreach}
  {/block}

  {block name='cart_summary_total'}
    <div class="cart-summary-line cart-total">
      <span class="label">{$cart.total.label}</span>
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
