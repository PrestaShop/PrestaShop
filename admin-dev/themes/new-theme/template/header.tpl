{**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale={(isset($viewport_scale)) ? $viewport_scale : '1'}">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="robots" content="NOFOLLOW, NOINDEX">

<link rel="icon" type="image/x-icon" href="{$img_dir}favicon.ico" />
<link rel="apple-touch-icon" href="{$img_dir}app_icon.png" />

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
    var admin_modules_link = '{$link->getAdminLink("AdminModulesSf", true, ['route' => "admin_module_catalog_post"])|addslashes}';
    var admin_notification_get_link = '{$link->getAdminLink("AdminCommon")|addslashes}';
    var admin_notification_push_link = '{$link->getAdminLink("AdminCommon", true, ['route' => 'admin_common_notifications_ack'])|addslashes}';
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
