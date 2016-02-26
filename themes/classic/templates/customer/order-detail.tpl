{extends file='customer/page.tpl'}

{block name='page_title'}
  {l s='Order details'}
{/block}

{block name='page_content'}
  {block name='order_infos'}
    <div id="order-infos">
      <div class="box">
          <strong>{l s='Order Reference %s - placed on %s' sprintf=[$order.details.reference, $order.details.order_date]}</strong>
          {if $order.details.url_to_reorder}
            <div class="pull-xs-right">
              <a href="{$order.details.url_to_reorder}" class="button-primary">{l s='Reorder'}</a>
            </div>
            <div class="clearfix"></div>
          {/if}
      </div>

      <div class="box">
          <ul>
          <li><strong>{l s='Carrier'}</strong> {$order.carrier.name}</li>
          <li><strong>{l s='Payment method'}</strong> {$order.details.payment}</li>

          {if $order.details.url_to_invoice}
            <li><a href="{$order.details.url_to_invoice}">{l s='Download your invoice as a PDF file.'}</a></li>
          {/if}

          {if $order.details.recyclable}
            <li>{l s='You have given permission to receive your order in recycled packaging.'}</li>
          {/if}

          {if $order.details.gift_message}
            <li>{l s='You have requested gift wrapping for this order.'}</li>
            <li>{l s='Message'} {$order.details.gift_message nofilter}</li>
          {/if}
          </ul>
      </div>
    </div>
  {/block}

  {block name='order_history'}
    <section id="order-history" class="box">
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

  {if $order.follow_up}
    <div class="box">
      <p>{l s='Click the following link to track the delivery of your order'}</p>
      <a href="{$order.follow_up}">{$order.follow_up}</a>
    </div>
  {/if}

  {block name='addresses'}
    <div class="addresses">
      {if $order.addresses.delivery}
        <div class="col-lg-6 col-md-6 col-sm-6">
          <article id="delivery-address" class="box">
            <h4>{l s='Delivery address %s' sprintf=$order.addresses.delivery.alias}</h4>
            <address>{$order.addresses.delivery.formatted nofilter}</address>
          </article>
        </div>
      {/if}

      <div class="col-lg-6 col-md-6 col-sm-6">
        <article id="invoice-address" class="box">
          <h4>{l s='Invoice address %s' sprintf=$order.addresses.invoice.alias}</h4>
          <address>{$order.addresses.invoice.formatted nofilter}</address>
        </article>
      </div>
      <div class="clearfix"></div>
    </div>
  {/block}

  {$hook_orderdetaildisplayed}

  {block name='order_detail'}
    {if $order.details.return_allowed}
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
      </div>
    {/if}
  {/block}

  {block name='order_messages'}
    {include file='customer/_partials/order-messages.tpl'}
  {/block}
{/block}
