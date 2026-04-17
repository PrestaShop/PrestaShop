{**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *}
{extends 'customer/page.tpl'}

{block name='page_title'}
  {l s='Credit slips' d='Shop.Theme.Customeraccount'}
{/block}

{block name='page_content'}
  <p class="h6">{l s='Credit slips you have received after canceled orders.' d='Shop.Theme.Customeraccount'}</p>

  {if $credit_slips}
    <div class="table-wrapper">
      <table class="table table-striped d-none d-xl-table">
        <thead class="thead-default">
          <tr>
            <th>{l s='Order' d='Shop.Theme.Customeraccount'}</th>
            <th>{l s='Credit slip' d='Shop.Theme.Customeraccount'}</th>
            <th>{l s='Date issued' d='Shop.Theme.Customeraccount'}</th>
            <th>{l s='View credit slip' d='Shop.Theme.Customeraccount'}</th>
          </tr>
        </thead>

        <tbody>
          {foreach from=$credit_slips item=slip}
            <tr>
              <td><a href="{$slip.order_url_details}" data-link-action="view-order-details">{$slip.order_reference}</a></td>
              <td scope="row">{$slip.credit_slip_number}</td>
              <td>{$slip.credit_slip_date}</td>
              <td class="text-sm-center">
                <a href="{$slip.url}"><i class="material-icons" aria-hidden="true">&#xE415;</i></a>
              </td>
            </tr>
          {/foreach}
        </tbody>
      </table>
    </div>

    <div class="credit-slips row d-flex d-xl-none">
      {foreach from=$credit_slips item=slip}
        <div class="credit-slip col-lg-6">
          <ul>
            <li class="d-flex align-items-center justify-content-between border-bottom py-3">
              <strong>{l s='Order' d='Shop.Theme.Customeraccount'}</strong>
              <a href="{$slip.order_url_details}" data-link-action="view-order-details">{$slip.order_reference}</a>
            </li>

            <li class="d-flex align-items-center justify-content-between border-bottom py-3">
              <strong>{l s='Credit slip' d='Shop.Theme.Customeraccount'}</strong>
              {$slip.credit_slip_number}
            </li>

            <li class="d-flex align-items-center justify-content-between border-bottom py-3">
              <strong>{l s='Date issued' d='Shop.Theme.Customeraccount'}</strong>
              {$slip.credit_slip_date}
            </li>

            <li class="d-flex align-items-center justify-content-end py-3">
              <a href="{$slip.url}" class="d-inline-block text-reset border-bottom">
                {l s='View credit slip' d='Shop.Theme.Customeraccount'}
              </a>
            </li>
          </ul>
        </div>
      {/foreach}
    </div>
  {else}
    <div class="alert alert-info" role="alert" data-alert="info">{l s='You have not received any credit slips.' d='Shop.Notifications.Warning'}</div>
  {/if}
{/block}
