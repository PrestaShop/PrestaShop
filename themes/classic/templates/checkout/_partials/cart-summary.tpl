<section id="checkout-cart-summary" class="card card-block -js-cart" data-refresh-url="{$urls.pages.cart}?ajax=1">
  {block name='cart_summary_header'}
    <header>
      <div class="card cart-summary">
               <div class="cart-summary-title" align="center">
          <h1 class="h1">{l s='Shopping Summary'}</h1>
        </div>
    </header>
  {/block}

  <a href="#" data-toggle="collapse" data-target="#cart-summary-product-list">{l s='show details'}</button>

  {block name='cart_summary_product_list'}
    <div id="cart-summary-product-list" class="collapse">
      <ul class="media-list">
        {foreach from=$cart.products item=product}
          <li class="media">{include file='checkout/_partials/cart-summary-product-line.tpl' product=$product}</li>
        {/foreach}
      </ul>
    </div>
  {/block}

  {block name='cart_voucher'}
    {include file='checkout/_partials/cart-voucher.tpl'}
  {/block}
  <hr>
  {block name='cart_totals'}
    {include file='checkout/_partials/cart-summary-totals.tpl' cart=$cart}
  {/block}
</section>
