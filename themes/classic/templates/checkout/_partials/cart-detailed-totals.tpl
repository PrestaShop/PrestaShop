<div class="cart-detailed-totals">

  <div class="card-block">
    {foreach from=$cart.subtotals item="subtotal"}
      {if $subtotal.amount && $subtotal.type !== 'tax'}
        <div class="cart-summary-line" id="cart-subtotal-{$subtotal.type}">
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
  </div>

  {block name='cart_voucher'}
    {include file='checkout/_partials/cart-voucher.tpl'}
  {/block}

  <hr>

  <div class="card-block">
    <div class="cart-summary-line cart-total">
      <span class="label">{$cart.totals.total.label} {$cart.labels.tax_short}</span>
      <span class="value">{$cart.totals.total.value}</span>
    </div>

    <div class="cart-summary-line">
      <span class="label sub">{$cart.subtotals.tax.label}</span>
      <span class="value sub">{$cart.subtotals.tax.value}</span>
    </div>
  </div>

  <hr>
</div>
