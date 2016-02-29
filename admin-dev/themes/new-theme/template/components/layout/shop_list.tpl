{if isset($is_multishop) && $is_multishop && $shop_list && (isset($multishop_context) && $multishop_context & Shop::CONTEXT_GROUP || $multishop_context & Shop::CONTEXT_SHOP)}
  <div class="shop-list dropdown">
    {* TODO: N'AFFICHER LE DROP DOWN QU'EN MULTIBOUTIQUE *}
    {* TODO: ATTENTION AUX FONCTIONNALITES MULTIBOUTIQUE *}
    <a
      class="link dropdown-toggle"
      data-toggle="dropdown"
      href="{if isset($base_url_tc)}{$base_url_tc|escape:'html':'UTF-8'}{else}{$base_url|escape:'html':'UTF-8'}{/if}"
    >
      {$shop_name}
    </a>
    <div class="dropdown-menu dropdown-menu-right">
      {$shop_list}
    </div>
  </div>
{else}
  <div class="shop-list">
    <a class="link" href="{if isset($base_url_tc)}{$base_url_tc|escape:'html':'UTF-8'}{else}{$base_url|escape:'html':'UTF-8'}{/if}">{$shop_name}</a>
  </div>
{/if}
