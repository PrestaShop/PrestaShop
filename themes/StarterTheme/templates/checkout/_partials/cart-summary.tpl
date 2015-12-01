<section id="checkout-cart-summary">
  {block name="cart_summary_header"}
    <header>
      <h1 class="h3">{l s='Your order'}</h1>
      <p>{$cart.summary_string}</p>
    </header>
  {/block}

  {block name="cart_summary_body"}
    <div id="cart-summary">
      {foreach from=$cart.subtotals item="subtotal"}
        <div class="{$subtotal.type}">
          <span class="label">{$subtotal.label}</span>
          <span class="value">{$subtotal.amount}</span>
        </div>
      {/foreach}
    </div>
  {/block}

  {block name="cart_summary_product_list"}
    <div id="cart-summary-product-list">
      <ul>
        {foreach from=$cart.products item=product}
          <li>{include './cart-summary-product-line.tpl' product=$product}</li>
        {/foreach}
      </ul>
    </div>
  {/block}

  {block name="cart_summary_totals"}
    <div class="cart-summary-totals">
      <span class="label">{$cart.total.label}</span>
      <span class="value">{$cart.total.amount}</span>
    </div>
  {/block}
</section>
