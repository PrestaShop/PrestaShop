{extends "customer/page.tpl"}

{block name="page_title"}
  {l s='Order history'}
{/block}

{block name="page_content"}
  <h2>{l s='Here are the orders you\'ve placed since your account was created.'}</h2>

  {if $orders}
    <table>
      <thead>
        <tr>
          <th>{l s='Order reference'}</th>
          <th>{l s='Date'}</th>
          <th>{l s='Total price'}</th>
          <th>{l s='Payment'}</th>
          <th>{l s='Status'}</th>
          <th>{l s='Invoice'}</th>
          <th>&nbsp;</th>
        </tr>
      </thead>
      <tbody>
        {foreach from=$orders item=order}
          <tr>
            <td>{$order.reference}</td>
            <td>{$order.order_date}</td>
            <td>{$order.total_price}</td>
            <td>{$order.payment}</td>
            <td>
              <span class="order-status-label {$order.contrast}" style="background-color:{$order.order_state_color}">{$order.order_state}</span>
            </td>
            <td>
              {if $order.url_to_invoice}
                <a href="{$order.url_to_invoice}" class="order-invoice-link">{l s='PDF'}</a>
              {else}
                -
              {/if}
            </td>
            <td>
              <a href="{$order.url_details}" class="order-detail-link">
                {l s='Details'}
              </a>
              {if $order.url_to_reorder}
                <a href="{$order.url_to_reorder}" class="order-reorder-link">{l s='Reorder'}</a>
              {/if}
            </td>
          </tr>
        {/foreach}
      </tbody>
    </table>
  {/if}

{/block}
