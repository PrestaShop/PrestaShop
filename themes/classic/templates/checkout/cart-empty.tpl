{extends file='checkout/cart.tpl'}

{block name='content' append}
  {hook h='displayCrossSellingShoppingCart'}
{/block}

{block name='cart_overview' append}
  <a class="p-l-2" href="{$urls.pages.index}">
    {l s='Continue shopping' d='Shop.Theme.Actions'}<i class="material-icons">chevron_right</i>
  </a>
{/block}

{block name='cart_actions'}
  <div class="checkout text-xs-center card-block">
    <button type="button" class="btn btn-primary disabled" disabled>{l s='Checkout' d='Shop.Theme.Actions'}</button>
  </div>
{/block}

{block name='continue_shopping'}{/block}
{block name='cart_voucher'}{/block}
{block name='display_reassurance'}{/block}
