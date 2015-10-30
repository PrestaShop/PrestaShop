{extends $layout}

{block name="content"}
  <section id="main">

    <h1>{l s='Your Shopping Cart'}</h1>

    {block name="cart_overview"}
      {include 'checkout/_partials/cart-overview.tpl' cart=$cart}
    {/block}

    <ul>
      <li><a href="{$urls.pages.index}">{l s='Continue shopping'}</a></li>
      <li><a href="{$urls.pages.order}">{l s='Proceed to checkout'}</a></li>
    </ul>

  </section>
{/block}
