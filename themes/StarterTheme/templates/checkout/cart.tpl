{extends $layout}

{block name="content"}
  <section id="main">

    <h1>{l s='Your Shopping Cart'}</h1>

    {block name="shopping_cart_summary"}
      {include 'checkout/_partials/shopping-cart-summary.tpl' cart=$cart}
    {/block}

  </section>
{/block}
