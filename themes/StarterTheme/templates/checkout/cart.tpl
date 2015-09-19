{extends "layout.tpl"}

{block name="content"}
  <section id="main">
    <h1>{l s='Your Shopping Cart'}</h1>
    {include './_partials/shopping-cart-summary.tpl' cart=$cart}
  </section>
{/block}
