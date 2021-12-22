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
{extends file=$layout}

{block name='header'}
  {include file='checkout/_partials/header.tpl'}
{/block}

{block name='content'}
  <section id="content">
    <div class="row">
      <div class="cart-grid-body col-xs-12 col-lg-8">
        {block name='checkout_process'}
          {render file='checkout/checkout-process.tpl' ui=$checkout_process}
        {/block}
      </div>
      <div class="cart-grid-right col-xs-12 col-lg-4">
        {block name='cart_summary'}
          {include file='checkout/_partials/cart-summary.tpl' cart=$cart}
        {/block}
        {hook h='displayReassurance'}
      </div>
    </div>
  </section>
{/block}

{block name='footer'}
  {include file='checkout/_partials/footer.tpl'}
{/block}
