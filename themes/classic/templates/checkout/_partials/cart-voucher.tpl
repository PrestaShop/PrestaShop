{if $cart.vouchers.allowed}
  <div class="block-promo">
    <div class="cart-voucher">
      {if $cart.vouchers.added}
        <ul class="promo-name card-block">
          {foreach from=$cart.vouchers.added item=voucher}
            <li class="cart-summary-line">
              <span class="label">{$voucher.name}</span>
              <a href="{$voucher.delete_url}" data-link-action="remove-voucher"><i class="material-icons">{l s='delete' d='Shop.Theme.Actions'}</i></a>
              <div class="pull-xs-right">
                {$voucher.reduction_formatted}
              </div>
            </li>
          {/foreach}
        </ul>
      {/if}
      <p>
        <a class="collapse-button promo-code-button" data-toggle="collapse" href="#promo-code" aria-expanded="false" aria-controls="promo-code">
          {l s='Have a promo code?' d='Shop.Theme.Checkout'}
        </a>
      </p>    
      <div class="promo-code collapse" id="promo-code">
        <form action="{$urls.pages.cart}" data-link-action="add-voucher" method="post">
          <input type="hidden" name="token" value="{$static_token}">
          <input type="hidden" name="addDiscount" value="1">
          <input class="promo-input" type="text" name="discount_name" placeholder="{l s='Promo code' d='Shop.Theme.Checkout'}">
          <button type="submit" class="btn btn-primary"><span>{l s='Add' d='Shop.Theme.Actions'}</span></button>
        </form>
      </div>
    </div>
  </div>
{/if}
