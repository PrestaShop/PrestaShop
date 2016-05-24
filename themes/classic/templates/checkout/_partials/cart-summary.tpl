<section id="checkout-cart-summary" class="card -js-cart" data-refresh-url="{$urls.pages.cart}?ajax=1">
  <div class="card-block">

    {block name='cart_summary_products'}
      <div class="cart-summary-products">

        <p>{$cart.summary_string}</p>

        <p>
          <a href="#" data-toggle="collapse" data-target="#cart-summary-product-list">
            {l s='show details'}
          </a>
        </p>

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

    {block name='cart_summary_subtotals'}
      {foreach from=$cart.subtotals item="subtotal"}
        {if $subtotal.amount && $subtotal.type !== 'tax'}
          <div class="cart-summary-line cart-summary-subtotals" id="cart-subtotal-{$subtotal.type}">
            <span class="label">{$subtotal.label}</span>
            <span class="value">{$subtotal.value}</span>
          </div>
        {/if}
      {/foreach}
    {/block}

  </div>

  <hr>

  {block name='cart_totals'}
    {include file='checkout/_partials/cart-summary-totals.tpl' cart=$cart}
  {/block}

</section>
