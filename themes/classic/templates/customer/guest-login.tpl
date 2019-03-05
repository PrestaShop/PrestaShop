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
{extends file='page.tpl'}

{block name='page_title'}
  {l s='Guest Order Tracking' d='Shop.Theme.Customeraccount'}
{/block}

{block name='page_content'}
  <form id="guestOrderTrackingForm" action="{$urls.pages.guest_tracking}" method="get">
    <header>
      <p>{l s='To track your order, please enter the following information:' d='Shop.Theme.Customeraccount'}</p>
    </header>

    <section class="form-fields">

      <div class="form-group row">
        <label class="col-md-3 form-control-label required">
          {l s='Order Reference:' d='Shop.Forms.Labels'}
        </label>
        <div class="col-md-6">
          <input
            class="form-control"
            name="order_reference"
            type="text"
            size="8"
            value="{if isset($smarty.request.order_reference)}{$smarty.request.order_reference}{/if}"
          >
          <div class="form-control-comment">
            {l s='For example: QIIXJXNUI or QIIXJXNUI#1' d='Shop.Theme.Customeraccount'}
          </div>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-md-3 form-control-label required">
          {l s='Email:' d='Shop.Forms.Labels'}
        </label>
        <div class="col-md-6">
          <input
            class="form-control"
            name="email"
            type="email"
            value="{if isset($smarty.request.email)}{$smarty.request.email}{/if}"
          >
        </div>
      </div>

    </section>

    <footer class="form-footer text-sm-center clearfix">
      <button class="btn btn-primary" type="submit">
        {l s='Send' d='Shop.Theme.Actions'}
      </button>
    </footer>
  </form>
{/block}
