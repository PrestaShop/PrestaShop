{if $cart.vouchers.allowed}
  {foreach from=$cart.vouchers.added item=voucher}
    <li>{$voucher.name} <a href="{$voucher.delete_url}">{l s='Remove'}</a></li>
  {/foreach}
  <form action="{$urls.pages.cart}" method="post">
    <input type="hidden" name="token" value="{$static_token}" />
    <input type="text" name="discount_name" value="{*if isset($discount_name) && $discount_name}{$discount_name}{/if*}" placeholder="{l s='Promo code'}" />
    <button type="submit" name="addDiscount"><span>{l s='ok'}</span></button>
  </form>
  {*if $displayVouchers}
    <p id="title" class="title-offers">{l s='Take advantage of our exclusive offers:'}</p>
    <div id="display_cart_vouchers">
      {foreach $displayVouchers as $voucher}
        {if $voucher.code != ''}<span class="voucher_name" data-code="{$voucher.code|escape:'html':'UTF-8'}">{$voucher.code|escape:'html':'UTF-8'}</span> - {/if}{$voucher.name}<br />
      {/foreach}
    </div>
  {/if*}
{/if}
