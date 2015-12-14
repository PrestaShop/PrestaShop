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

      {$personal_information_section nofilter}

      {$addresses_section nofilter}

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
