{extends file='customer/page.tpl'}

{block name='page_title'}
  {l s='Order details' d='Shop.Theme.CustomerAccount'}
{/block}

{block name='page_content'}
  {block name='order_infos'}
    <div id="order-infos">
      <div class="box">
          <strong>
            {l
              s='Order Reference %reference% - placed on %date%'
              d='Shop.Theme.CustomerAccount'
              sprintf=['%reference%' => $order.details.reference, '%date%' => $order.details.order_date]
            }
          </strong>
          {if $order.details.reorder_url}
            <div class="pull-xs-right">
              <a href="{$order.details.reorder_url}" class="button-primary">{l s='Reorder' d='Shop.Theme.Actions'}</a>
            </div>
            <div class="clearfix"></div>
          {/if}
      </div>

      <div class="box">
          <ul>
          <li><strong>{l s='Carrier' d='Shop.Theme.Checkout'}</strong> {$order.carrier.name}</li>
          <li><strong>{l s='Payment method' d='Shop.Theme.Checkout'}</strong> {$order.details.payment}</li>

          {if $order.details.invoice_url}
            <li>
              <a href="{$order.details.invoice_url}">
                {l s='Download your invoice as a PDF file.' d='Shop.Theme.CustomerAccount'}
              </a>
            </li>
          {/if}

          {if $order.details.recyclable}
            <li>
              {l s='You have given permission to receive your order in recycled packaging.' d='Shop.Theme.CustomerAccount'}
            </li>
          {/if}

          {if $order.details.gift_message}
            <li>{l s='You have requested gift wrapping for this order.' d='Shop.Theme.CustomerAccount'}</li>
            <li>{l s='Message' d='Shop.Theme.CustomerAccount'} {$order.details.gift_message nofilter}</li>
          {/if}
          </ul>
      </div>
    </div>
  {/block}

  {block name='order_history'}
    <section id="order-history" class="box">
      <h3>{l s='Follow your order\'s status step-by-step' d='Shop.Theme.CustomerAccount'}</h3>
      <table class="table table-striped table-bordered table-labeled">
        <thead class="thead-default">
          <tr>
            <th>{l s='Date' d='Shop.Theme'}</th>
            <th>{l s='Status' d='Shop.Theme'}</th>
          </tr>
        </thead>
        <tbody>
          {foreach from=$order.history item=state}
            <tr>
              <td>{$state.history_date}</td>
              <td>
                <span class="label label-pill {$state.contrast}" style="background-color:{$state.color}">
                  {$state.ostate_name}
                </span>
              </td>
            </tr>
          {/foreach}
        </tbody>
      </table>
    </section>
  {/block}

  {if $order.follow_up}
    <div class="box">
      <p>{l s='Click the following link to track the delivery of your order' d='Shop.Theme.CustomerAccount'}</p>
      <a href="{$order.follow_up}">{$order.follow_up}</a>
    </div>
  {/if}

  {block name='addresses'}
    <div class="addresses">
      {if $order.addresses.delivery}
        <div class="col-lg-6 col-md-6 col-sm-6">
          <article id="delivery-address" class="box">
            <h4>{l s='Delivery address %alias%' d='Shop.Theme.Checkout' sprintf=['%alias%' => $order.addresses.delivery.alias]}</h4>
            <address>{$order.addresses.delivery.formatted nofilter}</address>
          </article>
        </div>
      {/if}

      <div class="col-lg-6 col-md-6 col-sm-6">
        <article id="invoice-address" class="box">
          <h4>{l s='Invoice address %alias%' d='Shop.Theme.Checkout' sprintf=['%alias%' => $order.addresses.invoice.alias]}</h4>
          <address>{$order.addresses.invoice.formatted nofilter}</address>
        </article>
      </div>
      <div class="clearfix"></div>
    </div>
  {/block}

  {$hook_orderdetaildisplayed}

  {block name='order_detail'}
    {if $order.details.is_returnable}
      {include file='customer/_partials/order-detail-return.tpl'}
    {else}
      {include file='customer/_partials/order-detail-no-return.tpl'}
    {/if}
  {/block}

  {block name='order_carriers'}
    {if $order.shipping}
      <div class="box">
        <table class="table table-striped table-bordered">
          <thead class="thead-default">
            <tr>
              <th>{l s='Date' d='Shop.Theme'}</th>
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
                <td>{$line.tracking}</td>
              </tr>
            {/foreach}
          </tbody>
        </table>
      </div>
    {/if}
  {/block}

  {block name='order_messages'}
    {include file='customer/_partials/order-messages.tpl'}
  {/block}
{/block}
