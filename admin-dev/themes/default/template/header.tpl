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
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="NOFOLLOW, NOINDEX">
	<title>{$shop_name} {if $meta_title != ''}{if isset($navigationPipe)}{$navigationPipe|escape:'html':'UTF-8'}{else}&gt;{/if} {$meta_title}{/if}</title>
	{if $display_header}
	<script type="text/javascript">
		var help_class_name = '{$controller_name|@addcslashes:'\''}';
		var iso_user = '{$iso_user|@addcslashes:'\''}';
		var country_iso_code = '{$country_iso_code|@addcslashes:'\''}';
		var _PS_VERSION_ = '{$smarty.const._PS_VERSION_|@addcslashes:'\''}';
		var roundMode = {$round_mode|intval};
{if isset($shop_context)}
	{if $shop_context == Shop::CONTEXT_ALL}
		var youEditFieldFor = '{l s='A modification of this field will be applied for all shops' js=1}';
	{elseif $shop_context == Shop::CONTEXT_GROUP}
		var youEditFieldFor = '{l s='A modification of this field will be applied for all shops of group' js=1} <b>{$shop_name|@addcslashes:'\''}</b>';
	{else}
		var youEditFieldFor = '{l s='A modification of this field will be applied for the shop' js=1} <b>{$shop_name|@addcslashes:'\''}</b>';
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
		var new_msg = '{l s='A new message posted on your shop.' js=1}';
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
	</script>
{/if}
{if isset($css_files)}
{foreach from=$css_files key=css_uri item=media}
	<link href="{$css_uri}" rel="stylesheet" type="text/css" media="{$media}" />
{/foreach}
{/if}
{if isset($js_files)}
{foreach from=$js_files item=js_uri}
	<script type="text/javascript" src="{$js_uri}"></script>
{/foreach}
{/if}

	<link rel="icon" type="image/vnd.microsoft.icon" href="{$img_dir}favicon.ico" />
	<link rel="shortcut icon" type="image/x-icon" href="{$img_dir}favicon.ico" />
	{if isset($displayBackOfficeHeader)}
		{$displayBackOfficeHeader}
	{/if}
	{if isset($brightness)}
	<!--
		/// todo multishop
		<style type="text/css">
			div#header_infos, div#header_infos a#header_shopname, div#header_infos a#header_logout, div#header_infos a#header_foaccess {ldelim}color:{$brightness}{rdelim}
		</style>
	-->
	{/if}
</head>

{if $display_header}
	<body class="{if $employee->bo_menu}page-sidebar {if $collapse_menu}page-sidebar-closed{/if}{else}page-topbar{/if} {$smarty.get.controller|escape|strtolower}">
	{* begin  HEADER *}
	<header id="header" class="bootstrap">
		<nav id="header_infos" role="navigation">
			<div class="navbar-header">
			<button id="header_nav_toggle" type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse-primary">
				<i class="icon-reorder"></i>
			</button>

			<a id="header_shopname" href="{$default_tab_link|escape:'html':'UTF-8'}">
				<img src="{$img_dir}prestashop-avatar.png" height="15" width="15" alt="{$shop_name|escape:'html':'UTF-8'}" />
				{$shop_name}
			</a>

			<ul id="header_notifs_icon_wrapper">
{if {$show_new_orders} == 1}
				<li id="orders_notif" class="dropdown" data-type="order">
					<a href="javascript:void(0);" class="dropdown-toggle notifs" data-toggle="dropdown">
						<i class="icon-shopping-cart"></i>
						<span id="orders_notif_number_wrapper" class="notifs_badge">
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
									{l s='No new orders has been placed on your shop'}
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
						<span id="customers_notif_number_wrapper" class="notifs_badge">
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
									{l s='No new customers registered on your shop'}
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
						<span id="customer_messages_notif_number_wrapper" class="notifs_badge">
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
									{l s='No new messages posted on your shop'}
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
		</div>
		<div class="collapse navbar-collapse navbar-collapse-primary">
			<form id="header_search" method="post" action="index.php?controller=AdminSearch&amp;token={getAdminToken tab='AdminSearch'}" role="search">
				<div class="form-group">
					<input type="hidden" name="bo_search_type" id="bo_search_type" />
					<div class="input-group">
						<div class="input-group-btn">
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
								<i id="search_type_icon" class="icon-reorder"></i>
								<i class="icon-caret-down"></i>
							</button>
							<ul id="header_search_options" class="dropdown-menu">
								<li class="search-all search-option active">
									<a href="#" data-value="0" data-placeholder="{l s='What are you looking for?'}" data-icon="icon-reorder">
										<i class="icon-search"></i> {l s='Everywhere'}</a>
								</li>
								<li class="divider"></li>
								<li class="search-book search-option">
									<a href="#" data-value="1" data-placeholder="{l s='Product name, SKU, reference...'}" data-icon="icon-book">
										<i class="icon-book"></i> {l s='Catalog'}
									</a>
								</li>
								<li class="search-customers-name search-option">
									<a href="#" data-value="2" data-placeholder="{l s='Email, name...'}" data-icon="icon-group">
										<i class="icon-group"></i> {l s='Customers'} {l s='by name'}
									</a>
								</li>
								<li class="search-customers-addresses search-option">
									<a href="#" data-value="6" data-placeholder="{l s='123.45.67.89'}" data-icon="icon-desktop">
										<i class="icon-desktop"></i> {l s='Customers'} {l s='by ip address'}</a>
								</li>
								<li class="search-orders search-option">
									<a href="#" data-value="3" data-placeholder="{l s='Order ID'}" data-icon="icon-credit-card">
										<i class="icon-credit-card"></i> {l s='Orders'}
									</a>
								</li>
								<li class="search-invoices search-option">
									<a href="#" data-value="4" data-placeholder="{l s='Invoice Number'}" data-icon="icon-book">
										<i class="icon-book"></i> {l s='Invoices'}
									</a>
								</li>
								<li class="search-carts search-option">
									<a href="#" data-value="5" data-placeholder="{l s='Cart ID'}" data-icon="icon-shopping-cart">
										<i class="icon-shopping-cart"></i> {l s='Carts'}
									</a>
								</li>
								<li class="search-modules search-option">
									<a href="#" data-value="7" data-placeholder="{l s='Module name'}" data-icon="icon-puzzle-piece">
										<i class="icon-puzzle-piece"></i> {l s='Modules'}
									</a>
								</li>
							</ul>
						</div>
						<input id="bo_query" name="bo_query" type="text" class="form-control" value="{$bo_query}" placeholder="{l s='Search'}" />
						<a href="javascript:void(0);" class="clear_search hide"><i class="icon-remove"></i></a>
						<span class="input-group-btn">
							<button type="submit" id="bo_search_submit" class="btn btn-primary">
								<i class="icon-search"></i>
							</button>
						</span>
					</div>
				</div>

				<script>
					$('#bo_query').on('blur', function(){ $('#header_search .form-group').removeClass('focus-search'); });
					$('#header_search *').on('focus', function(){ $('#header_search .form-group').addClass('focus-search'); });
					$('#header_search_options').on('click','li a', function(e){
						e.preventDefault();
						$('#header_search_options .search-option').removeClass('active');
						$(this).closest('li').addClass('active');
						$('#bo_search_type').val($(this).data('value'));
						$('#search_type_icon').removeAttr("class").addClass($(this).data('icon'));
						$('#bo_query').attr("placeholder",$(this).data('placeholder'));
						$('#bo_query').focus();
					});
					{if isset($search_type) && $search_type}
						$(document).ready(function() {
							$('.search-option a[data-value='+{$search_type|intval}+']').click();
						});
					{/if}
				</script>
			</form>

{if count($quick_access) > 0}
			<ul id="header_quick">
				<li class="dropdown">
					<a href="#" id="quick_select" class="dropdown-toggle" data-toggle="dropdown">{l s='Quick Access'} <b class="caret"></b></a>
					<ul class="dropdown-menu">
					{foreach $quick_access as $quick}
						<li><a href="{$quick.link|escape:'html':'UTF-8'}" {if $quick.new_window} onclick="return !window.open(this.href);"{/if}><i class="icon-chevron-right"></i> {$quick.name}</a></li>
					{/foreach}
					</ul>
				</li>
			</ul>
{/if}

			<ul id="header_employee_box">
{if {$base_url}}
				<li>
					<a href="{$base_url}" id="header_foaccess" target="_blank" title="{l s='View my shop'}">
						<i class="icon-star"></i> {l s='View my shop'}
					</a>
				</li>
{/if}
				<li id="employee_infos" class="dropdown">
					<a href="{$link->getAdminLink('AdminEmployees')|escape:'html':'UTF-8'}&id_employee={$employee->id}&amp;updateemployee" class="employee_name dropdown-toggle" data-toggle="dropdown">
						<span class="employee_avatar_small">{$employee_avatar}</span>
						{l s="Me"}
						<i class="caret"></i>
					</a>
					<ul id="employee_links" class="dropdown-menu">
						<li><span class="employee_avatar">{$employee_avatar}</span></li>
						<li class="text-center">{$first_name}&nbsp;{$last_name}</li>
						<li class="divider"></li>
						<li><a href="{$link->getAdminLink('AdminEmployees')|escape:'html':'UTF-8'}&id_employee={$employee->id}&amp;updateemployee"><i class="icon-wrench"></i> {l s='My preferences'}</a></li>
						<li class="divider"></li>
						<li><a id="header_logout" href="{$default_tab_link}&amp;logout"><i class="icon-signout"></i> {l s='Log out'}</a></li>
					</ul>
				</li>
			</ul>

			<span id="ajax_running" class="navbar-text">
				<i class="icon-refresh icon-spin"></i> {l s='Loading...'}
			</span>
		</div>
{if isset($displayBackOfficeTop)}{$displayBackOfficeTop}{/if}
		</nav>{* end header_infos*}
	</header>


	<div id="main">
		{include file='nav.tpl'}

		<div id="content" class="{if !$bootstrap}nobootstrap{else}bootstrap{/if}">
			{if isset($page_header_toolbar)}{$page_header_toolbar}{/if}
			{if isset($modal_module_list)}{$modal_module_list}{/if}

{if $install_dir_exists}
			<div class="alert alert-warning">
				{l s='For security reasons, you must also:'}&nbsp;{l s='delete the /install folder'}
			</div>
{/if}

{if $is_multishop && $shop_list && ($multishop_context & Shop::CONTEXT_GROUP || $multishop_context & Shop::CONTEXT_SHOP)}
			<div class="panel multishop_toolbar clearfix">
				<div class="col-lg-12 form-horizontal">
					<label class="control-label col-lg-3"><i class="icon-sitemap"></i> {l s='Multistore configuration for'}</label>
					<div class="col-lg-4">{$shop_list}</div>
				</div>
			</div>
{/if}
{* end display_header*}

{else}
	<body{if isset($lite_display) && $lite_display} class="display-modal"{/if}>
		<div id="main">
			<div id="content" class="{if !$bootstrap}nobootstrap{else}bootstrap{/if}">
{/if}
