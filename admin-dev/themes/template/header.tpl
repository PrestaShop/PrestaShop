{*
* 2007-2011 PrestaShop
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$iso}" lang="{$iso}">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="robots" content="NOFOLLOW, NOINDEX" />
		<title>{$meta_title} - PrestaShop&trade;</title>
		<script type="text/javascript">
			var helpboxes = {$help_box};
			var roundMode = {$round_mode};
			{if isset($shop_context)}
				{if $shop_context == 'all'}
					var youEditFieldFor = "{l s='A modification of this field will be applied for all shops'}";
				{elseif $shop_context == 'group'}
					var youEditFieldFor = "{l s='A modification of this field will be applied for all shops of group '}<b>{$shop_name}</b>";
				{else}
					var youEditFieldFor = "{l s='A modification of this field will be applied for the shop '}<b>{$shop_name}</b>";
				{/if}
			{else}
				var youEditFieldFor = '';
			{/if}
			
			{* Notifications vars *}
			var new_order_msg = '{l s='A new order has been made on your shop.'}';
			var order_number_msg = '{l s='Order number : '}';
			var total_msg = '{l s='Total : '}';
			var from_msg = '{l s='From : '}';
			var see_order_msg = '{l s='Click here to see that order'}';
			var new_customer_msg = '{l s='A new customer registered on your shop.'}';
			var customer_name_msg = '{l s='Customer name : '}';
			var see_customer_msg = '{l s='Click here to see that customer'}';
			var new_msg = '{l s='A new message posted on your shop.'}';
			var excerpt_msg = '{l s='Excerpt : '}';
			var see_msg = '{l s='Click here to see that message'}';
			var token_admin_orders = '{$token_admin_orders}';
			var token_admin_customers = '{$token_admin_customers}';
		</script>

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
		<script type="text/javascript">
			$(function() {
				$.ajax({
					type: 'POST',
					url: 'ajax.php',
					data: 'helpAccess=1&item={$class_name}&isoUser={$iso_user}&country={$country_iso_code}&version={$version}',
					async : true,
					success: function(msg) {
						$("#help-button").html(msg);
						$("#help-button").fadeIn("slow");
					}
				});
			});
		</script>

		<link rel="shortcut icon" href="{$img_dir}favicon.ico" />
		{$HOOK_HEADER}
		<!--[if IE]>
		<link type="text/css" rel="stylesheet" href="'._PS_CSS_DIR_.'admin-ie.css" />
		<![endif]-->
		<style type="text/css">
			div#header_infos, div#header_infos a#header_shopname, div#header_infos a#header_logout, div#header_infos a#header_foaccess {
				color:{$brightness}
			}
		</style>


<script type="text/javascript">
$(document).ready(function()
{
	var hints = $('.translatable span.hint');
	if (youEditFieldFor)
	{
		hints.html(hints.html() + '<br /><span class="red">' + youEditFieldFor + '</span>');
	}
	var html = "";		
	var nb_notifs = 0;
	var wrapper_id = "";
	var type = new Array();
	
	$(".notifs").live("click", function(){
		wrapper_id = $(this).attr("id");
		type = wrapper_id.split("s_notif")
		$.post("ajax.php",
			{
				"updateElementEmployee" : "1", "updateElementEmployeeType" : type[0]
			}, function(data) {
			if(data)
			{
				if(!$("#" + wrapper_id + "_wrapper").is(":visible"))
				{
					$(".notifs_wrapper").hide();
					$("#" + wrapper_id + "_number_wrapper").hide();  
					$("#" + wrapper_id + "_wrapper").show();  
				}else
				{
					$("#" + wrapper_id + "_wrapper").hide();							
				}
			}				
		});
	});
	
	$("#main").click(function(){
		$(".notifs_wrapper").hide();
	});

	// call it once immediately, then use setTimeout if refresh is activated
	getPush({$autorefresh_notifications});
});
</script>
	</head>
	<body {if $bo_color} style="background:{$bo_color}" {/if}>
	<div id="top_container">
		<div id="container">
			<div id="header_infos"><span>
				<a id="header_shopname" href="index.php"><span>{$shop_name}</span></a><div id="notifs_icon_wrapper">
				{if {$show_new_orders} == 1}
					<div id="orders_notif" class="notifs">
						<span id="orders_notif_number_wrapper" class="number_wrapper">
							<span id="orders_notif_value">0</span>
						</span>
						<div id="orders_notif_wrapper" class="notifs_wrapper">
							<h3>{l s='Last orders'}</h3>
							<p class="no_notifs">{l s='No new orders has been made on your shop'}</p>
							<ul id="list_orders_notif"></ul>
							<p><a href="index.php?controller=AdminOrders&token={$token_admin_orders}">{l s='Show all orders'}</a></p>
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
							<p><a href="index.php?controller=AdminCustomers&token={$token_admin_customers}">{l s='Show all customers'}</a></p>
						</div>
					</div>
				{/if}
				{if {$show_new_messages} == 1}
					<div id="messages_notif" class="notifs">
						<span id="messages_notif_number_wrapper" class="number_wrapper">
							<span id="messages_notif_value">0</span>
						</span>
						<div id="messages_notif_wrapper" class="notifs_wrapper">
							<h3>{l s='Last messages'}</h3>
							<p class="no_notifs">{l s='No new messages posted on your shop'}</p>
							<ul id="list_messages_notif"></ul>
							<p><a href="index.php?controller=AdminMessages&token={$token_admin_messages}">{l s='Show all messages'}</a></p>
						</div>
					</div>
				{/if}
				</div>
				<span id="employee_links">
					{$first_name}&nbsp;{$last_name}
					[ <a href="index.php?logout" id="header_logout">
						<span>{l s='logout'}</span>
					</a> ]
				{if {$base_url}}
					- <a href="{$base_url}" id="header_foaccess" target="_blank" title="{l s='View my shop'}"><span>{l s='View my shop'}</span></a>
				{/if}
				- <a href="index.php?controller=AdminEmployees&id_employee={$employee->id}&updateemployee&token={$token_admin_employees}" style="font-size: 10px;"><img src="../img/admin/employee.gif" alt="" /> {l s='My preferences'}</a>
			</span></div>
			<div id="header_search">
				<form method="post" action="index.php?controller=AdminSearch&token={$token_admin_search}">
					<input type="text" name="bo_query" id="bo_query" value="{$bo_query}" />
					<select name="bo_search_type" id="bo_search_type">
						<option value="0">{l s='everywhere'}</option>
						<option value="1" {if {$search_type} == 1} selected="selected" {/if}>{l s='catalog'}</option>
						<option value="2" {if {$search_type} == 2} selected="selected" {/if}>{l s='customers'}</option>
						<option value="6" {if {$search_type} == 6} selected="selected" {/if}>{l s='ip address'}</option>
						<option value="3" {if {$search_type} == 3} selected="selected" {/if}>{l s='orders'}</option>
						<option value="4" {if {$search_type} == 4} selected="selected" {/if}>{l s='invoices'}</option>
						<option value="5" {if {$search_type} == 5} selected="selected" {/if}>{l s='carts'}</option>
					</select>
					<input type="submit" id="bo_search_submit" class="button" value="{l s='Search'}"/>
				</form>
			</div>
			<div id="header_quick">
				<script type="text/javascript">
				function quickSelect(elt)
				{
					var eltVal = $(elt).val();
					if (eltVal == "0") return false;
					else if (eltVal.substr(eltVal.length - 6) == "_blank") window.open(eltVal.substr(0, eltVal.length - 6), "_blank");
					else location.href = eltVal;
				}
				</script>
				<select onchange="quickSelect(this);" id="quick_select">
					<option value="0">{l s='Quick Access'}</option>
					{foreach $quick_access as $quick}
						<option value="{$quick.link}{if $quick.new_window}_blank{/if}">&gt; {$quick.name}</option>
					{/foreach}
				</select>
			</div>

			{if $multi_shop}
				<div id="header_shoplist">{l s='Select your shop:'}{$shop_list}</div>
			{/if}
			{$HOOK_TOP}
			<ul id="menu">
				{if !$tab}
					<div class="mainsubtablist" style="display:none">
					</div>
				{/if}
				{foreach $tabs AS $t}
				<li class="submenu_size maintab {if $t.current}active{/if}" id="maintab{$t.id_tab}">
					<span class="title">
						<img src="{$t.img}" alt="" />{$t.name}
					</span>
<ul class="submenu">
{foreach from=$t.sub_tabs item=t2}
<li><a href="{$t2.href}">{$t2.name}</a></li>
{/foreach}
</ul>
				</li>
				{/foreach}
			</ul>
				{foreach $tabs AS $t}
					<div id="tab{$t.id_tab}_subtabs" style="display:none">
						{foreach $t.sub_tabs AS $t2}
							<li class="subitem" ><a href="{$t2.href}">{$t2.name}</a></li>
						{/foreach}
						<div class="flatclear">&nbsp;</div>
					</div>
				{/foreach}
{* @todo : handle bo_uimode == hover  / not hover ?
				{if $employee->bo_uimode == 'hover'}
					<script type="text/javascript">
						$("#menu li").hoverIntent( { over:hoverTabs,timeout:100,out:outTabs } );
						function outTabs(){}
						function hoverTabs() {
							var content = $("#tab"+parseInt(this.id.substr(7, 3))+"_subtabs").html();
							$("#submenu").html(content);
							if (content.length == 0)
								$("#submenu").removeClass("withLeftBorder");
							else
								$("#submenu").addClass("withLeftBorder");
							$("#menu li").removeClass("active");
							$(this).addClass("active");
						}
					</script>
				{/if}
				<ul id="submenu" {if isset($mainsubtab)}class="withLeftBorder clearfix"{/if}>
					{if isset($mainsubtab)}
						{foreach $mainsubtab.sub_tabs AS $t}
							<li>
							<a href="{$t.href}">{$t.name}</a></li>
						{/foreach}
					{/if}
				</ul>
*}
					<div id="main">
						<div id="content">
							{if $install_dir_exists}
								<div style="background-color: #FFEBCC;border: 1px solid #F90;line-height: 20px;margin: 0px 0px 10px;padding: 10px 20px;">
									{l s='For security reasons, you must also:'}  {l s='delete the /install folder'}
								</div>
							{/if}
							
							{* We should display breadcrumb only if needed *}
							{if count($tabs_breadcrumb)>1}
							<div class="path_bar">
								<div id="help-button" class="floatr" style="display: none; font-family: Verdana; font-size: 10px; margin-right: 4px; margin-top: 4px;"></div>
								<a href="?token={$home_token}">{l s='Back Office'}</a>
								{foreach $tabs_breadcrumb AS $item}
									<img src="../img/admin/separator_breadcrum.png" style="margin-right:5px" alt="&gt;" />
									{if isset($item.token)}<a href="?controller={$item.class_name}&token={$item.token}">{/if}
									{$item.name}
									{if isset($item.token)}</a>{/if}
								{/foreach}
							</div>
							{/if}
							{if $is_multishop && $shop_context != 'all'}
								<div class="multishop_info">
									{if $shop_context == 'group'}
										{l s='You are configuring your store for group shop '}<b>{$shop_name}</b>
									{elseif $shop_context == 'shop'}
										{l s='You are configuring your store for shop '}<b>{$shop_name}</b>
									{/if}
								</div>
							{/if}
