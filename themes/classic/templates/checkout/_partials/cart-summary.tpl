<section id="checkout-cart-summary" class="card card-block">
  {block name='cart_summary_header'}
    <header>
      <p class="_bolder">{$cart.summary_string}<span class="pull-xs-right">{$cart.total.amount}</span></p>
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

  {block name='cart_summary_body'}
    <div id="cart-summary">
      {foreach from=$cart.subtotals item="subtotal"}
        <div class="{$subtotal.type}">
          <span class="label">{$subtotal.label}</span>
          <span class="value pull-xs-right">{$subtotal.amount}</span>
        </div>
      {/foreach}
    </div>
  {/block}
  <hr>
  {block name='cart_summary_totals'}
    <div class="cart-summary-totals">
      <span class="label">{$cart.total.label}<span class="sub">&nbsp;{l s='(tax ecl)'}</span></span>
      <span class="value pull-xs-right">{$cart.total.amount}</span>
    </div>
  {/block}
</section>
