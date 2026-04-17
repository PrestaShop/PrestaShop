{**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *}
{extends file='customer/page.tpl'}

{block name='page_title'}
  {l s='Order details' d='Shop.Theme.Customeraccount'}
{/block}

{block name='page_content'}
  {block name='order_infos'}
    <div class="order__details">
      <div class="order__header row align-items-end">
        <div class="order__header__left col-12 col-sm-6">
          <p class="order__reference">
            {l
                                                          s='Order Reference %reference% - placed on %date%'
                                                          d='Shop.Theme.Customeraccount'
                                                          sprintf=['%reference%' => $order.details.reference, '%date%' => $order.details.order_date]
                                                        }
          </p>

          <p class="order__carrier">
            {l s='Carrier: %carrierName%' d='Shop.Theme.Checkout' sprintf=['%carrierName%' => $order.carrier.name]}
          </p>

          <p class="order__payment">
            {l s='Payment method: %paymentMethod%' d='Shop.Theme.Checkout' sprintf=['%paymentMethod%' => $order.details.payment]}
          </p>

          {if $order.details.invoice_url}
            <a href="{$order.details.invoice_url}">
              {l s='Download your invoice as a PDF file.' d='Shop.Theme.Customeraccount'}
            </a>
          {/if}

          {if $order.details.recyclable}
            <p>
              {l s='You have given permission to receive your order in recycled packaging.' d='Shop.Theme.Customeraccount'}
            </p>
          {/if}

          {if $order.details.gift_message}
            <p>{l s='You have requested gift wrapping for this order.' d='Shop.Theme.Customeraccount'}</p>
            <p>{l s='Message' d='Shop.Theme.Customeraccount'} {$order.details.gift_message nofilter}</p>
          {/if}
        </div>

        {if $order.details.reorder_url}
          <div class="order__header__right col-12 col-sm-6">
            <a href="{$order.details.reorder_url}" class="btn btn-outline-primary">{l s='Reorder' d='Shop.Theme.Actions'}</a>
          </div>
        {/if}
      </div>
    </div>
  {/block}

  <hr>

  {block name='order_history'}
    <section id="order-history" class="box">
      <h2 class="h3">{l s='Follow your order\'s status step-by-step' d='Shop.Theme.Customeraccount'}</h2>

      <div class="table-wrapper overflow-auto">
        <table class="table">
          <thead class="thead-default">
            <tr>
              <th>{l s='Date' d='Shop.Theme.Global'}</th>
              <th>{l s='Status' d='Shop.Theme.Global'}</th>
            </tr>
          </thead>

          <tbody>
            {foreach from=$order.history item=state}
              <tr>
                <td>{$state.history_date}</td>
                <td>
                  <span class="badge {$state.contrast}" style="background-color:{$state.color}">
                    {$state.ostate_name}
                  </span>
                </td>
              </tr>
            {/foreach}
          </tbody>
        </table>
      </div>
    </section>
  {/block}

  {if $order.follow_up}
    <div class="box">
      <p>{l s='Click the following link to track the delivery of your order' d='Shop.Theme.Customeraccount'}</p>

      <a href="{$order.follow_up}">{$order.follow_up}</a>
    </div>
  {/if}

  <hr>

  {block name='addresses'}
    <h3 class="h3">{l s='Addresses' d='Shop.Theme.Customeraccount'}</h3>

    <div class="addresses row">
      {if $order.addresses.delivery}
        <div class="col-sm-6 mb-4">
          <article id="delivery-address" class="address card">
            <div class="card-body">
              <h4 class="address__alias h4 card-title">
                {l s='Delivery address: %alias%' d='Shop.Theme.Checkout' sprintf=['%alias%' => $order.addresses.delivery.alias]}
              </h4>
              <address class="address__content">{$order.addresses.delivery.formatted nofilter}</address>
            </div>
          </article>
        </div>
      {/if}

      <div class="col-sm-6">
        <article id="invoice-address" class="address card">
          <div class="card-body">
            <h4 class="address__alias h4 card-title">
              {l s='Invoice address: %alias%' d='Shop.Theme.Checkout' sprintf=['%alias%' => $order.addresses.invoice.alias]}
            </h4>
            <address class="address__content">{$order.addresses.invoice.formatted nofilter}</address>
          </div>
        </article>
      </div>
    </div>
  {/block}

  <hr>

  {$HOOK_DISPLAYORDERDETAIL nofilter}

  <div class="order__detail__products">
    <h3 class="h3">{l s='Products' d='Shop.Theme.Customeraccount'}</h3>

    {block name='order_detail'}
      {if $order.details.is_returnable}
        {include file='customer/_partials/order-detail-return.tpl'}
      {else}
        {include file='customer/_partials/order-detail-no-return.tpl'}
      {/if}
    {/block}
  </div>

  <hr>

  {block name='order_carriers'}
    {if $order.shipping}
      <h3 class="h3">{l s='Tracking' d='Shop.Theme.Customeraccount'}</h3>

      <div class="table-wrapper">
        <table class="table d-none d-sm-table d-md-table">
          <thead class="thead-default">
            <tr>
              <th>{l s='Date' d='Shop.Theme.Global'}</th>
              <th>{l s='Carrier' d='Shop.Theme.Checkout'}</th>
              <th>{l s='Weight' d='Shop.Theme.Checkout'}</th>
              <th>{l s='Shipping cost' d='Shop.Theme.Checkout'}</th>
              <th>{l s='Tracking number' d='Shop.Theme.Checkout'}</th>
            </tr>
          </thead>

          <tbody>
            {foreach from=$order.shipping item=line}
              <tr>
                <td>{$line.shipping_date}</td>
                <td>{$line.carrier_name}</td>
                <td>{$line.shipping_weight}</td>
                <td>{$line.shipping_cost}</td>
                <td>{$line.tracking nofilter}</td>
              </tr>
            {/foreach}
          </tbody>
        </table>
      </div>

      <div class="d-block d-sm-block d-md-none shipping-lines">
        {foreach from=$order.shipping item=line}
          <div class="table-wrapper py-2 my-2">
            <ul class="m-0">
              <li class="row">
                <p class="col fw-bold">{l s='Date' d='Shop.Theme.Global'}</p>
                <p class="col text-end">{$line.shipping_date}</p>
              </li>

              <li class="row">
                <p class="col fw-bold">{l s='Carrier' d='Shop.Theme.Checkout'}</p>
                <p class="col text-end">{$line.carrier_name}</p>
              </li>

              <li class="row">
                <p class="col fw-bold">{l s='Weight' d='Shop.Theme.Global'}</p>
                <p class="col text-end">{$line.shipping_weight}</p>
              </li>

              <li class="row">
                <p class="col fw-bold">{l s='Shipping cost' d='Shop.Theme.Global'}</p>
                <p class="col text-end">{$line.shipping_cost}</p>
              </li>

              <li class="row">
                <p class="col fw-bold m-0">{l s='Tracking number' d='Shop.Theme.Global'}</p>
                <p class="col text-end m-0">{$line.tracking nofilter}</p>
              </li>
            </ul>
          </div>
        {/foreach}
      </div>
    {/if}
  {/block}

  <hr>

  {block name='order_messages'}
    {include file='customer/_partials/order-messages.tpl'}
  {/block}
{/block}
