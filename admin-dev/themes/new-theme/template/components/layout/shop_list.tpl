{**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{if isset($is_multishop)
  && $is_multishop
  && $shop_list
  && (
    isset($multishop_context)
    && $multishop_context & Shop::CONTEXT_GROUP
    || $multishop_context & Shop::CONTEXT_SHOP
    || $multishop_context & Shop::CONTEXT_ALL
  )}
  <div id="shop-list" class="shop-list dropdown ps-dropdown stores">
    <button class="btn btn-link" type="button" data-toggle="dropdown">
      <span class="selected-item">
        <i class="material-icons visibility">visibility</i>
        {if !isset($current_shop_name) || $current_shop_name == ''}
          {l s='All shops' d='Admin.Global'}
        {else}
          {$current_shop_name}
        {/if}
        <i class="material-icons arrow-down">arrow_drop_down</i>
      </span>
    </button>
    <div class="dropdown-menu dropdown-menu-right ps-dropdown-menu">
      {$shop_list}
    </div>
  </div>
{else}
  <div class="shop-list">
    <a class="link" id="header_shopname" href="{$base_url|escape:'html':'UTF-8'}" target= "_blank">
      <i class="material-icons">visibility</i>
      {l s='View my shop' d='Admin.Navigation.Header'}
    </a>
  </div>
{/if}
