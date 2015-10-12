{extends "page.tpl"}

{block name="page_title"}
  {l s='Order details'}
{/block}

{block name="page_content"}

  {block name="order_infos"}
    <div id="order-infos">
      <p>{l s='Order Reference %s - placed on %s' sprintf=[$order.reference, $order.order_date]}</p>
      {if $order.url_to_reorder}
        <a href="{$order.url_to_reorder}" title="{l s='Reorder'}">{l s='Reorder'}</a>
      {/if}

      <p>{l s='Carrier'} {$carrier.name}</p>
      <p>{l s='Payment method'} {$order.payment}</p>

      {if $order.url_to_invoice}
        <p><a href="{$order.url_to_invoice}" title="{l s='Invoice'}">{l s='Download your invoice as a PDF file.'}</a></p>
      {/if}

      {if $order.recyclable}
        <p>{l s='You have given permission to receive your order in recycled packaging.'}</p>
      {/if}

      {if $order.gift}
        <p>{l s='You have requested gift wrapping for this order.'}</p>
        <p>{l s='Message'} {$order.gift_message nofilter}</p>
      {/if}
    </div>
  {/block}

  {block name="order_history"}
    <section id="order-history">
      <h1>{l s='Follow your order\'s status step-by-step'}</h1>
      <table>
        <thead>
          <tr>
            <th>{l s='Date'}</th>
            <th>{l s='Status'}</th>
          </tr>
        </thead>
        <tbody>
          {foreach from=$order_history item=state}
            <tr>
              <td>{$state.history_date}</td>
              <td><span class="order-status-label {$state.contrast}" style="background-color:{$state.color}">{$state.ostate_name}</span></td>
            </tr>
          {/foreach}
        </tbody>
      </table>
    </section>
  {/block}

  {if $followup}
    <p>{l s='Click the following link to track the delivery of your order'}</p>
    <a href="{$followup}">{$followup}</a>
  {/if}

  {block name="addresses"}
    {if $addresses.delivery}
      <article id="address-{$addresses.delivery.id}" class="address">
        <header>
          <h1 class="h4">{l s='Delivery address %s' sprintf=$addresses.delivery.alias}</h1>
        </header>

        <p>{$addresses.delivery.formatted nofilter}</p>
      </article>
    {/if}

    <article id="address-{$addresses.invoice.id}" class="address">
      <header>
        <h1 class="h4">{l s='Invoice address %s' sprintf=$addresses.invoice.alias}</h1>
      </header>

      <p>{$addresses.invoice.formatted nofilter}</p>
    </article>
  {/block}

  {$hook_orderdetaildisplayed}

  {block name="order_detail"}
    {if $return_allowed}
      {include file='customer/_partials/order-detail-return.tpl'}
    {else}
      {include file='customer/_partials/order-detail-no-return.tpl'}
    {/if}
  {/block}

  {block name="order_carriers"}
    {if $shipping}
      <table>
        <thead>
          <tr>
            <th>{l s='Date'}</th>
            <th>{l s='Carrier'}</th>
            <th>{l s='Weight'}</th>
            <th>{l s='Shipping cost'}</th>
            <th>{l s='Tracking number'}</th>
          </tr>
        </thead>
        <tbody>
          {foreach from=$shipping item=line}
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
    {/if}
  {/block}

  {block name="order_messages"}
    {include file='customer/_partials/order-messages.tpl'}
  {/block}

{/block}
