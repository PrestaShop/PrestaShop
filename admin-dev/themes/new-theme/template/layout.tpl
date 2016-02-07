<!DOCTYPE html>
<html lang="{$iso}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=0.75, maximum-scale=0.75, user-scalable=0">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="robots" content="NOFOLLOW, NOINDEX">

  <link rel="icon" type="image/x-icon" href="{$img_dir}favicon.ico" />
  <link rel="apple-touch-icon" href="{$img_dir}app_icon.png" />

  <title>{if $meta_title != ''}{$meta_title} • {/if}{$shop_name}</title>

  {if !isset($display_header_javascript) || $display_header_javascript}
    <script type="text/javascript">
      var help_class_name = '{$controller_name|@addcslashes:'\''}';
      var iso_user = '{$iso_user|@addcslashes:'\''}';
      var full_language_code = '{$full_language_code|@addcslashes:'\''}';
      var full_cldr_language_code = '{$full_cldr_language_code|@addcslashes:'\''}';
      var country_iso_code = '{$country_iso_code|@addcslashes:'\''}';
      var _PS_VERSION_ = '{$smarty.const._PS_VERSION_|@addcslashes:'\''}';
      var roundMode = {$round_mode|intval};
      var youEditFieldFor = '';
      {if isset($shop_context)}
        {if $shop_context == Shop::CONTEXT_ALL}
          youEditFieldFor = '{l s='This field will be modified for all your shops.' js=1}';
        {elseif $shop_context == Shop::CONTEXT_GROUP}
          youEditFieldFor = '{l s='This field will be modified for all shops in this shop group:' js=1} <b>{$shop_name|@addcslashes:'\''}</b>';
        {else}
          youEditFieldFor = '{l s='This field will be modified for this shop:' js=1} <b>{$shop_name|@addcslashes:'\''}</b>';
        {/if}
      {/if}
      var autorefresh_notifications = '{$autorefresh_notifications|@addcslashes:'\''}';
      var new_order_msg = '{l s='A new order has been placed on your shop.' js=1}';
      var order_number_msg = '{l s='Order number:' js=1} ';
      var total_msg = '{l s='Total:' js=1} ';
      var from_msg = '{l s='From:' js=1} ';
      var see_order_msg = '{l s='View this order' js=1}';
      var new_customer_msg = '{l s='A new customer registered on your shop.' js=1}';
      var customer_name_msg = '{l s='Customer name:' js=1} ';
      var new_msg = '{l s='A new message was posted on your shop.' js=1}';
      var see_msg = '{l s='Read this message' js=1}';
      var token = '{$token|addslashes}';
      var token_admin_orders = '{getAdminToken tab='AdminOrders'}';
      var token_admin_customers = '{getAdminToken tab='AdminCustomers'}';
      var token_admin_customer_threads = '{getAdminToken tab='AdminCustomerThreads'}';
      var currentIndex = '{$currentIndex|@addcslashes:'\''}';
      var employee_token = '{getAdminToken tab='AdminEmployees'}';
      var choose_language_translate = '{l s='Choose language' js=1}';
      var default_language = '{$default_language|intval}';
      var admin_modules_link = '{$link->getAdminLink("AdminModules")|addslashes}';
      var tab_modules_list = '{if isset($tab_modules_list) && $tab_modules_list}{$tab_modules_list|addslashes}{/if}';
      var update_success_msg = '{l s='Update successful' js=1}';
      var errorLogin = '{l s='PrestaShop was unable to log in to Addons. Please check your credentials and your Internet connection.' js=1}';
      var search_product_msg = '{l s='Search for a product' js=1}';
    </script>
  {/if}

  {if isset($css_files)}
    {foreach from=$css_files key=css_uri item=media}
      <link href="{$css_uri|escape:'html':'UTF-8'}" rel="stylesheet" type="text/css"/>
    {/foreach}
  {/if}

  {if (isset($js_def) && count($js_def) || isset($js_files) && count($js_files))}
    {include file=$smarty.const._PS_ALL_THEMES_DIR_|cat:"javascript.tpl"}
  {/if}

  {if isset($displayBackOfficeHeader)}
    {$displayBackOfficeHeader}
  {/if}

</head>
<body class="{$smarty.get.controller|escape|strtolower}">

{* TODO: REPLACE THIS CLASSS SOMEWHERE
{if $collapse_menu} page-sidebar-closed{/if}
*}

{include file="header.tpl"}
{include file='components/layout/nav_bar.tpl'}

<div id="main-div">

  {if $install_dir_exists}

    <div class="alert alert-warning">
      {l s='For security reasons, you must also delete the /install folder.'}
    </div>

  {else}

    {if isset($page_header_toolbar)}{$page_header_toolbar}{/if}
    {if isset($modal_module_list)}{$modal_module_list}{/if}

    <div class="content-div {if !isset($page_header_toolbar)}-notoolbar{/if}">

      {* TODO: SEE IF USEFULL
      {if $current_tab_level == 3}with-tabs{/if}
      *}

      {hook h='displayAdminAfterHeader'}

      {* TODO: REPLACE THIS ELEMENT
      {if isset($conf)}
        <div class="bootstrap">
          <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {$conf}
          </div>
        </div>
      {/if}
      *}

      {include file='components/layout/error_messages.tpl'}
      {include file='components/layout/information_messages.tpl'}
      {include file='components/layout/confirmation_messages.tpl'}
      {include file='components/layout/warning_messages.tpl'}

      <div class="row ">
        <div class="col-xs-12">
          {$page}
        </div>
      </div>

    </div>

  {/if}

</div>

{* TODO: THIS FOOTER WILL BE REMOVED *}
{if $display_footer}
  {include file='footer.tpl'}
{/if}

{if isset($php_errors)}
  {include file="error.tpl"}
{/if}

{if isset($modals)}
  <div class="bootstrap">
    {$modals}
  </div>
{/if}

</body>
</html>
