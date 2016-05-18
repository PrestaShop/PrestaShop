<section id="checkout-cart-summary" class="card -js-cart" data-refresh-url="{$urls.pages.cart}?ajax=1">

  {block name='cart_summary_products'}
    <div class="card-block cart-summary-products">
      <header>
        <p>{$cart.summary_string}</p>
      </header>

      <a href="#" data-toggle="collapse" data-target="#cart-summary-product-list">
        {l s='show details'}
      </a>

      {block name='cart_summary_product_list'}
        <div class="collapse" id="cart-summary-product-list">
          <ul class="media-list">
            {foreach from=$cart.products item=product}
              <li class="media">{include file='checkout/_partials/cart-summary-product-line.tpl' product=$product}</li>
            {/foreach}
          </ul>
        </div>
      {/block}
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
