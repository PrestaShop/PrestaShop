{*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
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
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="robots" content="NOFOLLOW, NOINDEX">
	<title>{$meta_title} - PrestaShop&trade;</title>
	{if $display_header}
	<script type="text/javascript">
		var help_class_name = '{$controller_name}';
		var iso_user = '{$iso_user}';
		var country_iso_code = '{$country_iso_code}';
		var _PS_VERSION_ = '{$smarty.const._PS_VERSION_}';
		var helpboxes = {$help_box};
		var roundMode = {$round_mode};
			{if isset($shop_context)}
				{if $shop_context == Shop::CONTEXT_ALL}
				var youEditFieldFor = "{l s='A modification of this field will be applied for all shops' slashes=1 }";
					{elseif $shop_context == Shop::CONTEXT_GROUP}
				var youEditFieldFor = "{l s='A modification of this field will be applied for all shops of group ' slashes=1 }<b>{$shop_name}</b>";
					{else}
				var youEditFieldFor = "{l s='A modification of this field will be applied for the shop ' slashes=1 }<b>{$shop_name}</b>";
				{/if}
				{else}
			var youEditFieldFor = '';
			{/if}
		{* Notifications vars *}
		var autorefresh_notifications = '{$autorefresh_notifications}';
		var new_order_msg = '{l s='A new order has been placed on your shop.' slashes=1}';
		var order_number_msg = '{l s='Order number: ' slashes=1}';
		var total_msg = '{l s='Total: ' slashes=1}';
		var from_msg = '{l s='From: ' slashes=1}';
		var see_order_msg = '{l s='View this order' slashes=1}';
		var new_customer_msg = '{l s='A new customer registered on your shop.' slashes=1}';
		var customer_name_msg = '{l s='Customer name: ' slashes=1}';
		var see_customer_msg = '{l s='View this customer' slashes=1}';
		var new_msg = '{l s='A new message posted on your shop.' slashes=1}';
		var excerpt_msg = '{l s='Excerpt: ' slashes=1}';
		var see_msg = '{l s='Read this message' slashes=1}';
		var token_admin_orders = '{getAdminToken tab='AdminOrders' slashes=1}';
		var token_admin_customers = '{getAdminToken tab='AdminCustomers' slashes=1}';
		var token_admin_customer_threads = '{getAdminToken tab='AdminCustomerThreads' slashes=1}';
		var currentIndex = '{$currentIndex}';
		var default_language = '{$default_language|intval}';
		var choose_language_translate = "{l s='Choose language' slashes=1}";
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
	<!--[if IE]>
	<link type="text/css" rel="stylesheet" href="{$base_url}css/admin-ie.css" />
	<![endif]-->
	{if isset($brightness)}
	<!--
		/// todo multishop
		<style type="text/css">
			div#header_infos, div#header_infos a#header_shopname, div#header_infos a#header_logout, div#header_infos a#header_foaccess {ldelim}color:{$brightness}{rdelim}
		</style>
	-->
	{/if}
</head>


<body class="page-sidebar-closed">
{if $display_header}
{* begin  HEADER *}
	<header id="header">
		<nav id="header_infos" role="navigation">
			<div class="navbar-header">
			<button id="header_nav_toggle" type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse-primary">
					<i class="icon-reorder"></i>
			</button>

			<a id="header_shopname" href="{$link->getAdminLink('AdminHome')|escape:'htmlall':'UTF-8'}">
				<img src="{$img_dir}prestashop-avatar.png" height="15" width="15" />
				{$shop_name}
			</a>

			<ul id="header_notifs_icon_wrapper">
{if {$show_new_orders} == 1}
				<li id="orders_notif" class="dropdown" >
					<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
						<i class="icon-shopping-cart"></i>
						<span id="orders_notif_number_wrapper" class="notifs_badge">
							<span id="orders_notif_value">0</span>
						</span>
					</a>
					<div class="dropdown-menu notifs_dropdown">
						<section id="orders_notif_wrapper" class="notifs_panel">
							<header class="notifs_panel_header">
								<h3>{l s='Last orders'}</h3>
							</header>
							<div id="list_orders_notif" class="list-group">
								<a href="#" class="media list-group-item no_notifs">
									<span class="pull-left">
										<i class="icon-time"></i>
									</span>
									<span class="media-body">
										{l s='No new orders has been placed on your shop'}
									</span>
								</a>
							</div>
							<footer class="notifs_panel_footer">
								<a href="index.php?controller=AdminOrders&amp;token={getAdminToken tab='AdminOrders'}">{l s='Show all orders'}</a>
							</footer>
						</section>
					</div>
				</li>
{/if}
{if {$show_new_customers} == 1}
				<li id="customers_notif" class="dropdown">
					<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
						<i class="icon-user"></i>
						<span id="customers_notif_number_wrapper" class="notifs_badge">
							<span id="customers_notif_value">0</span>
						</span>
					</a>
					<div class="dropdown-menu notifs_dropdown">
						<section id="customers_notif_wrapper" class="notifs_panel">
							<header class="notifs_panel_header">
								<h3>{l s='Last customers'}</h3>
							</header>
							<div id="list_customers_notif" class="list-group">
								<a href="#" class="media list-group-item no_notifs">
									<span class="pull-left">
										<i class="icon-time"></i>
									</span>
									<span class="media-body">
										{l s='No new customers registered on your shop'}
									</span>
								</a>
							</div>
							<footer class="panel-footer">
								<a href="index.php?controller=AdminCustomers&amp;token={getAdminToken tab='AdminCustomers'}">{l s='Show all customers'}</a>
							</footer>
						</section>
					</div>
				</li>
{/if}
{if {$show_new_messages} == 1}
				<li id="customer_messages_notif" class="dropdown">
					<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
						<i class="icon-envelope"></i>
						<span id="customer_messages_notif_number_wrapper" class="notifs_badge">
							<span id="customer_messages_notif_value" >0</span>
						</span>
					</a>
					<div class="dropdown-menu notifs_dropdown">
						<section id="customer_messages_notif_wrapper" class="notifs_panel">
							<header class="notifs_panel_header">
								<h3>{l s='Last messages'}</h3>
							</header>
							<div id="list_orders_notif" class="list-group">
								<a href="#" class="media list-group-item no_notifs">
									<span class="pull-left">
										<i class="icon-time"></i>
									</span>
									<span class="media-body">
										{l s='No new messages posted on your shop'}
									</span>
								</a>
							</div>
							<footer class="panel-footer text-small">
								<a href="index.php?tab=AdminCustomerThreads&amp;token={getAdminToken tab='AdminCustomerThreads'}">{l s='Show all messages'}</a>
							</footer>
						</section>
					</div>
				</li>
{/if}
			</ul>
		</div>
		<div class="collapse navbar-collapse navbar-collapse-primary">
			<form id="header_search" method="post" action="index.php?controller=AdminSearch&amp;token={getAdminToken tab='AdminSearch'}" role="search">
				<div class="form-group">
					<input type="text" class="form-control" name="bo_query" id="bo_query" value="{$bo_query}" placeholder="{l s='Search'}"/>
				</div>
				<button type="submit" id="bo_search_submit" class="btn btn-default">
					<i class="icon-search"></i>
				</button>
				<!-- 	//todo Search in section 
				<select name="bo_search_type" id="bo_search_type" class="chosen no-search">
					<option value="0">{l s='everywhere'}</option>
					<option value="1" {if {$search_type} == 1} selected="selected" {/if}>{l s='catalog'}</option>
					<optgroup label="{l s='customers'}:">
						<option value="2" {if {$search_type} == 2} selected="selected" {/if}>{l s='by name'}</option>
						<option value="6" {if {$search_type} == 6} selected="selected" {/if}>{l s='by ip address'}</option>
					</optgroup>
					<option value="3" {if {$search_type} == 3} selected="selected" {/if}>{l s='orders'}</option>
					<option value="4" {if {$search_type} == 4} selected="selected" {/if}>{l s='invoices'}</option>
					<option value="5" {if {$search_type} == 5} selected="selected" {/if}>{l s='carts'}</option>
					<option value="7" {if {$search_type} == 7} selected="selected" {/if}>{l s='modules'}</option>
				</select> -->
			</form>

{if count($quick_access) > 0}
			<ul id="header_quick">
				<li class="dropdown">
					<a href="#" id="quick_select" class="dropdown-toggle" data-toggle="dropdown">{l s='Quick Access'} <b class="caret"></b></a>
					<ul class="dropdown-menu">
					{foreach $quick_access as $quick}
						<li><a href="{$quick.link|escape:'htmlall':'UTF-8'}" {if $quick.new_window} target="_blank"{/if}><i class="icon-chevron-right"></i> {$quick.name}</a></li>
					{/foreach}
					</ul>
				</li>
			</ul>
{/if}

			<ul id="header_employee_box">
{if {$base_url}}
				<li><a href="{$base_url}" id="header_foaccess" target="_blank" title="{l s='View my shop'}"><i class="icon-eye-open"></i> {l s='View my shop'}</a></li>
{/if}
				<li id="employee_infos" class="dropdown">
					<a href='#' class="employee_name dropdown-toggle" data-toggle="dropdown">
						<img src="{$img_dir}prestashop-avatar.png" height="15" width="15" />
						{$first_name}&nbsp;{$last_name}
						<i class="icon-angle-down"></i>
					</a>
					<ul id="employee_links" class="dropdown-menu">
						<li><a href="{$link->getAdminLink('AdminEmployees')|escape:'htmlall':'UTF-8'}&id_employee={$employee->id}&amp;updateemployee"><i class="icon-wrench"></i> {l s='My preferences'}</a></li>
						<li><a id="header_logout" href="index.php?logout"><i class="icon-signout"></i> {l s='logout'}</a></li>
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

{* end header*}	
{/if}

	<div id="main">
		{include file='nav-top.tpl'}
		{include file='nav-side.tpl'}

		<div id="content" class="page-content">
{if $display_header && $install_dir_exists}
			<div class="alert alert-warning">
				{l s='For security reasons, you must also:'}&nbsp;{l s='delete the /install folder'}
			</div>
{/if}
{if $display_header && $is_multishop && $shop_list && ($multishop_context & Shop::CONTEXT_GROUP || $multishop_context & Shop::CONTEXT_SHOP)}
			<div class="multishop_toolbar">
				<span class="text_multishop">{l s='Multistore configuration for'}</span> {$shop_list}
			</div>
{/if}