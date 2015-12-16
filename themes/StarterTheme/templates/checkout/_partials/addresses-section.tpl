<section id="addresses-section" data-checkout-step-status="{$status}">
  <h1 class="h1">{l s='Addresses'}</h1>

  <section id="delivery-addresses">
    <h1 class="h2>">{l s='Your delivery address'}</h1>

    {include  addresses  = $customer.addresses
              file       = "checkout/_partials/address-selector-block.tpl"
              name       = "id_address_delivery"
              selected   = $id_address_delivery
              type       = "delivery"
    }
    <a href="?newAddress=delivery" data-link-action="new-address-delivery">
      {l s='Add another address'}
    </a>

    {if $ui_state === 'new delivery address' or $ui_state === 'edit delivery address'}
      <form action="{$urls.pages.order}" method="POST" id="checkout-address-delivery">
        {include  address         = $address
                  address_fields  = $address_fields
                  file            = "customer/_partials/address-form-fields.tpl"
                  countries       = $countries
        }
        <footer class="form-footer">
          <button name="saveAddress" value="delivery">{l s='Save'}</button>
        </footer>
      </form>
    {/if}
  </section>

  <p>{l s='Please click [1]here[/1] if you want to use a different address for billing.' tags=["<a data-link-action='setup-invoice-address' href='?setupInvoiceAddress'>"]}</p>

  {if $ui_state === 'new invoice address' or $ui_state === 'edit invoice address' or $ui_state === 'choose invoice address'}
    <section id="invoice-addresses">
      <h1 class="h2>">{l s='Your billing address'}</h1>

      {include  addresses  = $customer.addresses
                file       = "checkout/_partials/address-selector-block.tpl"
                name       = "id_address_invoice"
                selected   = $id_address_invoice
                type       = "invoice"
      }
      <a href="?newAddress=invoice" data-link-action="new-address-invoice">
        {l s='Add another address'}
      </a>

      {if $ui_state === 'new invoice address' or $ui_state === 'edit invoice address'}
        <form action="{$urls.pages.order}" method="POST" id="checkout-address-invoice">
          {include  address         = $address
                    address_fields  = $address_fields
                    file            = "customer/_partials/address-form-fields.tpl"
                    countries       = $countries
          }
          <footer class="form-footer">
            <button name="saveAddress" value="invoice">{l s='Save'}</button>
          </footer>
        </form>
      {/if}
    </section>
  {/if}

</section>
