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

    {block name="content"}

      {block name="opc_address"}
        <section id="opc-addresses">
          <header>
            <h1 class="h3">{l s='Addresses'}</h1>
          </header>

          {* StarterTheme: Create new address (ajax) *}

          <div class="addresses-container">

            <article id="select-delivery-address" class="address-selector">
              {include file="checkout/_partials/address-selector-block.tpl"}
            </article>

          </div>

        </section>
      {/block}

    {/block}

    <footer id="footer">
      {block name="footer"}
        {include file="checkout/_partials/footer.tpl"}
      {/block}
    </footer>

  </body>

</html>
