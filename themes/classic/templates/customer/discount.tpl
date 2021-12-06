{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}
{extends file='customer/page.tpl'}

{block name='page_title'}
  {l s='Your vouchers' d='Shop.Theme.Customeraccount'}
{/block}

{block name='page_content'}
  {if $cart_rules}
    <table class="table table-striped table-bordered hidden-sm-down">
      <thead class="thead-default">
        <tr>
          <th>{l s='Code' d='Shop.Theme.Checkout'}</th>
          <th>{l s='Description' d='Shop.Theme.Checkout'}</th>
          <th>{l s='Quantity' d='Shop.Theme.Checkout'}</th>
          <th>{l s='Value' d='Shop.Theme.Checkout'}</th>
          <th>{l s='Minimum' d='Shop.Theme.Checkout'}</th>
          <th>{l s='Cumulative' d='Shop.Theme.Checkout'}</th>
          <th>{l s='Expiration date' d='Shop.Theme.Checkout'}</th>
        </tr>
      </thead>
      <tbody>
        {foreach from=$cart_rules item=cart_rule}
          <tr>
            <th scope="row">{$cart_rule.code}</th>
            <td>{$cart_rule.name}</td>
            <td class="text-xs-right">{$cart_rule.quantity_for_user}</td>
            <td>{$cart_rule.value}</td>
            <td>{$cart_rule.voucher_minimal}</td>
            <td>{$cart_rule.voucher_cumulable}</td>
            <td>{$cart_rule.voucher_date}</td>
          </tr>
        {/foreach}
      </tbody>
    </table>
    <div class="cart-rules hidden-md-up">
      {foreach from=$cart_rules item=cart_rule}
        <div class="cart-rule">
          <ul>
            <li>
              <strong>{l s='Code' d='Shop.Theme.Checkout'}</strong>
              {$cart_rule.code}
            </li>
            <li>
              <strong>{l s='Description' d='Shop.Theme.Checkout'}</strong>
              {$cart_rule.name}
            </li>
            <li>
              <strong>{l s='Quantity' d='Shop.Theme.Checkout'}</strong>
              {$cart_rule.quantity_for_user}
            </li>
            <li>
              <strong>{l s='Value' d='Shop.Theme.Checkout'}</strong>
              {$cart_rule.value}
            </li>
            <li>
              <strong>{l s='Minimum' d='Shop.Theme.Checkout'}</strong>
              {$cart_rule.voucher_minimal}
            </li>
            <li>
              <strong>{l s='Cumulative' d='Shop.Theme.Checkout'}</strong>
              {$cart_rule.voucher_cumulable}
            </li>
            <li>
              <strong>{l s='Expiration date' d='Shop.Theme.Checkout'}</strong>
              {$cart_rule.voucher_date}
            </li>
          </ul>
        </div>
      {/foreach}
    </div>
  {else}
    <div class="alert alert-info" role="alert" data-alert="info">{l s='You do not have any vouchers.' d='Shop.Notifications.Warning'}</div>
  {/if}
{/block}
