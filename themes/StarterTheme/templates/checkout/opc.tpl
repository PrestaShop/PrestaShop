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

      {block name="shopping_cart_summary_section"}
        {* include 'checkout/_partials/shopping-cart-summary.tpl' cart=$cart *}
      {/block}

      {block name="opc_address_section"}
        <section id="opc-addresses">

          <header>
            <h1 class="h3">{l s='Addresses'}</h1>
          </header>

          <div class="addresses-container">

            <article id="select-invoice-address" class="address-selector">
              {block name="opc_invoice_address"}
                {include file="checkout/_partials/address-selector-block.tpl" name="address_invoice"}
              {/block}
            </article>

            <article id="select-delivery-address" class="address-selector">
              {block name="opc_delivery_address"}
                {include file="checkout/_partials/address-selector-block.tpl" name="address_delivery"}
              {/block}
            </article>

          </div>

        </section>
      {/block}

      {block name="opc_delivery_section"}
        <section id="opc-delivery">

          <header>
            <h1 class="h3">{l s='Delivery options'}</h1>
          </header>

          <div class="delivery-option-list">

            {* StarterTheme: Delivery option list *}

          </div>

        </section>
      {/block}

      {block name="opc_legal_terms_section"}
        <section id="opc-legal-terms">

          <header>
            <h1 class="h3">{l s='Terms and conditions'}</h1>
          </header>

          <div>
            {block name="opc_legal_terms"}
              {include file="checkout/_partials/terms.tpl" overridden_terms=$override_tos_display}
            {/block}
          </div>

        </section>
      {/block}

      {block name="opc_payment_section"}
        <section id="opc-payment">

          <header>
            <h1 class="h3">{l s='Payment method'}</h1>
          </header>

          <div>
            {block name="opc_legal_terms"}
              {include file="checkout/_partials/terms.tpl" overridden_terms=$override_tos_display}
            {/block}
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
