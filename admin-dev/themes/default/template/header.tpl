{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *}
<!DOCTYPE html>
<!--[if lt IE 7]> <html lang="{$iso}" class="no-js lt-ie9 lt-ie8 lt-ie7 lt-ie6"> <![endif]-->
<!--[if IE 7]>    <html lang="{$iso}" class="no-js lt-ie9 lt-ie8 ie7"> <![endif]-->
<!--[if IE 8]>    <html lang="{$iso}" class="no-js lt-ie9 ie8"> <![endif]-->
<!--[if gt IE 8]> <html lang="{$iso}" class="no-js ie9"> <![endif]-->
<html lang="{$iso}">
<head>
  <meta charset="utf-8">

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <link rel="icon" type="image/x-icon" href="{$img_dir}favicon.ico" />
  <link rel="apple-touch-icon" href="{$img_dir}app_icon.png" />

  <meta name="robots" content="NOFOLLOW, NOINDEX">
  <title>{if $meta_title != ''}{$meta_title} • {/if}{$shop_name}</title>
  {if !isset($display_header_javascript) || $display_header_javascript}
  <script type="text/javascript">
    var help_class_name = '{$controller_name|@addcslashes:'\''}';
    var iso_user = '{$iso_user|@addcslashes:'\''}';
    var lang_is_rtl = '{$lang_is_rtl|intval}';
    var full_language_code = '{$full_language_code|@addcslashes:'\''}';
    var full_cldr_language_code = '{$full_cldr_language_code|@addcslashes:'\''}';
    var country_iso_code = '{$country_iso_code|@addcslashes:'\''}';
    var _PS_VERSION_ = '{$smarty.const._PS_VERSION_|@addcslashes:'\''}';
    var roundMode = {$round_mode|intval};
{if isset($shop_context)}
  {if $shop_context == Shop::CONTEXT_ALL}
    var youEditFieldFor = '{l|escape s='This field will be modified for all your shops.' js=1 d='Admin.Notifications.Info'}';
  {elseif $shop_context == Shop::CONTEXT_GROUP}
    var youEditFieldFor = '{l|escape s='This field will be modified for all shops in this shop group:' js=1 d='Admin.Notifications.Info'} <b>{$shop_name|@addcslashes:'\''}</b>';
  {else}
    var youEditFieldFor = '{l|escape s='This field will be modified for this shop:' js=1 d='Admin.Notifications.Info'} <b>{$shop_name|@addcslashes:'\''}</b>';
  {/if}
{else}
    var youEditFieldFor = '';
{/if}
		var new_order_msg = '{l|escape s='A new order has been placed on your store.' js=1 d='Admin.Navigation.Header'}';
		var order_number_msg = '{l|escape s='Order number:' js=1 d='Admin.Navigation.Header'} ';
		var total_msg = '{l|escape s='Total' js=1 d='Admin.Global'} ';
		var from_msg = '{l|escape s='From:' js=1 d='Admin.Global'} ';
		var see_order_msg = '{l|escape s='View this order' js=1 d='Admin.Orderscustomers.Feature'}';
		var new_customer_msg = '{l|escape s='A new customer registered on your store.' js=1 d='Admin.Navigation.Header'}';
        var customer_name_msg = '{l|escape s='registered' js=1 d='Admin.Navigation.Notification'} ';
		var new_msg = '{l|escape s='A new message was posted on your store.' js=1 d='Admin.Navigation.Header'}';
		var see_msg = '{l|escape s='Read this message' js=1 d='Admin.Navigation.Header'}';
		var token = '{$token|addslashes}';
		var token_admin_orders = tokenAdminOrders = '{getAdminToken tab='AdminOrders'}';
		var token_admin_customers = tokenAdminCustomers = '{getAdminToken tab='AdminCustomers'}';
		var token_admin_customer_threads = tokenAdminCustomerThreads = '{getAdminToken tab='AdminCustomerThreads'}';
		var currentIndex = '{$currentIndex|escape:'javascript':'UTF-8'|escape:'quotes'}';
		var employee_token = '{getAdminToken tab='AdminEmployees'}';
		var choose_language_translate = '{l|escape s='Choose language:' js=1 d='Admin.Actions'}';
		var default_language = '{$default_language|intval}';
		var admin_notification_get_link = adminNotificationGetLink = '{$link->getAdminLink("AdminCommon")|addslashes}';
		var admin_notification_push_link = adminNotificationPushLink ='{$link->getAdminLink("AdminCommon", true, ['route' => 'admin_common_notifications_ack'])|addslashes}';
		var tab_modules_list = '{if isset($tab_modules_list) && $tab_modules_list}{$tab_modules_list|addslashes}{/if}';
		var update_success_msg = '{l|escape s='Successful update' js=1 d='Admin.Notifications.Success'}';
		var search_product_msg = '{l|escape s='Search for a product' js=1 d='Admin.Orderscustomers.Feature'}';
	</script>
{/if}
{$admin_path = "{__PS_BASE_URI__}{basename(_PS_ADMIN_DIR_)}/themes/default/public/"}

{$preloadFilePath = "../public/preload.tpl"}

{include file=$preloadFilePath admin_dir=$admin_path}

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
  {if isset($brightness)}
  <!--
    // @todo: multishop color
    <style type="text/css">
      div#header_infos, div#header_infos a#header_shopname, div#header_infos a#header_logout, div#header_infos a#header_foaccess {ldelim}color:{$brightness}{rdelim}
    </style>
  -->
  {/if}
</head>

{if $display_header}
<body class="lang-{$iso_user}{if $lang_is_rtl} lang-rtl{/if} ps_back-office{if $employee->bo_menu} page-sidebar{if $collapse_menu} page-sidebar-closed{/if}{else} page-topbar{/if} {$controller_name|escape|strtolower}{if !empty($debug_mode)} developer-mode{/if}"
      {if isset($js_router_metadata.base_url)}data-base-url="{$js_router_metadata.base_url}"{/if}
      {if isset($js_router_metadata.token)}data-token="{$js_router_metadata.token}"{/if}>
  {* begin  HEADER *}
  <header id="header" class="bootstrap">
    <nav id="header_infos" role="navigation">
      <i class="material-icons js-mobile-menu">menu</i>

      {* Logo *}
      <a id="header_logo" href="{$default_tab_link|escape:'html':'UTF-8'}" aria-label="{l|escape s='PrestaShop logo' d='Admin.Navigation.Header'}"></a>
      <span id="shop_version">{$ps_version}</span>

      {* Quick access *}
      <div id="header_quick" class="component">
        <div class="dropdown">
          <button
            id="quick_select"
            class="btn btn-link dropdown-toggle"
            data-toggle="dropdown"
          >{l|escape s='Quick Access' d='Admin.Navigation.Header'} <i class="material-icons">arrow_drop_down</i></button>
          <ul class="dropdown-menu">
            {if !empty($quick_access)}
              {foreach $quick_access as $quick}
                <li class="quick-row-link{if $link->matchQuickLink({$quick.link})}{assign "matchQuickLink" $quick.id_quick_access} active{/if}">
                  <a {if isset($quick.class)}class="{$quick.class}"{/if} href="{$quick.link|escape:'html':'UTF-8'}" {if $quick.new_window}target="_blank"{/if}>
                      {$quick.name}
                  </a>
                </li>
              {/foreach}
            {/if}
            <li class="divider"></li>
            {if isset($matchQuickLink)}
              <li>
                <a id="quick-remove-link" href="javascript:void(0);" class="ajax-quick-link" data-method="remove"
                  data-quicklink-id="{$matchQuickLink}">
                  <i class="material-icons">remove_circle</i>
                  {l|escape s='Remove from QuickAccess' d='Admin.Navigation.Header'}
                </a>
              </li>
            {else}
              <li>
                <a id="quick-add-link" href="javascript:void(0);" class="ajax-quick-link" data-method="add">
                  <i class="material-icons">add_circle</i>
                  {l|escape s='Add current page to QuickAccess' d='Admin.Navigation.Header'}
                </a>
              </li>
            {/if}
            <li>
              <a id="quick-manage-link" href="{$link->getAdminLink("AdminQuickAccesses")|addslashes}">
                <i class="material-icons">settings</i>
                {l|escape s='Manage quick accesses' d='Admin.Navigation.Header'}
              </a>
            </li>
          </ul>
        </div>
      </div>
      {$quick_access_current_link_name = " - "|explode:$quick_access_current_link_name}
      <script>
        $(function() {
          $('.ajax-quick-link').on('click', function(e){
            e.preventDefault();

            var method = $(this).data('method');

            if(method == 'add')
              var name = prompt('{l|escape s='Please name this shortcut:' js=1 d='Admin.Navigation.Header'}', '{$quick_access_current_link_name.0|truncate:32}');

            if(method == 'add' && name || method == 'remove')
            {
              $.ajax({
                type: 'POST',
                headers: { "cache-control": "no-cache" },
                async: false,
                url: "{$link->getAdminLink('AdminQuickAccesses', true, [], ['action' => 'GetUrl', 'rand' => (1|rand:200), 'ajax' => 1])}" + "&method=" + method + ( $(this).data('quicklink-id') ? "&id_quick_access=" + $(this).data('quicklink-id') : ""),
                data: {
                  "url": "{$link->getQuickLink($smarty.server['REQUEST_URI']|escape:'javascript')}",
                  "name": name,
                  "icon": "{$quick_access_current_link_icon}"
                },
                dataType: "json",
                success: function(data) {
                  var quicklink_list ='';
                  $.each(data, function(index,value){
                    if (typeof data[index]['name'] !== 'undefined')
                      quicklink_list += '<li><a href="' + data[index]['link'] + '">' + data[index]['name'] + '</a></li>';
                  });

                  if (typeof data['has_errors'] !== 'undefined' && data['has_errors'])
                    $.each(data, function(index, value)
                      {
                        if (typeof data[index] == 'string')
                          $.growl.error({ title: "", message: data[index]});
                    });
                  else if (quicklink_list)
                  {
                    $('#header_quick ul.dropdown-menu .divider').prevAll().remove();
                    $('#header_quick ul.dropdown-menu').prepend(quicklink_list);
                    $(e.target).remove();
                    showSuccessMessage(update_success_msg);
                  }
                }
              });
            }
          });
        });
      </script>

      {* Search *}
      {include file="search_form.tpl" show_clear_btn=1}

      {if isset($debug_mode) && $debug_mode == true}
      <div class="component hide-mobile-sm">
        <a class="shop-state label-tooltip" id="debug-mode"
           data-toggle="tooltip"
           data-placement="bottom"
           data-html="true"
           title="<p class=&quot;text-left&quot;><strong>{l|escape s='Your store is in debug mode.' d='Admin.Navigation.Notification'}</strong></p><p class=&quot;text-left&quot;>{l|escape s='All the PHP errors and messages are displayed. When you no longer need it, [1]turn off[/1] this mode.' html=true sprintf=['[1]' => '<strong>', '[/1]' => '</strong>'] d='Admin.Navigation.Notification'}</p>"
             href="{$link->getAdminLink('AdminPerformance')|escape:'html':'UTF-8'}"
          >
          <i class="material-icons">bug_report</i>
          <span>{l|escape s='Debug mode' d='Admin.Navigation.Header'}</span>
        </a>
      </div>
      {/if}

      {if isset($maintenance_mode) && $maintenance_mode == true}
        {capture name="title"}
          <p class="text-left text-nowrap">
            <strong>{l s='Your store is in maintenance mode.' d='Admin.Navigation.Notification'}</strong>
          </p>
          <p class="text-left">
              {l s='Your visitors and customers cannot access your store while in maintenance mode.' d='Admin.Navigation.Notification'}
          </p>
          <p class="text-left">
            {l s='To manage the maintenance settings, go to Shop Parameters > General > Maintenance tab.' d='Admin.Navigation.Notification'}
          </p>
          {if isset($maintenance_allow_admins) && $maintenance_allow_admins}
            <p class="text-left">
                {l s='Admins can access the store front office without storing their IP.' d='Admin.Navigation.Notification'}
            </p>
          {/if}
        {/capture}
        <div class="component hide-mobile-sm">
          <a class="shop-state label-tooltip" id="maintenance-mode"
             href="{$link->getAdminLink('AdminMaintenance')|escape:'html':'UTF-8'}"
             data-toggle="tooltip"
             data-placement="bottom"
             data-html="true"
             title="{$smarty.capture.title|htmlspecialchars}"
          >
            <i class="material-icons"
               style="{if isset($maintenance_allow_admins)}color: #72c279;{/if}"
            >build</i>
            <span>{l|escape s='Maintenance mode' d='Admin.Navigation.Header'}</span>
          </a>
        </div>
      {/if}

      {* Shop name *}
      {if {$base_url}}
        <ul id="header-list" class="header-list">
          <li class="shopname" data-mobile="true" data-from="header-list" data-target="menu">
            {if isset($is_multishop) && $is_multishop && $shop_list &&
              (isset($multishop_context) &&
              $multishop_context & Shop::CONTEXT_GROUP ||
              $multishop_context & Shop::CONTEXT_SHOP ||
              $multishop_context & Shop::CONTEXT_ALL
            )}
              <ul id="header_shop" class="shop-state">
                <li class="dropdown">
                  <i class="material-icons">visibility</i>
                  <span>{$shop_list}</span>
                </li>
              </ul>
            {else}
              <a id="header_shopname" class="shop-state" href="{$base_url|escape:'html':'UTF-8'}" target="_blank">
                <i class="material-icons">visibility</i>
                <span>{l|escape s='View my store' d='Admin.Navigation.Header'}</span>
              </a>
            {/if}
          </li>
        </ul>
      {/if}

      {* Notifications *}
      {if $show_new_orders || $show_new_customers || $show_new_messages}
        <ul class="header-list component">
          <li id="notification" class="dropdown">
            <a href="javascript:void(0);" class="notification dropdown-toggle notifs">
              <i class="material-icons">notifications_none</i>
              <span id="total_notif_number_wrapper" class="notifs_badge hide">
                <span id="total_notif_value">0</span>
              </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right notifs_dropdown">
              <div class="notifications">
                <ul class="nav nav-tabs" role="tablist">
                  {$active = "active"}
                  {if $show_new_orders}
                    <li class="nav-item {$active}">
                      <a class="nav-link" data-toggle="tab" data-type="order" href="#orders-notifications" role="tab" id="orders-tab">{l|escape s='Orders' d='Admin.Navigation.Header'}<span id="orders_notif_value" class="notif-counter"></span></a>
                    </li>
                    {$active = ""}
                  {/if}
                  {if $show_new_customers}
                    <li class="nav-item {$active}">
                      <a class="nav-link" data-toggle="tab" data-type="customer" href="#customers-notifications" role="tab" id="customers-tab">{l|escape s='Customers' d='Admin.Navigation.Header'}<span id="customers_notif_value" class="notif-counter"></span></a>
                    </li>
                    {$active = ""}
                  {/if}
                  {if $show_new_messages}
                    <li class="nav-item {$active}">
                      <a class="nav-link" data-toggle="tab" data-type="customer_message" href="#messages-notifications" role="tab" id="messages-tab">{l|escape s='Messages' d='Admin.Global'}<span id="customer_messages_notif_value" class="notif-counter"></span></a>
                    </li>
                    {$active = ""}
                  {/if}
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                  {$active = "active"}
                  {if $show_new_orders}
                    <div class="tab-pane {$active} empty" id="orders-notifications" role="tabpanel">
                      <p class="no-notification">
                        {l|escape s='No new order for now :(' d='Admin.Navigation.Notification'}<br>
                        {$no_order_tip}
                      </p>
                      <div class="notification-elements"></div>
                    </div>
                    {$active = ""}
                  {/if}
                  {if $show_new_customers}
                    <div class="tab-pane {$active} empty" id="customers-notifications" role="tabpanel">
                      <p class="no-notification">
                        {l|escape s='No new customer for now :(' d='Admin.Navigation.Notification'}<br>
                        {$no_customer_tip}
                      </p>
                      <div class="notification-elements"></div>
                    </div>
                    {$active = ""}
                  {/if}
                  {if $show_new_messages}
                    <div class="tab-pane {$active} empty" id="messages-notifications" role="tabpanel">
                      <p class="no-notification">
                        {l|escape s='No new message for now.' d='Admin.Navigation.Notification'}<br>
                        {$no_customer_message_tip}
                      </p>
                      <div class="notification-elements"></div>
                    </div>
                    {$active = ""}
                  {/if}
                </div>
              </div>
            </div>
          </li>
        </ul>
      {/if}

      {* Employee *}
      <ul id="header_employee_box" class="component">
        <li id="employee_infos" class="dropdown">
          <a href="{$link->getAdminLink('AdminEmployees', true, [], ['id_employee' => $employee->id|intval, 'updateemployee' => 1])|escape:'html':'UTF-8'}"
             class="employee_name dropdown-toggle"
             data-toggle="dropdown"
          >
            <i class="material-icons">account_circle</i>
          </a>
          <ul id="employee_links" class="dropdown-menu dropdown-menu-right">
            <li class="employee-wrapper-avatar" data-mobile="true" data-from="employee_links" data-target="menu">
              <span class="employee_avatar">
                <img class="imgm img-thumbnail" alt="" src="{$employee->getImage()}" width="60" height="60" />
              </span>
            </li>
            <li class="text-left text-nowrap username" data-mobile="true" data-from="employee_links" data-target="menu">{l|escape s='Welcome back %name%' sprintf=['%name%' => $employee->firstname] d='Admin.Navigation.Header'}</li>
            <li class="employee-wrapper-profile"><a class="admin-link" href="{$link->getAdminLink('AdminEmployees', true, [], ['id_employee' => $employee->id|intval, 'updateemployee' => 1])|escape:'html':'UTF-8'}"><i class="material-icons">edit</i> {l|escape s='Your profile' d='Admin.Navigation.Header'}</a></li>
            <li class="divider"></li>

            {foreach from=$displayBackOfficeEmployeeMenu item=$menuItem}
              {assign var=menuItemProperties value=$menuItem->getProperties()}
              <li class="{$menuItem->getClass()}">
                <a class="dropdown-item" href="{$menuItemProperties.link}" {if !isset($menuItemProperties.isExternalLink) || true === $menuItemProperties.isExternalLink} target="_blank"{/if} rel="noopener noreferrer nofollow">
                  {if isset($menuItemProperties.icon)}<i class="material-icons">{$menuItemProperties.icon}</i> {/if}{$menuItem->getContent()}
                </a>
              </li>
              {if $menuItem@last}
                <p class="divider"></p>
              {/if}
            {/foreach}

            <li class="signout text-center" data-mobile="true" data-from="employee_links" data-target="menu" data-after="true"><a id="header_logout" href="{$logout_link|escape:'html':'UTF-8'}"><i class="material-icons visible-xs">power_settings_new</i> {l|escape s='Sign out' d='Admin.Navigation.Header'}</a></li>
          </ul>
        </li>
      </ul>

      {* Ajax running *}
      <span id="ajax_running" class="hidden-xs">
        <i class="icon-refresh icon-spin icon-fw"></i>
      </span>

    {if isset($displayBackOfficeTop)}{$displayBackOfficeTop}{/if}
    </nav>{* end header_infos*}
  </header>
    {include file='nav.tpl'}

  <div id="main">
    <div id="content" class="{if !$bootstrap}nobootstrap{else}bootstrap{/if}{if !isset($page_header_toolbar)} no-header-toolbar{/if} {if $current_tab_level == 3}with-tabs{/if}">
      {if isset($page_header_toolbar)}{$page_header_toolbar}{/if}
      {if isset($modal_module_list)}{$modal_module_list}{/if}

{if $install_dir_exists}
      <div class="alert alert-warning">
        {l|escape s='For security reasons, you must also delete the /install folder.' d='Admin.Login.Notification'}
      </div>
{/if}

{hook h='displayAdminAfterHeader'}

{* end display_header*}

{else}
  <body{if isset($lite_display) && $lite_display} class="ps_back-office display-modal"{/if}>
    <div id="main">
      <div id="content" class="{if !$bootstrap}nobootstrap{else}bootstrap{/if}">
{/if}
