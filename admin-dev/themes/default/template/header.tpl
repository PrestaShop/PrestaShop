{*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7 lt-ie6 " lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8 ie7" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9 ie8" lang="en"> <![endif]-->
<!--[if gt IE 8]> <html lang="fr" class="no-js ie9" lang="en"> <![endif]-->
<html lang="{$iso}">
<head>
	<meta charset="utf-8">
	
	<meta name="viewport" content="width=device-width, initial-scale=0.75, maximum-scale=0.75, user-scalable=0">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<link rel="icon" type="image/x-icon" href="{$img_dir}favicon.ico" />
	<link rel="apple-touch-icon" href="{$img_dir}app_icon.png" />

	<meta name="robots" content="NOFOLLOW, NOINDEX">
	<title>{if $meta_title != ''}{$meta_title} • {/if}{$shop_name}</title>
	{if $display_header}
	<script type="text/javascript">
		var help_class_name = '{$controller_name|@addcslashes:'\''}';
		var iso_user = '{$iso_user|@addcslashes:'\''}';
		var full_language_code = '{$full_language_code|@addcslashes:'\''}';
		var country_iso_code = '{$country_iso_code|@addcslashes:'\''}';
		var _PS_VERSION_ = '{$smarty.const._PS_VERSION_|@addcslashes:'\''}';
		var roundMode = {$round_mode|intval};
{if isset($shop_context)}
	{if $shop_context == Shop::CONTEXT_ALL}
		var youEditFieldFor = '{l s='This field will be modified for all your shops.' js=1}';
	{elseif $shop_context == Shop::CONTEXT_GROUP}
		var youEditFieldFor = '{l s='This field will be modified for all shops in this shop group:' js=1} <b>{$shop_name|@addcslashes:'\''}</b>';
	{else}
		var youEditFieldFor = '{l s='This field will be modified for this shop:' js=1} <b>{$shop_name|@addcslashes:'\''}</b>';
	{/if}
{else}
		var youEditFieldFor = '';
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
		var errorLogin = '{l s='PrestaShop was unable to log in to Addons. Please check your credentials and your Internet connection.'}';
		var search_product_msg = '{l s='Search for a product' js=1}';
	</script>
{/if}
{if isset($css_files)}
{foreach from=$css_files key=css_uri item=media}
	<link href="{$css_uri|escape:'html':'UTF-8'}" rel="stylesheet" type="text/css"/>
{/foreach}
{/if}
{if isset($js_files)}
{foreach from=$js_files item=js_uri}
	<script type="text/javascript" src="{$js_uri|escape:'html':'UTF-8'}"></script>
{/foreach}
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
	<body class="ps_back-office{if $employee->bo_menu} page-sidebar{if $collapse_menu} page-sidebar-closed{/if}{else} page-topbar{/if} {$smarty.get.controller|escape|strtolower}">
	{* begin  HEADER *}
	<header id="header" class="bootstrap">
		<nav id="header_infos" role="navigation">
			<div class="navbar-header">
				<button id="header_nav_toggle" type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse-primary">
					<i class="icon-reorder"></i>
				</button>

				<a id="header_shopname" href="{$default_tab_link|escape:'html':'UTF-8'}">
					<img src="{$img_dir}prestashop-avatar.png" alt="{$shop_name|escape:'html':'UTF-8'}" />
					{$shop_name}
				</a>

				<ul id="header_notifs_icon_wrapper">
{if {$show_new_orders} == 1}
					<li id="orders_notif" class="dropdown" data-type="order">
						<a href="javascript:void(0);" class="dropdown-toggle notifs" data-toggle="dropdown">
							<i class="icon-shopping-cart"></i>
							<span id="orders_notif_number_wrapper" class="notifs_badge hide">
								<span id="orders_notif_value">0</span>
							</span>
						</a>
						<div class="dropdown-menu notifs_dropdown">
							<section id="orders_notif_wrapper" class="notifs_panel">
								<div class="notifs_panel_header">
									<h3>{l s='Latest Orders'}</h3>
								</div>
								<div id="list_orders_notif" class="list_notif">
									<span class="no_notifs">
										{l s='No new orders have been placed on your shop.'}
									</span>
								</div>
								<div class="notifs_panel_footer">
									<a href="index.php?controller=AdminOrders&amp;token={getAdminToken tab='AdminOrders'}">{l s='Show all orders'}</a>
								</div>
							</section>
						</div>
					</li>
{/if}
{if {$show_new_customers} == 1}
					<li id="customers_notif" class="dropdown" data-type="customer">
						<a href="javascript:void(0);" class="dropdown-toggle notifs" data-toggle="dropdown">
							<i class="icon-user"></i>
							<span id="customers_notif_number_wrapper" class="notifs_badge hide">
								<span id="customers_notif_value">0</span>
							</span>
						</a>
						<div class="dropdown-menu notifs_dropdown">
							<section id="customers_notif_wrapper" class="notifs_panel">
								<div class="notifs_panel_header">
									<h3>{l s='Latest Registrations'}</h3>
								</div>
								<div id="list_customers_notif" class="list_notif">
									<span class="no_notifs">
										{l s='No new customers have registered on your shop.'}
									</span>
								</div>
								<div class="notifs_panel_footer">
									<a href="index.php?controller=AdminCustomers&amp;token={getAdminToken tab='AdminCustomers'}">{l s='Show all customers'}</a>
								</div>
							</section>
						</div>
					</li>
{/if}
{if {$show_new_messages} == 1}
					<li id="customer_messages_notif" class="dropdown" data-type="customer_message">
						<a href="javascript:void(0);" class="dropdown-toggle notifs" data-toggle="dropdown">
							<i class="icon-envelope"></i>
							<span id="customer_messages_notif_number_wrapper" class="notifs_badge hide">
								<span id="customer_messages_notif_value" >0</span>
							</span>
						</a>
						<div class="dropdown-menu notifs_dropdown">
							<section id="customer_messages_notif_wrapper" class="notifs_panel">
								<div class="notifs_panel_header">
									<h3>{l s='Latest Messages'}</h3>
								</div>
								<div id="list_customer_messages_notif" class="list_notif">
									<span class="no_notifs">
										{l s='No new messages have been posted on your shop.'}
									</span>
								</div>
								<div class="notifs_panel_footer">
									<a href="index.php?controller=AdminCustomerThreads&amp;token={getAdminToken tab='AdminCustomerThreads'}">{l s='Show all messages'}</a>
								</div>
							</section>
						</div>
					</li>
{/if}
				</ul>

{if count($quick_access) > 0}
				<ul id="header_quick">
					<li class="dropdown">
						<a href="#" id="quick_select" class="dropdown-toggle" data-toggle="dropdown">{l s='Quick Access'} <i class="icon-caret-down"></i></a>
						<ul class="dropdown-menu">
						{foreach $quick_access as $quick}
							<li><a href="{$quick.link|escape:'html':'UTF-8'}" {if $quick.new_window} onclick="return !window.open(this.href);"{/if}><i class="icon-chevron-right"></i> {$quick.name}</a></li>
						{/foreach}
						</ul>
					</li>
				</ul>
{/if}
				<ul id="header_employee_box">
					{if !isset($logged_on_addons) || !$logged_on_addons}
						<li>
							<a href="#" class="addons_connect toolbar_btn" data-toggle="modal" data-target="#modal_addons_connect" title="{l s='Addons'}">
								<i class="icon-chain-broken"></i>
								<span class="string-long">{l s='Not connected to PrestaShop Addons'}</span>
								<span class="string-short">{l s='Addons'}</span>
							</a>
						</li>
					{/if}
{if {$base_url}}
					<li>
						<a href="{if isset($base_url_tc)}{$base_url_tc|escape:'html':'UTF-8'}{else}{$base_url|escape:'html':'UTF-8'}{/if}" id="header_foaccess" target="_blank" title="{l s='View my shop'}">
							<i class="icon-star"></i>
							<span class="string-long">{l s='My shop'}</span>
							<span class="string-short">{l s='Shop'}</span>
						</a>
					</li>
{/if}
					<li id="employee_infos" class="dropdown">
						<a href="{$link->getAdminLink('AdminEmployees')|escape:'html':'UTF-8'}&amp;id_employee={$employee->id|intval}&amp;updateemployee" class="employee_name dropdown-toggle" data-toggle="dropdown">
							<span class="employee_avatar_small">
								{if isset($employee)}
								<img class="imgm img-thumbnail" alt="" src="{$employee->getImage()}" width="32" height="32" />
								{/if}
							</span>
							<span class="string-long">{$employee->firstname}&nbsp;{$employee->lastname}</span>
							<span class="string-short">{l s='Me'}</span>
							<i class="caret"></i>
						</a>
						<ul id="employee_links" class="dropdown-menu">
							<li>
								<span class="employee_avatar">
									<img class="imgm img-thumbnail" alt="" src="{$employee->getImage()}" width="96" height="96" />
								</span>
							</li>
							<li class="text-center">{$employee->firstname} {$employee->lastname}</li>
							<li class="divider"></li>
							<li><a href="{$link->getAdminLink('AdminEmployees')|escape:'html':'UTF-8'}&amp;id_employee={$employee->id|intval}&amp;updateemployee"><i class="icon-wrench"></i> {l s='My preferences'}</a></li>
							<li class="divider"></li>
							<li><a id="header_logout" href="{$default_tab_link|escape:'html':'UTF-8'}&amp;logout"><i class="icon-signout"></i> {l s='Sign out'}</a></li>
						</ul>
					</li>
				</ul>

				<span id="ajax_running">
					<i class="icon-refresh icon-spin icon-fw"></i>
				</span>

	{if isset($displayBackOfficeTop)}{$displayBackOfficeTop}{/if}
			</div>
		</nav>{* end header_infos*}
	</header>

	<div id="main">
		{include file='nav.tpl'}

		<div id="content" class="{if !$bootstrap}nobootstrap{else}bootstrap{/if}">
			{if isset($page_header_toolbar)}{$page_header_toolbar}{/if}
			{if isset($modal_module_list)}{$modal_module_list}{/if}

{if $install_dir_exists}
			<div class="alert alert-warning">
				{l s='For security reasons, you must also delete the /install folder.'}
			</div>
{/if}


{* end display_header*}

{else}
	<body{if isset($lite_display) && $lite_display} class="ps_back-office display-modal"{/if}>		
		<div id="main">
			<div id="content" class="{if !$bootstrap}nobootstrap{else}bootstrap{/if}">
{/if}
