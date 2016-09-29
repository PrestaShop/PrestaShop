{if isset($is_multishop) && $is_multishop && $shop_list &&
  (isset($multishop_context) &&
  $multishop_context & Shop::CONTEXT_GROUP ||
  $multishop_context & Shop::CONTEXT_SHOP ||
  $multishop_context & Shop::CONTEXT_ALL
)}
  <div id="shop-list" class="shop-list dropdown ps-dropdown stores">
    <span class="link" data-toggle="dropdown">
      <span class="selected-item">
        {if !isset($current_shop_name) || $current_shop_name == ''}
          {l s='All shops'}
        {else}
          {$current_shop_name}
        {/if}
        <i class="material-icons arrow-down">keyboard_arrow_down</i>
      </span>
    </span>
    <div class="dropdown-menu ps-dropdown-menu">
      {$shop_list}
    </div>
  </div>
{else}
  <div class="shop-list">
    <a class="link" href="{$base_url|escape:'html':'UTF-8'}" target= "_blank">{$shop_name}</a>
  </div>
{/if}
