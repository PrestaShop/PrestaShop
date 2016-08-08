{extends file='customer/page.tpl'}

{block name='page_title'}
  {l s='Merchandise returns' d='Shop.Theme.CustomerAccount'}
{/block}

{block name='page_content'}

  {if $ordersReturn && count($ordersReturn)}

    <h6>{l s='Here is a list of pending merchandise returns' d='Shop.Theme.CustomerAccount'}</h6>

    <table class="table table-striped table-bordered">
      <thead class="thead-default">
        <tr>
          <th>{l s='Return' d='Shop.Theme.CustomerAccount'}</th>
          <th>{l s='Order' d='Shop.Theme.CustomerAccount'}</th>
          <th>{l s='Package status' d='Shop.Theme.CustomerAccount'}</th>
          <th>{l s='Date issued' d='Shop.Theme.CustomerAccount'}</th>
          <th>{l s='Return slip' d='Shop.Theme.CustomerAccount'}</th>
        </tr>
      </thead>
      <tbody>
        {foreach from=$ordersReturn item=return}
          <tr>
            <td><a href="{$return.return_url}">{$return.return_number}</a></td>
            <td><a href="{$return.details_url}">{$return.reference}</a></td>
            <td>{$return.state_name}</td>
            <td>{$return.return_date}</td>
            <td class="text-xs-center">
              {if $return.print_url}
                <a href="{$return.print_url}">{l s='Print out' d='Shop.Theme.Actions'}</a>
              {else}
                -
              {/if}
            </td>
          </tr>
        {/foreach}
      </tbody>
    </table>

  {/if}

{/block}
