{extends file=$layout}

{block name='content'}

  <section id="main">
    <div class="cart-grid">

      <!-- Left Block: cart product informations & shpping -->
      <div class="cart-grid-body col-xs-12 col-md-8">

        <!-- cart products detailed -->
        <div class="cart-body">
          <div class="cart-body-header">
            <h1>{l s='Shopping Cart'}</h1>
          </div>

          {block name='cart_overview'}
            {include file='checkout/_partials/cart-detailed.tpl' cart=$cart}
          {/block}

        </div>

        <a class="label" href="{$urls.pages.index}">
          <i class="material-icons">chevron_left</i>{l s='Continue shopping'}
        </a>

        <!-- shipping informations -->
        <div class="cart-shipping-information">
          {hook h='displayShoppingCart'}
        </div>
      </div>

      <!-- Right Block: cart subtotal & cart total -->
      <div class="cart-grid-right col-xs-12 col-md-4">

        <div class="cart-summary">

          <div class="cart-summary-line" id="items-subtotal">
            <span class="label _bolder">{$cart.summary_string}</span>
            <span class="value">{$cart.total.amount}</span>
          </div>

          {foreach from=$cart.subtotals item="subtotal"}
            <div class="cart-summary-line" id="cart-subtotal-{$subtotal.type}">
              <span class="label">{$subtotal.label}</span>
              <span class="value">{$subtotal.amount}</span>
            </div>
          {/foreach}

          <div class="cart-total cart-summary-line">
            <span class="label">{$cart.total.label}: </span>
            <span class="value">{$cart.total.amount}</span>
          </div>

          <div class="checkout text-xs-center">
            <ul>
              <li>
                <a href="{$urls.pages.order}" class="button-primary">{l s='Checkout'}</a>
              </li>
            </ul>
            {hook h='displayExpressCheckout'}
          </div>

        </div>

        {hook h='displayShoppingCartReassurance'}
      </div>
    </div>
  </section>
{/block}
