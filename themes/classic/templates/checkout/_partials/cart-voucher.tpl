{if $cart.vouchers.allowed}
  <div class="cart-voucher">
    {foreach from=$cart.vouchers.added item=voucher}
      <li>{$voucher.name} <a href="{$voucher.delete_url}" data-link-action="remove-voucher">{l s='Remove'}</a></li>
    {/foreach}
    <form action="{$urls.pages.cart}" data-link-action="add-voucher" method="post">
      <input type="hidden" name="token" value="{$static_token}" />
      <input type="hidden" name="addDiscount" value="1" />
      <input type="text" name="discount_name" placeholder="{l s='Promo code'}" />
      <button type="submit"><span>{l s='ok'}</span></button>
    </form>
  </div>
{/if}
