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
    <section id="content">

      {block name="cart_summary_section"}
        {$cart_summary nofilter}
      {/block}

      {if !$customer.is_logged}
        {block name="checkout_login_form"}
          <section class="login-form ps-shown-by-js">
            {include file="customer/_partials/login-form.tpl" back=$urls.pages.order}
          </section>
        {/block}
      {/if}

      {block name="checkout_basic_information"}
        {include file="checkout/_partials/personal-details.tpl" customer=$customer}
      {/block}

      {block name="checkout_addresses"}

        <header>
          <h1 class="h3">{l s='Addresses'}</h1>
        </header>

        {if $customer.is_logged && count($customer.addresses) > 0}
          {block name="checkout_customer_addresses"}
            {include file="checkout/_partials/checkout-section-logged-addresses.tpl"}
          {/block}
        {else}
          {block name="checkout_address_forms"}
            {$address_form_delivery nofilter}
            {$address_form_invoice nofilter}
          {/block}
        {/if}

      {/block}

      {$delivery_options nofilter}

      {$payment_options nofilter}

    </section>
    {/block}

    <footer id="footer">
      {block name="footer"}
        {include file="checkout/_partials/footer.tpl"}
      {/block}
    </footer>

  </body>

</html>
