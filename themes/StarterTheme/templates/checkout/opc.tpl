<!doctype html>
<html lang="{$language.iso_code}">

  <head>
    {block name="head"}
      {include file="_partials/head.tpl"}
    {/block}
  </head>

  <body id="{$page.page_name}">

    <header id="header">
      {block name="header"}
        {include file="checkout/_partials/header.tpl"}
      {/block}
    </header>

    {block name="notifications"}
      {block name="notifications"}
        {include file="_partials/notifications.tpl"}
      {/block}
    {/block}

    {block name="content"}

      {block name="shopping_cart_summary_section"}
        <section id="opc-cart-summary">
          <header>
            <h1 class="h3">{l s='Your order'}</h1>
          </header>

          {block name="shopping_cart_summary"}
            <div id="cart-summary">
              {$cart_summary nofilter}
            </div>
          {/block}

        </section>
      {/block}

      {block name="opc_addresses_section"}
        {if $customer.is_logged}
          {block name="opc_customer_addresses"}
            {include file="checkout/_partials/opc-section-logged-addresses.tpl"}
          {/block}
        {else}
          {block name="opc_login_or_registrer"}
            {include file="checkout/_partials/opc-section-login_or_register.tpl"}
          {/block}
        {/if}
      {/block}

      {$delivery_options nofilter}

      {$payment_options nofilter}

    {/block}

    <footer id="footer">
      {block name="footer"}
        {include file="checkout/_partials/footer.tpl"}
      {/block}
    </footer>

  </body>

</html>
