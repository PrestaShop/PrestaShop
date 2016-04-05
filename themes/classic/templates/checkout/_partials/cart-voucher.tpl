{if $cart.vouchers.allowed}
  <div class="block-promo">
    <div class="cart-voucher">
      <ul class="promo-name">
        {foreach from=$cart.vouchers.added item=voucher}
          <li>{$voucher.name}
            <div class="pull-xs-right">
            {if $voucher.reduction_formated }
              {$voucher.reduction_formated}
              <a class="pull-xs-right" href="{$voucher.delete_url}" data-link-action="remove-voucher"><i class="material-icons">{l s='delete'}</i></a></li>
            {/if}
            </div>
        {/foreach}
      </ul>
      <a class="collapse-button" data-toggle="collapse" href="#promo-code" aria-expanded="false" aria-controls="promo-code">
        {l s='Have a promo code ?'}
      </a>
      <div class="promo-code collapse" id="promo-code">
        <form action="{$urls.pages.cart}" data-link-action="add-voucher" method="post">
          <input type="hidden" name="token" value="{$static_token}" />
          <input type="hidden" name="addDiscount" value="1" />
          <input class="promo-input" type="text" name="discount_name" placeholder="{l s='Promo code'}" />
          <button type="submit" class="btn btn-primary"><span>{l s='Add'}</span></button>
        </form>
      </div>
    </div>
  </div>
{/if}
