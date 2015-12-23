{extends "checkout/checkout-step.tpl"}

{block "step_content"}

  {if !$use_same_address}
    <h2 class="h2">{l s='Your Delivery Address'}</h2>
  {/if}

  {if $customer.addresses|count === 0 || $show_delivery_address_form}

    {form form              = $address_form
          template          = "checkout/_partials/address-form.tpl"
          use_same_address  = $use_same_address
          type              = "delivery"
    }

  {else if $customer.addresses|count > 0}

    {if $use_same_address}
      <p>
        {l s='The selected address will be used both as your personal address (for invoice) and as your delivery address.'}
      </p>
    {/if}

    {include  addresses  = $customer.addresses
              file       = "checkout/_partials/address-selector-block.tpl"
              name       = "id_address_delivery"
              selected   = $id_address_delivery
              type       = "delivery"
    }

    <a href="?newAddress=delivery">{l s='Add another address'}</a>

    {if $use_same_address}
      <p>
        <a href="?use_same_address=0">{l s='Use a different address for invoice?'}</a>
      </p>
    {/if}

  {/if}

  {if !$use_same_address || $show_invoice_address_form}

    <h2 class="h2">{l s='Your Invoice Address'}</h2>

    {if $customer.addresses|count < 2 || $show_invoice_address_form}

      {form form=$address_form template="checkout/_partials/address-form.tpl" type="invoice"}

    {else}

      {include  addresses  = $customer.addresses
                file       = "checkout/_partials/address-selector-block.tpl"
                name       = "id_address_invoice"
                selected   = $id_address_invoice
                type       = "invoice"
      }

      <a href="?newAddress=invoice">{l s='Add another address'}</a>

    {/if}

  {/if}

  {if $customer.addresses|count}
    <form>
      <button type="submit" class="continue" name="continue" value="1">
          {l s='Continue'}
      </button>
    </form>
  {/if}

{/block}
