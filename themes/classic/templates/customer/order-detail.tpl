{extends file='customer/page.tpl'}

{block name='page_title'}
  {l s='Order details'}
{/block}

{block name='page_content_container'}
  <section id="content" class="page-content page-order page-order-details">
    {block name='order_infos'}
      <div id="order-infos">
        <div class="card">
          <div class="card-block">
            <strong>{l s='Order Reference %s - placed on %s' sprintf=[$order.data.reference, $order.data.order_date]}</strong>
            {if $order.data.url_to_reorder}
              <div class="pull-xs-right">
                <a href="{$order.data.url_to_reorder}" class="btn btn-primary">{l s='Reorder'}</a>
              </div>
              <div class="clearfix"></div>
            {/if}
          </div>
        </div>

        <div class="card">
          <div class="card-block">
            <ul>
            <li><strong>{l s='Carrier'}</strong> {$order.carrier.name}</li>
            <li><strong>{l s='Payment method'}</strong> {$order.data.payment}</li>

            {if $order.data.url_to_invoice}
              <li><a href="{$order.data.url_to_invoice}">{l s='Download your invoice as a PDF file.'}</a></li>
            {/if}

            {if $order.data.recyclable}
              <li>{l s='You have given permission to receive your order in recycled packaging.'}</li>
            {/if}

            {if $order.data.gift}
              <li>{l s='You have requested gift wrapping for this order.'}</li>
              <li>{l s='Message'} {$order.data.gift_message nofilter}</li>
            {/if}
            </ul>
          </div>
        </div>
      </div>
    {/block}

    {block name='order_history'}
      <section id="order-history">
        <h3>{l s='Follow your order\'s status step-by-step'}</h3>
        <table class="table table-striped table-bordered table-labeled">
          <thead class="thead-default">
            <tr>
              <th>{l s='Date'}</th>
              <th>{l s='Status'}</th>
            </tr>
          </thead>
          <tbody>
            {foreach from=$order.history item=state}
              <tr>
                <td>{$state.history_date}</td>
                <td><span class="label label-pill {$state.contrast}" style="background-color:{$state.color}">{$state.ostate_name}</span></td>
              </tr>
            {/foreach}
          </tbody>
        </table>
      </section>
    {/block}

    {if $order.data.followup}
      <p>{l s='Click the following link to track the delivery of your order'}</p>
      <a href="{$order.data.followup}">{$order.data.followup}</a>
    {/if}

    {block name='addresses'}
      {if $order.addresses.delivery}
        <div class="col-lg-6 col-md-6 col-sm-6">
          <article id="delivery-address" class="card address">
            <div class="card-header">
              {l s='Delivery address %s' sprintf=$order.addresses.delivery.alias}
            </div>
            <div class="card-block">
              <address>{$order.addresses.delivery.formatted nofilter}</address>
            </div>
          </article>
        </div>
      {/if}

      <div class="col-lg-6 col-md-6 col-sm-6">
        <article id="invoice-address" class="card address">
          <div class="card-header">
            {l s='Invoice address %s' sprintf=$order.addresses.invoice.alias}
          </div>
          <div class="card-block">
            <address>{$order.addresses.invoice.formatted nofilter}</address>
          </div>
        </article>
      </div>
      <div class="clearfix"></div>
    {/block}

    {$hook_orderdetaildisplayed}

    {block name='order_detail'}
      {if $order.data.return_allowed}
        {include file='customer/_partials/order-detail-return.tpl'}
      {else}
        {include file='customer/_partials/order-detail-no-return.tpl'}
      {/if}
    {/block}

    {block name='order_carriers'}
      {if $order.shipping}
        <table class="table table-striped table-bordered">
          <thead class="thead-default">
            <tr>
              <th>{l s='Date'}</th>
              <th>{l s='Carrier'}</th>
              <th>{l s='Weight'}</th>
              <th>{l s='Shipping cost'}</th>
              <th>{l s='Tracking number'}</th>
            </tr>
          </thead>
          <tbody>
            {foreach from=$order.shipping item=line}
              <tr>
                <td>{$line.shipping_date}</td>
                <td>{$line.carrier_name}</td>
                <td>{$line.shipping_weight}</td>
                <td class="text-xs-right">{$line.shipping_cost}</td>
                <td>{$line.tracking}</td>
              </tr>
            {/foreach}
          </tbody>
        </table>
      {/if}
    {/block}

    {block name='order_messages'}
      {include file='customer/_partials/order-messages.tpl'}
    {/block}
  </section>
{/block}
