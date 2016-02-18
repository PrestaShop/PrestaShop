{extends file=$layout}

{block name='content'}
  <section id="main">

    <h1>{l s='Shopping Cart'}</h1>

    {block name='cart_overview'}
      {include file='checkout/_partials/cart-detailed.tpl' cart=$cart}
    {/block}

    <ul>
      <li><a href="{$urls.pages.index}">{l s='Continue shopping'}</a></li>
    </ul>

    {hook h='displayShoppingCart'}

    {block name='cart_voucher'}
      {include file='checkout/_partials/cart-voucher.tpl'}
    {/block}
    
    {hook h='displayShoppingCart'}

    {block name='cart_totals'}
      {include file='checkout/_partials/cart-detailed-totals.tpl' cart=$cart}
    {/block}

    <div class="checkout">
      <ul>
        <li><a href="{$urls.pages.order}">{l s='Checkout'}</a></li>
      </ul>
      {hook h='displayExpressCheckout'}
    </div>

    {hook h='displayShoppingCartReassurance'}

  </section>
{/block}
