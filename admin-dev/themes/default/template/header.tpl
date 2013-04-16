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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7 lt-ie6 " lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8 ie7" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9 ie8" lang="en"> <![endif]-->
<!--[if gt IE 8]> <html lang="fr" class="no-js ie9" lang="en"> <![endif]-->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$iso}" lang="{$iso}">
<head>
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
	<meta name="robots" content="NOFOLLOW, NOINDEX" />
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
	<style type="text/css">
		div#header_infos, div#header_infos a#header_shopname, div#header_infos a#header_logout, div#header_infos a#header_foaccess {ldelim}color:{$brightness}{rdelim}
	</style>
{/if}
</head>
<body style="{if isset($bo_color) && $bo_color}background:{$bo_color};{/if}{if isset($bo_width) && $bo_width > 0}text-align:center;{/if}">
{if $display_header}
<div id="ajax_running"><img src="../img/admin/ajax-loader-yellow.gif" alt="" /> {l s='Loading...'}</div>
<div id="top_container" {if $bo_width > 0}style="margin:auto;width:{$bo_width}px"{/if}>
<div id="container">
{* begin  HEADER *}
	<div id="header">
		<div id="header_infos">
			<a id="header_shopname" href="{$link->getAdminLink('AdminHome')|escape:'htmlall':'UTF-8'}"><span>{$shop_name}</span></a>
			<div id="notifs_icon_wrapper">
{if {$show_new_orders} == 1}
					<div id="orders_notif" class="notifs">
							<span id="orders_notif_number_wrapper" class="number_wrapper">
								<span id="orders_notif_value">0</span>
							</span>
						<div id="orders_notif_wrapper" class="notifs_wrapper">
							<h3>{l s='Last orders'}</h3>
							<p class="no_notifs">{l s='No new orders has been placed on your shop'}</p>
							<ul id="list_orders_notif"></ul>
							<p><a href="index.php?controller=AdminOrders&amp;token={getAdminToken tab='AdminOrders'}">{l s='Show all orders'}</a></p>
						</div>
					</div>
{/if}
{if ($show_new_customers == 1)}
					<div id="customers_notif" class="notifs notifs_alternate">
							<span id="customers_notif_number_wrapper" class="number_wrapper">
								<span id="customers_notif_value">0</span>
							</span>
						<div id="customers_notif_wrapper" class="notifs_wrapper">
							<h3>{l s='Last customers'}</h3>
							<p class="no_notifs">{l s='No new customers registered on your shop'}</p>
							<ul id="list_customers_notif"></ul>
							<p><a href="index.php?controller=AdminCustomers&amp;token={getAdminToken tab='AdminCustomers'}">{l s='Show all customers'}</a></p>
						</div>
					</div>
{/if}
{if {$show_new_messages} == 1}
					<div id="customer_messages_notif" class="notifs">
							<span id="customer_messages_notif_number_wrapper" class="number_wrapper">
								<span id="customer_messages_notif_value">0</span>
							</span>
						<div id="customer_messages_notif_wrapper" class="notifs_wrapper">
							<h3>{l s='Last messages'}</h3>
							<p class="no_notifs">{l s='No new messages posted on your shop'}</p>
							<ul id="list_customer_messages_notif"></ul>
							<p><a href="index.php?tab=AdminCustomerThreads&amp;token={getAdminToken tab='AdminCustomerThreads'}">{l s='Show all messages'}</a></p>
						</div>
					</div>
{/if}
			</div>
			<div id="employee_box">
				<div id="employee_infos">
					<div class="employee_name">{l s='Welcome,'} <strong>{$first_name}&nbsp;{$last_name}</strong></div>
					<div class="clear"></div>
					<ul id="employee_links">
						<li><a href="{$link->getAdminLink('AdminEmployees')|escape:'htmlall':'UTF-8'}&id_employee={$employee->id}&amp;updateemployee">{l s='My preferences'}</a></li>
						<li class="separator">&nbsp;</li>
						<li><a id="header_logout" href="index.php?logout">{l s='logout'}</a></li>
{if {$base_url}}
						<li class="separator">&nbsp;</li>
						<a href="{$base_url}" id="header_foaccess" target="_blank" title="{l s='View my shop'}">{l s='View my shop'}</a>
{/if}
					</ul>
				</div>
			</div>
			<div id="header_search">
				<form method="post" action="index.php?controller=AdminSearch&amp;token={getAdminToken tab='AdminSearch'}">
					<input type="text" name="bo_query" id="bo_query" value="{$bo_query}" />
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
					</select>
					<input type="submit" id="bo_search_submit" class="button" value="{l s='Search'}"/>
				</form>
			</div>
{if count($quick_access) > 0}
			<div id="header_quick">
				<select onchange="quickSelect(this);" id="quick_select" class="chosen no-search">
					<option value="0">{l s='Quick Access'}</option>
					{foreach $quick_access as $quick}
						<option value="{$quick.link|escape:'htmlall':'UTF-8'}{if $quick.new_window}_blank{/if}">&raquo; {$quick.name}</option>
					{/foreach}
				</select>
			</div>
{/if}
{if isset($displayBackOfficeTop)}{$displayBackOfficeTop}{/if}
		</div>{* end header_infos*}
		<ul id="menu">
{if !$tab}
				<div class="mainsubtablist" style="display:none"></div>
{/if}
{foreach $tabs AS $t}
{if $t.active}
					<li class="submenu_size maintab {if $t.current}active{/if}" id="maintab{$t.id_tab}">
						<a href="#" class="title">
							<img src="{$t.img}" alt="" />{if $t.name eq ''}{$t.class_name}{else}{$t.name}{/if}
						</a>
						<ul class="submenu">
							{foreach from=$t.sub_tabs item=t2}
								{if $t2.active}
									<li><a href="{$t2.href|escape:'htmlall':'UTF-8'}">{if $t2.name eq ''}{$t2.class_name}{else}{$t2.name|escape:'htmlall':'UTF-8'}{/if}</a></li>
								{/if}
							{/foreach}
						</ul>
					</li>
{/if}
{/foreach}
		</ul>
	</div>{* end header*}	
{/if}
	<div id="main">
		<div id="content">
{if $display_header && $install_dir_exists}
		<div style="background-color: #FFEBCC;border: 1px solid #F90;line-height: 20px;margin: 0px 0px 10px;padding: 10px 20px;">
			{l s='For security reasons, you must also:'}&nbsp;{l s='delete the /install folder'}
		</div>
{/if}
{if $display_header && $is_multishop && $shop_list && ($multishop_context & Shop::CONTEXT_GROUP || $multishop_context & Shop::CONTEXT_SHOP)}
		<div class="multishop_toolbar">
			<span class="text_multishop">{l s='Multistore configuration for'}</span> {$shop_list}
		</div>
{/if}