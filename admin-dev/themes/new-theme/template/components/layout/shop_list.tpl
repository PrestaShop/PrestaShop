{if isset($is_multishop) && $is_multishop && $shop_list && (isset($multishop_context) && $multishop_context & Shop::CONTEXT_GROUP || $multishop_context & Shop::CONTEXT_SHOP)}
  <div class="shop-list dropdown ps-dropdown stores">
    {* TODO: N'AFFICHER LE DROP DOWN QU'EN MULTIBOUTIQUE *}
    {* TODO: ATTENTION AUX FONCTIONNALITES MULTIBOUTIQUE *}
    <a
      class="link"
      data-toggle="dropdown"
      href="{if isset($base_url_tc)}{$base_url_tc|escape:'html':'UTF-8'}{else}{$base_url|escape:'html':'UTF-8'}{/if}"
    >
      <span class="selected-item js-link">{$shop_name}</span><i class="material-icons arrow-down">keyboard_arrow_down</i>
    </a>
    <div class="dropdown-menu ps-dropdown-menu">
      {$shop_list}
    </div>
  </div>
{else}
  <div class="shop-list">
    <a class="link" href="{if isset($base_url_tc)}{$base_url_tc|escape:'html':'UTF-8'}{else}{$base_url|escape:'html':'UTF-8'}{/if}">{$shop_name}</a>
  </div>
{/if}
