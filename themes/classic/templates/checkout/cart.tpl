{extends file=$layout}

{block name='content'}

  <section id="main">
    <div class="cart-grid">

      <!-- Left Block: cart product informations & shpping -->
      <div class="cart-grid-body col-xs-12 col-md-8">

        <!-- cart products detailed -->
        <div class="card cart-container">
          <div class="card-block">
            <h1 class="h1">{l s='Shopping Cart'}</h1>
          </div>
          <hr>
          {block name='cart_overview'}
            {include file='checkout/_partials/cart-detailed.tpl' cart=$cart}
          {/block}

        </div>

        {block name='continue_shopping'}
          <a class="label" href="{$urls.pages.index}">
            <i class="material-icons">chevron_left</i>{l s='Continue shopping'}
          </a>
        {/block}

        <!-- shipping informations -->
        <div>
          {hook h='displayShoppingCartFooter'}
        </div>
      </div>

      <!-- Right Block: cart subtotal & cart total -->
      <div class="cart-grid-right col-xs-12 col-md-4">

        {block name='cart_summary'}
          <div class="card cart-summary">

            {block name='cart_voucher'}
              {include file='checkout/_partials/cart-voucher.tpl'}
            {/block}

            {hook h='displayShoppingCart'}

            {block name='cart_totals'}
              {include file='checkout/_partials/cart-detailed-totals.tpl' cart=$cart}
            {/block}

            {block name='cart_actions'}
              {include file='checkout/_partials/cart-detailed-actions.tpl' cart=$cart}
            {/block}

          </div>
        {/block}

        {block name='display_reassurance'}
          {hook h='displayReassurance'}
        {/block}

      </div>

    </div>
  </section>
{/block}
