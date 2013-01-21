<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6 no-js" lang="en"><![endif]-->
<!--[if IE 7 ]><html class="ie ie7 no-js" lang="en"><![endif]-->
<!--[if IE 8 ]><html class="ie ie8 no-js" lang="en"><![endif]-->
<!--[if IE 9 ]><html class="ie ie9 no-js" lang="en"><![endif]-->
<!--[if gt IE 9]><!--><html class="no-js" lang="{$lang_iso}"><!--<![endif]--><head>
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">	
	<title>{$meta_title|escape:'htmlall':'UTF-8'}</title>
	{if isset($meta_description) AND $meta_description}
<meta name="description" content="{$meta_description|escape:html:'UTF-8'}" />{/if}
	{if isset($meta_keywords) AND $meta_keywords}<meta name="keywords" content="{$meta_keywords|escape:html:'UTF-8'}" />{/if}
	<meta name="generator" content="PrestaShop" />
    <meta name="robots" content="{if isset($nobots)}no{/if}index,{if isset($nofollow) && $nofollow}no{/if}follow" />
	<meta name="author" content="Prestashop 1.5">
	<link rel="icon" type="image/vnd.microsoft.icon" href="{$favicon_url}?{$img_update_time}" />
	<link rel="shortcut icon" type="image/x-icon" href="{$favicon_url}?{$img_update_time}" />
	<script type="text/javascript">
		var baseDir = '{$content_dir}';
		var baseUri = '{$base_uri}';
		var static_token = '{$static_token}';
		var token = '{$token}';
		var priceDisplayPrecision = {$priceDisplayPrecision*$currency->decimals};
		var priceDisplayMethod = {$priceDisplay};
		var roundMode = {$roundMode};
	</script>
{if isset($css_files)}
	{foreach from=$css_files key=css_uri item=media}<link href="{$css_uri}" rel="stylesheet" type="text/css" media="{$media}" />{/foreach}
{/if}      <link href="{$css_dir}global.css" rel="stylesheet" type="text/css" media="all" />            
{if isset($js_files)}
	{foreach from=$js_files item=js_uri}<script type="text/javascript" src="{$js_uri}"></script>{/foreach}
{/if}
	<script type="text/javascript" src="{$js_dir}cookies.js"></script>
	<script type="text/javascript" src="{$js_dir}script.js"></script>
	<script type="text/javascript" src="{$js_dir}jscript_xjquery.jqtransform.js"></script>
	<script type="text/javascript" src="{$js_dir}modernizr-2.5.3.min.js"></script>
	{$HOOK_HEADER}
</head>
<body {if isset($page_name)}id="{$page_name|escape:'htmlall':'UTF-8'}"{/if}>
<!--[if lt IE 8]><div style='clear:both;height:59px;padding:0 15px 0 15px;position:relative;z-index:10000;text-align:center;'><a href="http://www.microsoft.com/windows/internet-explorer/default.aspx?ocid=ie6_countdown_bannercode"><img src="http://storage.ie6countdown.com/assets/100/images/banners/warning_bar_0000_us.jpg" border="0" height="42" width="820" alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today." /></a></div><![endif]-->
{if !$content_only}{if isset($restricted_country_mode) && $restricted_country_mode}
	<div id="restricted-country">
		<p>{l s='You cannot place a new order from your country.'} <span class="bold">{$geolocation_country}</span></p>
	</div>
{/if}
<div id="wrapper1">
<div id="wrapper2">
<div id="wrapper3" class="clearfix">

<header id="header">
	<a id="header_logo" href="{$base_dir}" title="{$shop_name|escape:'htmlall':'UTF-8'}"><img class="logo" src="{$img_ps_dir}logo.jpg?{$img_update_time}" alt="{$shop_name|escape:'htmlall':'UTF-8'}" /></a>
	{$HOOK_TOP}
</header>

<div class="columns clearfix">

<div id="center_column" class="center_column">

{/if}