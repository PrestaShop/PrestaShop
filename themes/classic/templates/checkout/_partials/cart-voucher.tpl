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
{if $cart.vouchers.allowed}
  {block name='cart_voucher'}
    <div class="block-promo">
      <div class="cart-voucher js-cart-voucher">
        {if $cart.vouchers.added}
          {block name='cart_voucher_list'}
            <ul class="promo-name card-block">
              {foreach from=$cart.vouchers.added item=voucher}
                <li class="cart-summary-line">
                  <span class="label">{$voucher.name}</span>
                  <div class="float-xs-right">
                    <span>{$voucher.reduction_formatted}</span>
                      {if isset($voucher.code) && $voucher.code !== ''}
                        <a href="{$voucher.delete_url}" data-link-action="remove-voucher"><i class="material-icons">&#xE872;</i></a>
                      {/if}
                  </div>
                </li>
              {/foreach}
            </ul>
          {/block}
        {/if}

        <p class="promo-code-button display-promo{if $cart.discounts|count > 0} with-discounts{/if}">
          <a class="collapse-button" href="#promo-code">
            {l s='Have a promo code?' d='Shop.Theme.Checkout'}
          </a>
        </p>

        <div id="promo-code" class="collapse{if $cart.discounts|count > 0} in{/if}">
          <div class="promo-code">
            {block name='cart_voucher_form'}
              <form action="{$urls.pages.cart}" data-link-action="add-voucher" method="post">
                <input type="hidden" name="token" value="{$static_token}">
                <input type="hidden" name="addDiscount" value="1">
                <input class="promo-input" type="text" name="discount_name" placeholder="{l s='Promo code' d='Shop.Theme.Checkout'}">
                <button type="submit" class="btn btn-primary"><span>{l s='Add' d='Shop.Theme.Actions'}</span></button>
              </form>
            {/block}

            {block name='cart_voucher_notifications'}
              <div class="alert alert-danger js-error" role="alert">
                <i class="material-icons">&#xE001;</i><span class="ml-1 js-error-text"></span>
              </div>
            {/block}

            <a class="collapse-button promo-code-button cancel-promo" role="button" data-toggle="collapse" data-target="#promo-code" aria-expanded="true" aria-controls="promo-code">
              {l s='Close' d='Shop.Theme.Checkout'}
            </a>
          </div>
        </div>

        {if $cart.discounts|count > 0}
          <p class="block-promo promo-highlighted">
            {l s='Take advantage of our exclusive offers:' d='Shop.Theme.Actions'}
          </p>
          <ul class="js-discount card-block promo-discounts">
            {foreach from=$cart.discounts item=discount}
              <li class="cart-summary-line">
                <span class="label">
                  <span class="code">{$discount.code}</span> - {$discount.name}
                </span>
              </li>
            {/foreach}
          </ul>
        {/if}
      </div>
    </div>
  {/block}
{/if}
