{if isset($is_multishop) && $is_multishop && $shop_list && (isset($multishop_context) && $multishop_context & Shop::CONTEXT_GROUP || $multishop_context & Shop::CONTEXT_SHOP)}
  <div id="shop-list" class="shop-list dropdown ps-dropdown stores">
    <span
      class="link"
      data-toggle="dropdown"
    >
      <span class="selected-item">
        <i class="material-icons">visibility</i>
        {l s='All shops'}
        <i class="material-icons arrow-down">keyboard_arrow_down</i>
      </span>
    </span>
    <div class="dropdown-menu ps-dropdown-menu">
      {$shop_list}
    </div>
  </div>
{else}
  <div class="shop-list">
    <a
     class="link"
     target= "_blank"
     href="{if isset($base_url_tc)}{$base_url_tc|escape:'html':'UTF-8'}{else}{$base_url|escape:'html':'UTF-8'}{/if}"
    >
    {$shop_name}
   </a>
  </div>
{/if}
