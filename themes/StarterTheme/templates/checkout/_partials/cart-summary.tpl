<section id="checkout-cart-summary">
  {block name="cart_summary_header"}
    <header>
      <h1 class="h3">{l s='Your order'}</h1>
      <p>{$cart.summary_string}</p>
    </header>
  {/block}

  {block name="cart_summary_body"}
    <div id="cart-summary">
      {foreach from=$cart.totals item="total"}
        <div class="{$total.type}">
          <span class="label">{$total.label}</span>
          <span class="value">{$total.amount}</span>
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
</section>
