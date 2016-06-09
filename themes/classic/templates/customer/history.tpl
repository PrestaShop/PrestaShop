{extends file='customer/page.tpl'}

{block name='page_title'}
  {l s='Order history' d='Shop.Theme.CustomerAccount'}
{/block}

{block name='page_content'}
  <h6>{l s='Here are the orders you\'ve placed since your account was created.' d='Shop.Theme.CustomerAccount'}</h6>

  {if $orders}
    <table class="table table-striped table-bordered table-labeled">
      <thead class="thead-default">
        <tr>
          <th>{l s='Order reference' d='Shop.Theme.Checkout'}</th>
          <th>{l s='Date' d='Shop.Theme.Checkout'}</th>
          <th>{l s='Total price' d='Shop.Theme.Checkout'}</th>
          <th>{l s='Payment' d='Shop.Theme.Checkout'}</th>
          <th>{l s='Status' d='Shop.Theme.Checkout'}</th>
          <th>{l s='Invoice' d='Shop.Theme.Checkout'}</th>
          <th>&nbsp;</th>
        </tr>
      </thead>
      <tbody>
        {foreach from=$orders item=order}
          <tr>
            <th scope="row">{$order.details.reference}</th>
            <td>{$order.details.order_date}</td>
            <td class="text-xs-right">{$order.totals.total.value}</td>
            <td>{$order.details.payment}</td>
            <td>
              <span
                class="label label-pill {$order.history.current.contrast}"
                style="background-color:{$order.history.current.color}"
              >
                {$order.history.current.ostate_name}
              </span>
            </td>
            <td class="text-xs-center">
              {if $order.details.invoice_url}
                <a href="{$order.details.invoice_url}"><i class="material-icons">&#xE415;</i></a>
              {else}
                -
              {/if}
            </td>
            <td class="text-xs-center order-actions">
              <a href="{$order.details.details_url}" data-link-action="view-order-details">
                {l s='Details' d='Shop.Theme.CustomerAccount'}
              </a>
              {if $order.details.reorder_url}
                <a href="{$order.details.reorder_url}">{l s='Reorder' d='Shop.Theme.Action'}</a>
              {/if}
            </td>
          </tr>
        {/foreach}
      </tbody>
    </table>
  {/if}
{/block}
