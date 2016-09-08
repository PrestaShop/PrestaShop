{extends file='customer/page.tpl'}

{block name='page_title'}
  <h1 class="h1">{l s='Return details' d='Shop.Theme.CustomerAccount'}</h1>
{/block}

{block name='page_content'}
  {block name='order_return_infos'}
    <div id="order-return-infos" class="card">
      <div class="card-block">
        <p>
          <strong>{l
            s='%number% on %date%'
            d='Shop.Theme.CustomerAccount'
            sprintf=['%number%' => $return.return_number, '%date%' => $return.return_date]}
          </strong>
        </p>
        <p>{l s='We have logged your return request.' d='Shop.Theme.CustomerAccount'}</p>
        <p>{l
          s='Your package must be returned to us within %number% days of receiving your order.'
          d='Shop.Theme.CustomerAccount'
          sprintf=['%number%' => $configuration.number_of_days_for_return]}</p>
        <p>
          {* [1][/1] is for a HTML tag. *}
          {l
            s='The current status of your merchandise return is: [1] %status% [/1]'
            d='Shop.Theme.CustomerAccount'
            sprintf=[
              '[1]' => '<strong>',
              '[/1]' => '</strong>',
              '%status%' => $return.state_name
            ]
          }
        </p>
        <p>{l s='List of items to be returned:' d='Shop.Theme.CustomerAccount'}</p>
        <table class="table table-striped table-bordered">
          <thead class="thead-default">
            <tr>
              <th>{l s='Reference' d='Shop.Theme.Catalog'}</th>
              <th>{l s='Product' d='Shop.Theme.Catalog'}</th>
              <th>{l s='Quantity' d='Shop.Theme.Checkout'}</th>
            </tr>
          </thead>
          <tbody>
          {foreach from=$products item=product}
            <tr>
              <td>{$product.product_reference}</td>
              <td>{$product.product_name}
                {if $product.customizations}
                  <br />
                  {foreach $product.customizations as $customization}
                    <ul>
                      {foreach from=$customization.fields item=field}
                        {if $field.type == 'image'}
                          <li><img src="{$field.image.small.url}" alt=""></li>
                        {elseif $field.type == 'text'}
                          <li>
                            {$field.label} : {if (int)$field.id_module}{$field.text nofilter}{else}{$field.text}{/if}
                          </li>
                        {/if}
                      {/foreach}
                    </ul>
                  {/foreach}
                {/if}
              </td>
              <td>
                {if $product.customizations}
                  {$product.product_quantity}
                {else}
                  {foreach $product.customizations as $customization}
                    {$customization.quantity}
                  {/foreach}
                {/if}
              </td>
            </tr>
          {/foreach}
          </tbody>
        </table>
      </div>
    </div>
  {/block}

  {if $return.state == 2}
    <section class="card">
      <div class="card-block">
        <h3 class="card-title h3">{l s='Reminder' d='Shop.Theme.CustomerAccount'}</h3>
        <p class="card-text">
          {l
            s='All merchandise must be returned in its original packaging and in its original state.'
            d='Shop.Theme.CustomerAccount'
          }<br>
          {* [1][/1] is for a HTML tag. *}
          {l
            s='Please print out the [1]PDF return slip[/1] and include it with your package.'
            d='Shop.Theme.CustomerAccount'
            sprintf=[
              '[1]' => '<a href="'|cat:$return.print_url|cat:'">',
              '[/1]' => '</a>'
            ]
          }
          <br>
          {* [1][/1] is for a HTML tag. *}
          {l
            s='Please see the PDF return slip ([1]for the correct address[/1]).'
            d='Shop.Theme.CustomerAccount'
            sprintf=[
              '[1]' => '<a href="'|cat:$return.print_url|cat:'">',
              '[/1]' => '</a>'
            ]
          }
        </p>
        <p class="card-text">
          {l
            s='When we receive your package, we will notify you by email. We will then begin processing order reimbursement.'
            d='Shop.Theme.CustomerAccount'
          }<br>
          <a href="{$urls.pages.contact}">
            {l
              s='Please let us know if you have any questions.'
              d='Shop.Theme.CustomerAccount'
            }
          </a><br>
          {l
            s='If the conditions of return listed above are not respected, we reserve the right to refuse your package and/or reimbursement.'
            d='Shop.Theme.CustomerAccount'
          }
        </p>
      </div>
    </section>
  {/if}
{/block}
