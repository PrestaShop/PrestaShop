{**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{extends file='customer/order-detail.tpl'}

{block name='page_title'}
  {l s='Guest Tracking' d='Shop.Theme.CustomerAccount'}
{/block}

{block name='order_detail'}
  {include file='customer/_partials/order-detail-no-return.tpl'}
{/block}

{block name='order_messages'}
{/block}

{block name='page_content' append}
  {block name='guest_to_customer'}
    <form action="{$urls.pages.guest_tracking}" method="post">
      <header>
        <h1 class="h3">{l s='Transform your guest account into a customer account and enjoy:' d='Shop.Theme.CustomerAccount'}</h1>
        <ul>
          <li> -{l s='Personalized and secure access' d='Shop.Theme.CustomerAccount'}</li>
          <li> -{l s='Fast and easy checkout' d='Shop.Theme.CustomerAccount'}</li>
          <li> -{l s='Easier merchandise return' d='Shop.Theme.CustomerAccount'}</li>
        </ul>
      </header>

      <section class="form-fields">

        <label>
          <span>{l s='Set your password:' d='Shop.Forms.Labels'}</span>
          <input type="password" data-validate="isPasswd" name="password" value="">
        </label>

      </section>

      <footer class="form-footer">
        <input type="hidden" name="submitTransformGuestToCustomer" value="1">
        <input type="hidden" name="id_order" value="{$order.details.id}">
        <input type="hidden" name="order_reference" value="{$order.details.reference}">
        <input type="hidden" name="email" value="{$guest_email}">

        <button class="btn btn-primary" type="submit">{l s='Send' d='Shop.Theme.Actions'}</button>
      </footer>

    </form>
  {/block}
{/block}
