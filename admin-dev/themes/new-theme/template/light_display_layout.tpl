<!DOCTYPE html>
<html lang="{$iso}">
<head>
  {$header}
</head>

<body
  class="lang-{$iso_user}{if $lang_is_rtl} lang-rtl{/if} {$smarty.get.controller|escape|strtolower}{if $collapse_menu} page-sidebar-closed{/if}"
  {if isset($js_router_metadata.base_url)}data-base-url="{$js_router_metadata.base_url}"{/if}
  {if isset($js_router_metadata.token)}data-token="{$js_router_metadata.token}"{/if}
>

<div id="main-div" class="light_display_layout">
    {if $install_dir_exists}
      <div class="alert alert-warning">
        {l|escape s='For security reasons, you must also delete the /install folder.' d='Admin.Login.Notification'}
      </div>
    {else}
      {if isset($modal_module_list)}{$modal_module_list}{/if}

      <div class="-notoolbar">
        {hook h='displayAdminAfterHeader'}

        {if $display_header}
          {include file='components/layout/error_messages.tpl'}
          {include file='components/layout/information_messages.tpl'}
          {include file='components/layout/confirmation_messages.tpl'}
          {include file='components/layout/warning_messages.tpl'}
        {/if}

        {$page}
        {hook h='displayAdminEndContent'}
      </div>
    {/if}
</div>

{if isset($php_errors)}
  {include file="error.tpl"}
{/if}
</body>
</html>
