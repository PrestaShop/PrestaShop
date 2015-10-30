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

      {block name="cart_summary_section"}
        {$cart_summary nofilter}
      {/block}

      {block name="checkout_basic_information"}
        {include file="checkout/_partials/personal-details.tpl" customer=$customer  }
      {/block}

      {block name="checkout_login_form"}
        {include file="customer/_partials/login-form.tpl" back=$urls.pages.order}
      {/block}

      {block name="checkout_addresses"}
        {if $customer.is_logged}
          {block name="checkout_customer_addresses"}
            {include file="checkout/_partials/checkout-section-logged-addresses.tpl"}
          {/block}
        {else}
          {block name="checkout_guest_addresses"}
            {include file="customer/_partials/address-form.tpl" address_fields=$address_fields address=$address countries=$countries form_action=$urls.pages.order}
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
