<div class="cart-summary-totals">
  {block name='cart_summary_body'}
    <div id="cart-summary">
      {foreach from=$cart.subtotals item="subtotal"}
        {if $subtotal.amount && $subtotal.type !== 'tax'}
          <div class="{$subtotal.type}">
            <span class="label">{$subtotal.label}</span>
            <span class="value pull-xs-right">{$subtotal.value}</span>
          </div>
        {/if}
      {/foreach}
    </div>
  {/block}

  {block name='cart_summary_totals'}
    <div class="cart-summary-totals">
      <span class="label">{$cart.total.label}</span>
      <span class="value pull-xs-right">{$cart.total.value}</span>
    </div>
  {/block}

  {block name='cart_summary_tax'}
    <div class="cart-summary-line">
      <span class="label sub">{$cart.subtotals.tax.label}</span>
      <span class="value sub">{$cart.subtotals.tax.value}</span>
    </div>
  {/block}
</div>
