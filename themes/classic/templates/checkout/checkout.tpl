<!doctype html>
<html lang="{$language.iso_code}">

  <head>
    {block name='head'}
      {include file='_partials/head.tpl'}
    {/block}
  </head>

  <body id="{$page.page_name}" class="{$page.body_classes|classnames}">

    <header id="header">
      {block name='header'}
        {include file='checkout/_partials/header.tpl'}
      {/block}
    </header>

    <main class="container">

      {block name='notifications'}
        {include file='_partials/notifications.tpl'}
      {/block}

      {block name='content'}
        <section id="content">
          <div class="row">
            <div class="col-md-8">
              {render file='checkout/checkout-process.tpl' ui=$checkout_process}
            </div>
            <div class="col-md-4">

              {include file='checkout/_partials/cart-summary.tpl' cart = $cart}

              {hook h='displayReassurance'}
            </div>
          </div>
        </section>
      {/block}
    </main>
    <footer id="footer">
      {block name='footer'}
        {include file='checkout/_partials/footer.tpl'}
      {/block}
    </footer>

  </body>

</html>
