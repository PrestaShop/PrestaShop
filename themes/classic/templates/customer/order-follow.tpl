{**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{extends file='customer/page.tpl'}

{block name='page_title'}
  {l s='Merchandise returns' d='Shop.Theme.Customeraccount'}
{/block}

{block name='page_content'}

  {if $ordersReturn && count($ordersReturn)}

    <h6>{l s='Here is a list of pending merchandise returns' d='Shop.Theme.Customeraccount'}</h6>

    <table class="table table-striped table-bordered hidden-sm-down">
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
    <div class="order-returns hidden-md-up">
      {foreach from=$ordersReturn item=return}
        <div class="order-return">
          <ul>
            <li>
              <strong>{l s='Order' d='Shop.Theme.Customeraccount'}</strong>
              <a href="{$return.details_url}">{$return.reference}</a>
            </li>
            <li>
              <strong>{l s='Return' d='Shop.Theme.Customeraccount'}</strong>
              <a href="{$return.return_url}">{$return.return_number}</a>
            </li>
            <li>
              <strong>{l s='Package status' d='Shop.Theme.Customeraccount'}</strong>
              {$return.state_name}
            </li>
            <li>
              <strong>{l s='Date issued' d='Shop.Theme.Customeraccount'}</strong>
              {$return.return_date}
            </li>
            {if $return.print_url}
              <li>
                <strong>{l s='Returns form' d='Shop.Theme.Customeraccount'}</strong>
                <a href="{$return.print_url}">{l s='Print out' d='Shop.Theme.Actions'}</a>
              </li>
            {/if}
          </ul>
        </div>
      {/foreach}
    </div>

  {/if}

{/block}
