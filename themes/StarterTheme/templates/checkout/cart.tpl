{extends file="$layout"}

{block name="content"}
  <section id="main">

    <h1>{l s="Shopping Cart"}</h1>

    {block name="cart_overview"}
      {include file="checkout/_partials/cart-detailed.tpl" cart=$cart}
    {/block}

    <ul>
      <li><a href="{$urls.pages.index}">{l s="Continue shopping"}</a></li>
    </ul>

    {hook h="displayShoppingCart"}

    <div class="cart-subtotals">
      {foreach from=$cart.subtotals item="subtotal"}
        <div class="{$subtotal.type}">
          <span class="label">{$subtotal.label}</span>
          <span class="value">{$subtotal.amount}</span>
        </div>
      {/foreach}
    </div>

    <div class="cart-total">
      <span class="label">{$cart.total.label}</span>
      <span class="value">{$cart.total.amount}</span>
    </div>

    <div class="checkout">
      <ul>
        <li><a href="{$urls.pages.order}">{l s="Checkout"}</a></li>
      </ul>
      {hook h="displayExpressCheckout"}
    </div>

    {hook h="displayShoppingCartReassurance"}

  </section>
{/block}
