{extends file="checkout/checkout-step.tpl"}

{block name="step_content"}

  {if !$use_same_address}
    <h2 class="h2">{l s="Your Delivery Address"}</h2>
  {/if}

  {if $use_same_address}
    <p>
      {l s="The selected address will be used both as your personal address (for invoice) and as your delivery address."}
    </p>
  {/if}

  {if $show_delivery_address_form}
    <div id="delivery-address">
      {render file                      = "checkout/_partials/address-form.tpl"
              ui                        = $address_form
              use_same_address          = $use_same_address
              type                      = "delivery"
              form_has_continue_button  = $form_has_continue_button
      }
    </div>
  {elseif $customer.addresses|count > 0}
    <div id="delivery-addresses">
      {include  file        = "checkout/_partials/address-selector-block.tpl"
                addresses   = $customer.addresses
                name        = "id_address_delivery"
                selected    = $id_address_delivery
                type        = "delivery"
                interactive = !$show_delivery_address_form and !$show_invoice_address_form
      }
    </div>

    <a href="?newAddress=delivery">{l s="Add another address"}</a>

    {if $use_same_address}
      <p>
        <a data-link-action="different-invoice-address" href="?use_same_address=0">{l s="Use a different address for invoice?"}</a>
      </p>
    {/if}

  {/if}

  {if !$use_same_address}

    <h2 class="h2">{l s="Your Invoice Address"}</h2>

    {if $show_invoice_address_form}
      <div id="invoice-address">
        {render file                      = "checkout/_partials/address-form.tpl"
                ui                        = $address_form
                use_same_address          = $use_same_address
                type                      = "invoice"
                form_has_continue_button  = $form_has_continue_button
        }
      </div>
    {else}
      <div id="invoice-addresses">
        {include  file        = "checkout/_partials/address-selector-block.tpl"
                  addresses   = $customer.addresses
                  name        = "id_address_invoice"
                  selected    = $id_address_invoice
                  type        = "invoice"
                  interactive = !$show_delivery_address_form and !$show_invoice_address_form
        }
      </div>

      <a href="?newAddress=invoice">{l s="Add another address"}</a>

    {/if}

  {/if}

  {if !$form_has_continue_button}
    <form>
      <button type="submit" class="continue" name="continue" value="1">
          {l s="Continue"}
      </button>
    </form>
  {/if}

{/block}
