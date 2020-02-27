{**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{extends file='checkout/_partials/order-confirmation-table.tpl'}

{block name='order-items-table-head'}
<div id="order-items" class="col-md-12">
  <h3 class="card-title h3">
    {if $products_count == 1}
       {l s='%product_count% item in your cart' sprintf=['%product_count%' => $products_count] d='Shop.Theme.Checkout'}
    {else}
       {l s='%products_count% items in your cart' sprintf=['%products_count%' => $products_count] d='Shop.Theme.Checkout'}
    {/if}
  	<a href="{url entity=cart params=['action' => 'show']}"><span class="step-edit"><i class="material-icons edit">mode_edit</i> {l s='edit' d='Shop.Theme.Actions'}</span></a>
  </h3>
</div>
{/block}
