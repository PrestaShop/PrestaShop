{**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *}
{extends file=$layout}

{block name='content'}
  <div class="cart-grid row">
    <!-- Left Block: cart product informations & shpping -->
    <div class="cart-grid__body col-lg-8">
      <h1 class="h4">{l s='Shopping Cart' d='Shop.Theme.Checkout'}</h1>

      <!-- cart products detailed -->
      <div class="cart-container mb-3">
        <div class="js-cart-update-alert" data-alert="{l s='has been removed from the cart.' d='Shop.Theme.Actions' js=1}"></div>
        
        {block name='cart_overview'}
          {include file='checkout/_partials/cart-detailed.tpl' cart=$cart}
        {/block}

        {block name='continue_shopping'}
          <a class="btn btn-outline-primary btn-with-icon" href="{$urls.pages.index}">
            <i class="material-icons rtl-flip" aria-hidden="true">chevron_left</i>
            {l s='Continue shopping' d='Shop.Theme.Actions'}
          </a>
        {/block}

        <!-- shipping informations -->
        {block name='hook_shopping_cart_footer'}
          {hook h='displayShoppingCartFooter'}
        {/block}
      </div>
    </div>


    <!-- Right Block: cart subtotal & cart total -->
    <div class="cart-grid__right col-lg-4">
      <h2 class="h4">{l s='Order summary' d='Shop.Theme.Checkout'}</h2>
      {block name='cart_summary'}
        <div class="card cart-summary">
          {block name='hook_shopping_cart'}
            {hook h='displayShoppingCart'}
          {/block}

          {block name='cart_totals'}
            {include file='checkout/_partials/cart-detailed-totals.tpl' cart=$cart}
          {/block}

          {block name='cart_actions'}
            {include file='checkout/_partials/cart-detailed-actions.tpl' cart=$cart}
          {/block}
        </div>
      {/block}

      <hr />

      {block name='hook_reassurance'}
        {hook h='displayReassurance'}
      {/block}
    </div>
  </div>

  {hook h='displayCrossSellingShoppingCart'}
{/block}
