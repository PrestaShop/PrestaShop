<section id="addresses-section" data-checkout-step-status="{$status}">

  <header>
    <h1 class="h1">{l s='Addresses'}</h1>
  </header>

  <form action  = {$urls.pages.order}
        id      = "delivery-addresses"
        method  = "POST"
  >

    <input  name  = "token"
            type  = "hidden"
            value = "{$static_token}"
    >

    <h2 class="h2">{l s='Your delivery address'}</h2>

    {if $customer.addresses|count and !$new_delivery_address}

      <h2 class="h3">{l s='Choose from your existing addresses'}</h2>

      {include  file       = "checkout/_partials/address-selector-block.tpl"
                name       = "id_address_delivery"
                addresses  = $customer.addresses
                selected   = $id_address_delivery
      }

      <a  data-link-action  = "new-address-delivery"
          href              = "?newAddress=delivery"
      >
        {l s='Add an address'}
      </a>

    {else}

      <h2 class="h3">{l s='Create a new address'}</h2>
      {$address_form_delivery nofilter}

    {/if}

    <label class  = "ps-shown-by-js"
           for    = "checkout-different-address-for-invoice"
    >

      <input  type   = "checkbox"
              name   = "checkout-different-address-for-invoice"
              id     = "checkout-different-address-for-invoice"
              data-action        = "hideOrShow"
              data-action-target = "invoice-addresses"
              {if $checkout_different_address_for_invoice}
                checked
              {/if}
      >

      {l s='Use a different address for invoice'}

    </label>
  </form>

  <form action  = {$urls.pages.order}
        id      = "invoice-addresses"
        method  = "POST"
        {if !$checkout_different_address_for_invoice}
          class="ps-hidden-by-js"
        {/if}
  >
    <input  name  = "token"
            type  = "hidden"
            value = "{$static_token}"
    >

      <h2 class="h2">{l s='Your invoice address'}</h2>

      {if $customer.addresses|count and !$new_invoice_address}
        <h2 class="h3">{l s='Choose from your existing addresses'}</h2>

        {include
          file      = "checkout/_partials/address-selector-block.tpl"
          name      = "id_address_invoice"
          addresses = $customer.addresses
          selected  = $id_address_invoice
        }

        <a  data-link-action  = "new-address-invoice"
            href              = "?newAddress=invoice"
        >
          {l s='Add an address'}
        </a>

      {else}

        <h2 class="h3">{l s='Create a new address'}</h2>
        {$address_form_invoice nofilter}

      {/if}
  </form>
</div>
