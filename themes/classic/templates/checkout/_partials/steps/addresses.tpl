{**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{extends file='checkout/_partials/steps/checkout-step.tpl'}

{block name='step_content'}
  <div class="js-address-form">
    <form
      method="POST"
      action="{$urls.pages.order}"
      data-refresh-url="{url entity='order' params=['ajax' => 1, 'action' => 'addressForm']}"
    >

      {if !$use_same_address}
        <h2 class="h4">{l s='Shipping Address' d='Shop.Theme.Checkout'}</h2>
      {/if}

      {if $use_same_address && !$cart.is_virtual}
        <p>
          {l s='The selected address will be used both as your personal address (for invoice) and as your delivery address.' d='Shop.Theme.Checkout'}
        </p>
      {elseif $use_same_address && $cart.is_virtual}
        <p>
          {l s='The selected address will be used as your personal address (for invoice).' d='Shop.Theme.Checkout'}
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
          <a href="{$new_address_delivery_url}"><i class="material-icons">&#xE145;</i>{l s='add new address' d='Shop.Theme.Actions'}</a>
        </p>

        {if $use_same_address && !$cart.is_virtual}
          <p>
            <a data-link-action="different-invoice-address" href="{$use_different_address_url}">
              {l s='Billing address differs from shipping address' d='Shop.Theme.Checkout'}
            </a>
          </p>
        {/if}

      {/if}

      {if !$use_same_address}

        <h2 class="h4">{l s='Your Invoice Address' d='Shop.Theme.Checkout'}</h2>

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
            <a href="{$new_address_invoice_url}"><i class="material-icons">&#xE145;</i>{l s='add new address' d='Shop.Theme.Actions'}</a>
          </p>
        {/if}

      {/if}

      {if !$form_has_continue_button}
        <div class="clearfix">
          <button type="submit" class="btn btn-primary continue pull-xs-right" name="confirm-addresses" value="1">
              {l s='Continue' d='Shop.Theme.Actions'}
          </button>
        </div>
      {/if}

    </form>
  </div>
{/block}
