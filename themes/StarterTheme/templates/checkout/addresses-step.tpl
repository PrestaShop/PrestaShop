{extends "checkout/checkout-step.tpl"}

{block "step_content"}

  {if !$use_same_address}
    <h2 class="h2">{l s='Your Delivery Address'}</h2>
  {/if}

  {if $use_same_address}
    <p>
      {l s='The selected address will be used both as your personal address (for invoice) and as your delivery address.'}
    </p>
  {/if}

  {if $show_delivery_address_form}
    <div id="delivery-address">
      {form form                      = $address_form
            template                  = "checkout/_partials/address-form.tpl"
            use_same_address          = $use_same_address
            type                      = "delivery"
            form_has_continue_button  = $form_has_continue_button
      }
    </div>
  {elseif $customer.addresses|count > 0}
    <div id="delivery-addresses">
      {include  addresses  = $customer.addresses
                file       = "checkout/_partials/address-selector-block.tpl"
                name       = "id_address_delivery"
                selected   = $id_address_delivery
                type       = "delivery"
      }
    </div>

    <a href="?newAddress=delivery">{l s='Add another address'}</a>

    {if $use_same_address}
      <p>
        <a data-link-action="different-invoice-address" href="?use_same_address=0">{l s='Use a different address for invoice?'}</a>
      </p>
    {/if}

  {/if}

  {if !$use_same_address}

    <h2 class="h2">{l s='Your Invoice Address'}</h2>

    {if $show_invoice_address_form}
      <div id="invoice-address">
        {form form                      = $address_form
              template                  = "checkout/_partials/address-form.tpl"
              use_same_address          = $use_same_address
              type                      = "invoice"
              form_has_continue_button  = $form_has_continue_button
        }
      </div>
    {else}
      <div id="invoice-addresses">
        {include  addresses  = $customer.addresses
                  file       = "checkout/_partials/address-selector-block.tpl"
                  name       = "id_address_invoice"
                  selected   = $id_address_invoice
                  type       = "invoice"
        }
      </div>

      <a href="?newAddress=invoice">{l s='Add another address'}</a>

    {/if}

  {/if}

  {if !$form_has_continue_button}
    <form>
      <button type="submit" class="continue" name="continue" value="1">
          {l s='Continue'}
      </button>
    </form>
  {/if}

{/block}
