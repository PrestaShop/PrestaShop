{extends file='checkout/checkout-step.tpl'}

{block name='step_content'}
<form method="POST" action="{$urls.pages.order}">

  {if !$use_same_address}
    <h2 class="h4">{l s='Your Delivery Address'}</h2>
  {/if}

  {if $use_same_address}
    <p>
      {l s='The selected address will be used both as your personal address (for invoice) and as your delivery address.'}
    </p>
  {/if}

  {if $show_delivery_address_form}
    <div id="delivery-address">
      {render file                      = 'checkout/_partials/address-form.tpl'
              ui                        = $address_form
              use_same_address          = $use_same_address
              type                      = "delivery"
              form_has_continue_button  = $form_has_continue_button
      }
    </div>
  {elseif $customer.addresses|count > 0}
    <div id="delivery-addresses" class="address-selector js-address-selector">
      {include  file        = 'checkout/_partials/address-selector-block.tpl'
                addresses   = $customer.addresses
                name        = "id_address_delivery"
                selected    = $id_address_delivery
                type        = "delivery"
                interactive = !$show_delivery_address_form and !$show_invoice_address_form
      }
    </div>

    <p class="add-address">
      <a href="{$new_address_delivery_url}"><i class="material-icons">&#xE145;</i>{l s='add new address'}</a>
    </p>

    {if $use_same_address}
      <p>
        <a data-link-action="different-invoice-address" href="{$use_different_address_url}">{l s='Use a different address for invoice?'}</a>
      </p>
    {/if}

  {/if}

  {if !$use_same_address}

    <h2 class="h4">{l s='Your Invoice Address'}</h2>

    {if $show_invoice_address_form}
      <div id="invoice-address">
        {render file                      = 'checkout/_partials/address-form.tpl'
                ui                        = $address_form
                use_same_address          = $use_same_address
                type                      = "invoice"
                form_has_continue_button  = $form_has_continue_button
        }
      </div>
    {else}
      <div id="invoice-addresses" class="address-selector js-address-selector">
        {include  file        = 'checkout/_partials/address-selector-block.tpl'
                  addresses   = $customer.addresses
                  name        = "id_address_invoice"
                  selected    = $id_address_invoice
                  type        = "invoice"
                  interactive = !$show_delivery_address_form and !$show_invoice_address_form
        }
      </div>

      <p class="add-address">
        <a href="{$new_address_invoice_url}"><i class="material-icons">&#xE145;</i>{l s='add new address'}</a>
      </p>
    {/if}

  {/if}

  {if !$form_has_continue_button}
    <div class="clearfix">
      <button type="submit" class="btn btn-primary continue pull-xs-right" name="confirm-addresses" value="1">
          {l s='Continue'}
      </button>
    </div>
  {/if}

</form>
{/block}
