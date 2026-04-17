{**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *}

{extends file=$layout}
{$componentName = 'order-confirmation'}

{block name='content'}

  {block name='order_confirmation_header'}
    <div class="alert alert-success" role="alert">
      <h1 class="h4 alert-heading">{l s='Your order is confirmed' d='Shop.Theme.Checkout'}</h1>
      <p class="mb-0">
        {l s='An email has been sent to your mail address %email%.' d='Shop.Theme.Checkout' sprintf=['%email%' => $order_customer.email]}
      </p>
      {if $order.details.invoice_url}
        <hr class="alert-divider"/>
        {l
          s='You can also [1]download your invoice[/1].'
          d='Shop.Theme.Checkout'
          sprintf=[
            '[1]' => "<a href='{$order.details.invoice_url}' class='alert-link'>",
            '[/1]' => "</a>"
          ]
        }
      {/if}
    </div>
  {/block}

  {block name='hook_payment_return'}
    {if !empty($HOOK_PAYMENT_RETURN)}
      {$HOOK_PAYMENT_RETURN nofilter}
    {/if}
  {/block}

  {block name='hook_order_confirmation'}
    {$HOOK_ORDER_CONFIRMATION nofilter}
  {/block}

  {block name='hook_order_confirmation_1'}
    {hook h='displayOrderConfirmation1'}
  {/block}

  {block name='order_details'}
    <div class="{$componentName}__details card bg-light border-1 mb-3">
      <div class="card-body">
        <h2 class="h4">{l s='Order details' d='Shop.Theme.Checkout'}</h2>
        <ul class="order-details">
          <li>{l s='Order reference: %reference%' d='Shop.Theme.Checkout' sprintf=['%reference%' => $order.details.reference]}</li>
          <li>{l s='Payment method: %method%' d='Shop.Theme.Checkout' sprintf=['%method%' => $order.details.payment]}</li>
          {if !$order.details.is_virtual}
            <li>{l s='Shipping method: %method%' d='Shop.Theme.Checkout' sprintf=['%method%' => $order.carrier.name]} - {$order.carrier.delay}</li>
          {/if}
        </ul>
        <hr>
        {block name='order_confirmation_table'}
          {include
                  file='checkout/_partials/order-confirmation-table.tpl'
                  products=$order.products
                  subtotals=$order.subtotals
                  totals=$order.totals
                  labels=$order.labels
                  add_product_link=false
                }
        {/block}
      </div>
    </div>
  {/block}

  {if !$registered_customer_exists}
    {block name='account_transformation_form'}
      <div class="card card-body bg-light mb-3 {$componentName}__account-transformation">
        {include file='customer/_partials/account-transformation-form.tpl'}
      </div>
    {/block}
  {/if}

  {block name='hook_order_confirmation_2'}
    {hook h='displayOrderConfirmation2'}
  {/block}

{/block}
