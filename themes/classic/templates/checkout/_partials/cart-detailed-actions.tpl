<div class="checkout cart-detailed-actions card-block">
  {if $cart.minimalPurchaseRequired}
    <div class="alert alert-warning" role="alert">
      {$cart.minimalPurchaseRequired}
    </div>
    <div class="text-xs-center">
      <button type="button" class="btn btn-primary disabled" disabled>{l s='Checkout' d='Shop.Theme.Actions'}</button>
    </div>
  {else}
    <div class="text-xs-center">
      <a href="{$urls.pages.order}" class="btn btn-primary">{l s='Checkout' d='Shop.Theme.Actions'}</a>
      {hook h='displayExpressCheckout'}
    </div>
  {/if}
</div>
