{extends file='customer/page.tpl'}

{block name='page_title'}
  {l s='Credit slips' d='Shop.Theme.CustomerAccount'}
{/block}

{block name='page_content'}
  <h6>{l s='Credit slips you have received after canceled orders' d='Shop.Theme.CustomerAccount'}.</h6>
  {if $credit_slips}
    <table class="table table-striped table-bordered">
      <thead class="thead-default">
        <tr>
          <th>{l s='Credit slip' d='Shop.Theme.CustomerAccount'}</th>
          <th>{l s='Order' d='Shop.Theme.CustomerAccount'}</th>
          <th>{l s='Date issued' d='Shop.Theme.CustomerAccount'}</th>
          <th>{l s='View credit slip' d='Shop.Theme.CustomerAccount'}</th>
        </tr>
      </thead>
      <tbody>
        {foreach from=$credit_slips item=slip}
          <tr>
            <th scope="row">{$slip.credit_slip_number}</th>
            <td><a href="{$slip.order_url_details}" data-link-action="view-order-details">{$slip.order_reference}</a></td>
            <td>{$slip.credit_slip_date}</td>
            <td class="text-xs-center">
              <a href="{$slip.url}"><i class="material-icons">&#xE415;</i></a>
            </td>
          </tr>
        {/foreach}
      </tbody>
    </table>
  {/if}
{/block}
