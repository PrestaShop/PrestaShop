{extends file='customer/page.tpl'}

{block name='page_title'}
  {l s='Order history'}
{/block}

{block name='page_content'}
  <h6>{l s='Here are the orders you\'ve placed since your account was created.'}</h6>

  {if $orders}
    <table class="table table-striped table-bordered table-labeled">
      <thead class="thead-default">
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
            <th scope="row">{$order.reference}</th>
            <td>{$order.order_date}</td>
            <td class="text-xs-right">{$order.total_price}</td>
            <td>{$order.payment}</td>
            <td>
              <span class="label label-pill {$order.contrast}" style="background-color:{$order.order_state_color}">{$order.order_state}</span>
            </td>
            <td class="text-xs-center">
              {if $order.url_to_invoice}
                <a href="{$order.url_to_invoice}"><i class="material-icons">&#xE415;</i></a>
              {else}
                -
              {/if}
            </td>
            <td class="text-xs-center order-actions">
              <a href="{$order.url_details}" data-link-action="view-order-details">
                {l s='Details'}
              </a>
              {if $order.url_to_reorder}
                <a href="{$order.url_to_reorder}">{l s='Reorder'}</a>
              {/if}
            </td>
          </tr>
        {/foreach}
      </tbody>
    </table>
  {/if}
{/block}
