<section id="addresses-section" data-checkout-step-status="{$status}">
  {block name="checkout_addresses"}

    <header>
      <h1 class="h3">{l s='Addresses'}</h1>
    </header>

    {if $customer.is_logged && count($customer.addresses) > 0}
      {block name="checkout_customer_addresses"}
        {include file="checkout/_partials/checkout-section-logged-addresses.tpl"
          selected_address_delivery=$cart.id_address_delivery
          selected_address_invoice=$cart.id_address_invoice}
      {/block}
    {else}
      {block name="checkout_address_forms"}
        {$address_form_delivery nofilter}
        {$address_form_invoice nofilter}
      {/block}
    {/if}

  {/block}
</div>
