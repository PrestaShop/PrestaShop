<div class="checkout cart-detailed-actions text-xs-center card-block">
  {if $cart.minimalPurchaseRequired}
    <div class="alert alert-warning" role="alert">
      {$cart.minimalPurchaseRequired}
    </div>
    <button type="button" class="btn btn-primary disabled" disabled>{l s='Checkout'}</button>
  {else}
    <a href="{$urls.pages.order}" class="btn btn-primary">{l s='Checkout'}</a>
    {hook h='displayExpressCheckout'}
  {/if}
</div>
