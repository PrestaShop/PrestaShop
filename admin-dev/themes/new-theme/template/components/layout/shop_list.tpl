{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
          {l s='All stores' d='Admin.Global'}
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
      <span>{l s='View my store' d='Admin.Navigation.Header'}</span>
    </a>
  </div>
{/if}
