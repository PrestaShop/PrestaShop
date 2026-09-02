{**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *}
{extends file='customer/page.tpl'}

{block name='page_title'}
  {l s='Merchandise returns' d='Shop.Theme.Customeraccount'}
{/block}

{block name='page_content'}
  <p class="h6">{l s='Here is a list of pending merchandise returns' d='Shop.Theme.Customeraccount'}</p>

  {if $ordersReturn && count($ordersReturn)}
    <div class="table-wrapper">
      <table class="table table-striped d-none d-xl-table">
        <thead class="thead-default">
          <tr>
            <th>{l s='Order' d='Shop.Theme.Customeraccount'}</th>
            <th>{l s='Return' d='Shop.Theme.Customeraccount'}</th>
            <th>{l s='Package status' d='Shop.Theme.Customeraccount'}</th>
            <th>{l s='Date issued' d='Shop.Theme.Customeraccount'}</th>
            <th>{l s='Returns form' d='Shop.Theme.Customeraccount'}</th>
          </tr>
        </thead>

        <tbody>
          {foreach from=$ordersReturn item=return}
            <tr>
              <td><a href="{$return.details_url}">{$return.reference}</a></td>
              <td><a href="{$return.return_url}">{$return.return_number}</a></td>
              <td>{$return.state_name}</td>
              <td>{$return.return_date}</td>
              <td class="text-sm-center">
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
    </div>

    <div class="order-returns row d-block d-xl-none">
      {foreach from=$ordersReturn item=return}
        <div class="order-return table-wrapper col col-lg-6">
          <div class="order-return__reference">
            <p class="order-return__label">{l s='Order' d='Shop.Theme.Customeraccount'}</p> 
            <p class="order-return__value">
              <a href="{$return.details_url}">
                {$return.reference}
              </a>
            </p>
          </div>

          <div class="order-return__return">
            <p class="order-return__label">{l s='Return' d='Shop.Theme.Customeraccount'}</p> 
            <p class="order-return__value">
              <a href="{$return.return_url}">
                {$return.return_number}
              </a>
            </p>
          </div>

          <div class="order-return__status">
            <p class="order-return__label">{l s='Package status' d='Shop.Theme.Customeraccount'}</p> 
            <p class="order-return__value"><span class="badge">{$return.state_name}</span></p>
          </div>

          <div class="order-return__date">
            <p class="order-return__label">{l s='Date issued' d='Shop.Theme.Customeraccount'}</p> 
            <p class="order-return__value">{$return.return_date}</p>
          </div>

          {if $return.print_url}
            <div class="order-return__print">
              <p class="order-return__label">{l s='Returns form' d='Shop.Theme.Customeraccount'}</p> 
              <p class="order-return__value">
                <a href="{$return.print_url}">
                  {l s='Print out' d='Shop.Theme.Actions'}
                </a>
              </p>
            </div>
          {/if}
        </div>
      {/foreach}
    </div>
    {else}
      <div class="alert alert-info" role="alert" data-alert="info">{l s='You have no merchandise return authorizations.' d='Shop.Notifications.Error'}</div>
    {/if}
{/block}
