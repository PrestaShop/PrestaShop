<div class="cart-summary-totals">
  {block name='cart_summary_body'}
    <div id="cart-summary">
      {foreach from=$cart.subtotals item="subtotal"}
        {if $subtotal.amount}
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
</div>
