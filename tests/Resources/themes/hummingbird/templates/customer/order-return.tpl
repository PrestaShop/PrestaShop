{**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *}
{extends file='customer/page.tpl'}

{block name='page_title'}
  {l s='Return details' d='Shop.Theme.Customeraccount'}
{/block}

{block name='page_content'}
  {block name='order_return_infos'}
    <div id="order-return-infos" class="card">
      <div class="card-block">
        <p>
          <strong>{l
            s='%number% on %date%'
            d='Shop.Theme.Customeraccount'
            sprintf=['%number%' => $return.return_number, '%date%' => $return.return_date]}
          </strong>
        </p>
        <p>{l s='We have logged your return request.' d='Shop.Theme.Customeraccount'}</p>
        <hr>
        <h2 class="h3">{l s='List of items to be returned:' d='Shop.Theme.Customeraccount'}</h2>
        <div class="table-wrapper">
          <table class="table d-none d-sm-table d-md-table">
            <thead class="thead-default">
              <tr>
                <th>{l s='Product' d='Shop.Theme.Catalog'}</th>
                <th>{l s='Quantity' d='Shop.Theme.Checkout'}</th>
              </tr>
            </thead>
            <tbody>
            {foreach from=$products item=product}
              <tr>
                <td>
                  <strong>{$product.product_name}</strong>
                  {if $product.product_reference}
                    <br />
                    {l s='Reference' d='Shop.Theme.Catalog'}: {$product.product_reference}
                  {/if}
                  {if $product.customizations}
                    {foreach from=$product.customizations item="customization"}
                      <div class="customization">
                        <a href="#" data-bs-toggle="modal" class="text-decoration-underline"
                        data-bs-target="#product-customizations-modal-{$customization.id_customization}">{l s='Product customization' d='Shop.Theme.Catalog'}</a>
                      </div>
                      {include file='catalog/_partials/customization-modal.tpl' customization=$customization}
                    {/foreach}
                  {/if}
                </td>
                <td>
                  {$product.product_quantity}
                </td>
              </tr>
            {/foreach}
            </tbody>
          </table>
        </div>
        <div class="d-block d-sm-block d-md-none">
          {foreach from=$products item=product}
            <div class="table-wrapper py-2 my-2">
              <ul class="m-0">
                <li class="row">
                  <p class="col fw-bold">{l s='Product' d='Shop.Theme.Catalog'}</p>
                  <p class="col-8 text-end">{$product.product_name}</p>
                </li>
                {if $product.product_reference}
                  <li class="row">
                    <p class="col fw-bold">{l s='Reference' d='Shop.Theme.Catalog'}</p>
                    <p class="col text-end">{$product.product_reference}</p>
                  </li>
                {/if}
                <li class="row">
                  <p class="col fw-bold">{l s='Quantity' d='Shop.Theme.Checkout'}</p>
                  <p class="col text-end">{$product.product_quantity}</p>
                </li>
                <li>
                  {if $product.customizations}
                    {foreach $product.customizations as $customization}
                      <div class="customization text-end">
                        <a href="#" data-bs-toggle="modal" class="text-decoration-underline"
                          data-bs-target="#product-customizations-modal-{$customization.id_customization}">{l s='Product customization' d='Shop.Theme.Catalog'}</a>
                      </div>
                      <div id="_mobile_product_customization_modal_wrapper_{$customization.id_customization}">
                      </div>
                    {/foreach}
                  {/if}
                </li>
              </ul>
            </div>
          {/foreach}
        </div>
        <hr>
        <p>{l
          s='Your package must be returned to us within %number% days of receiving your order.'
          d='Shop.Theme.Customeraccount'
          sprintf=['%number%' => $configuration.number_of_days_for_return]}</p>
        <p class="mb-0">
          {* [1][/1] is for a HTML tag. *}
          {l
            s='The current status of your merchandise return is: [1] %status% [/1]'
            d='Shop.Theme.Customeraccount'
            sprintf=[
              '[1]' => '<strong>',
              '[/1]' => '</strong>',
              '%status%' => $return.state_name
            ]
          }
        </p>
      </div>
    </div>
  {/block}

  {if $return.state == 2}
    <hr>
    <section class="card">
      <div class="card-block">
        <h3 class="card-title h3">{l s='Reminder' d='Shop.Theme.Customeraccount'}</h3>
        <p class="card-text">
          {l
            s='All merchandise must be returned in its original packaging and in its original state.'
            d='Shop.Theme.Customeraccount'
          }<br>
          {* [1][/1] is for a HTML tag. *}
          {l
            s='Please print out the [1]returns form[/1] and include it with your package.'
            d='Shop.Theme.Customeraccount'
            sprintf=[
              '[1]' => '<a href="'|cat:$return.print_url|cat:'">',
              '[/1]' => '</a>'
            ]
          }
          <br>
          {* [1][/1] is for a HTML tag. *}
          {l
            s='Please check the [1]returns form[/1] for the correct address.'
            d='Shop.Theme.Customeraccount'
            sprintf=[
              '[1]' => '<a href="'|cat:$return.print_url|cat:'">',
              '[/1]' => '</a>'
            ]
          }
        </p>
        <p class="card-text">
          {l
            s='When we receive your package, we will notify you by email. We will then begin processing order reimbursement.'
            d='Shop.Theme.Customeraccount'
          }<br>
          <a href="{$urls.pages.contact}">
            {l
              s='Please let us know if you have any questions.'
              d='Shop.Theme.Customeraccount'
            }
          </a><br>
          {l
            s='If the conditions of return listed above are not respected, we reserve the right to refuse your package and/or reimbursement.'
            d='Shop.Theme.Customeraccount'
          }
        </p>
      </div>
    </section>
  {/if}
{/block}
