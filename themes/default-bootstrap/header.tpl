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
<!DOCTYPE HTML>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7 " lang="{$lang_iso}"> <![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8 ie7" lang="{$lang_iso}"> <![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9 ie8" lang="{$lang_iso}"> <![endif]-->
<!--[if gt IE 8]> <html class="no-js ie9" lang="{$lang_iso}"> <![endif]-->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$lang_iso}">
	<head>
		<title>{$meta_title|escape:'htmlall':'UTF-8'}</title>
{if isset($meta_description) AND $meta_description}
		<meta name="description" content="{$meta_description|escape:html:'UTF-8'}" />
{/if}
{if isset($meta_keywords) AND $meta_keywords}
		<meta name="keywords" content="{$meta_keywords|escape:html:'UTF-8'}" />
{/if}
		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
		<meta http-equiv="content-language" content="{$meta_language}" />
		<meta name="generator" content="PrestaShop" />
		<meta name="robots" content="{if isset($nobots)}no{/if}index,{if isset($nofollow) && $nofollow}no{/if}follow" />
        <meta name="viewport" content="width=device-width, minimum-scale=0.25, maximum-scale=1.6, initial-scale=1.0" /> 
        <meta name="apple-mobile-web-app-capable" content="yes" /> 
        <script>
			if (navigator.userAgent.match(/Android/i)) {
				var viewport = document.querySelector("meta[name=viewport]");
				viewport.setAttribute('content', 'initial-scale=1.0,maximum-scale=1.0,user-scalable=0,width=device-width,height=device-height');
			}
				if(navigator.userAgent.match(/Android/i)){
				window.scrollTo(0,1);
			 }
		</script>
		<link rel="icon" type="image/vnd.microsoft.icon" href="{$favicon_url}?{$img_update_time}" />
		<link rel="shortcut icon" type="image/x-icon" href="{$favicon_url}?{$img_update_time}" />
		<script type="text/javascript">
			var baseDir = '{$content_dir|addslashes}';
			var baseUri = '{$base_uri|addslashes}';
			var static_token = '{$static_token|addslashes}';
			var token = '{$token|addslashes}';
			var priceDisplayPrecision = {$priceDisplayPrecision*$currency->decimals};
			var priceDisplayMethod = {$priceDisplay};
			var roundMode = {$roundMode};
		</script>
       	<link href="{$css_dir}/bootstrap_lib/bootstrap.css" rel="stylesheet" type="text/css" media="all" />
        <link href="{$css_dir}/font-awesome.css" rel="stylesheet" type="text/css" media="all" />
        <link href="{$css_dir}/jquery/uniform.default.css" rel="stylesheet" type="text/css" media="all" />
        <link href="{$css_dir}/highdpi.css" rel="stylesheet" media="only screen and (-webkit-min-device-pixel-ratio: 2)" />
        <link href="{$css_dir}/jquery/footable.core.css" rel="stylesheet" type="text/css" media="all" />
        <link href="{$css_dir}jquery/jquery.bxslider.css" rel="stylesheet" type="text/css" media="all" /> 
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
<script src="{$js_dir}/tools/bootstrap.min.js"></script>
<!--[if IE 7]><html class="no-js lt-ie9 ie8" lang="{$lang_iso}">
	 <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
     <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->
<script src="{$js_dir}/tools/jquery.total-storage.min.js"></script>
<script src="{$js_dir}/jquery/jquery.uniform-modify.js"></script>
<script src="{$js_dir}/jquery/highdpi.js"></script>
<script src="{$js_dir}/jquery/jquery.bxslider.js"></script>
<script src="{$js_dir}/jquery/footable.js"></script>
<script src="{$js_dir}/jquery/footable.sort.js"></script>
<script src="{$js_dir}/jquery/resonsive_utilites.js"></script>
		{$HOOK_HEADER}
	</head>
	
	<body {if isset($page_name)}id="{$page_name|escape:'htmlall':'UTF-8'}"{/if} class="{if $hide_left_column}hide-left-column {/if} {if $hide_right_column}hide-right-column {/if} {if $content_only} content_only {/if}lang_{$lang_iso}">
	{if !$content_only}
		{if isset($restricted_country_mode) && $restricted_country_mode}
		<div id="restricted-country">
			<p>{l s='You cannot place a new order from your country.'} <span class="bold">{$geolocation_country}</span></p>
		</div>
		{/if}
		<div id="page">
			<!-- Header -->
            <div class="header-container">
                <header id="header">
                	<div class="header-row">
                        <div class="container clearfix">
                        	 <!-- User info -->
                        	<div id="header_user_info">
                                {if $logged}
                                    <a href="{$link->getPageLink('index', true, NULL, "mylogout")|escape:'html'}" title="{l s='Log me out' mod='blockuserinfo'}" class="logout" rel="nofollow">{l s='Sign out' mod='blockuserinfo'}</a>
                                {else}
                                    <a href="{$link->getPageLink('my-account', true)|escape:'html'}" title="{l s='Login to your customer account' mod='blockuserinfo'}" class="login" rel="nofollow">{l s='Sign in' mod='blockuserinfo'}</a>
                                {/if}
                            </div>
                            <!-- /User info -->
                            <!-- languages -->
                            {if count($languages) > 1}
                            <div id="languages-block-top">
                                <div id="countries">
                                    {foreach from=$languages key=k item=language name="languages"}
                                        {if $language.iso_code == $lang_iso}
                                            <div class="current">
                                                <span>{$language.name}</span>
                                            </div>
                                        {/if}
                                    {/foreach}
                                    <ul id="first-languages" class="countries_ul toogle_content">
                                    {foreach from=$languages key=k item=language name="languages"}
                                        <li {if $language.iso_code == $lang_iso}class="selected"{/if}>
                                        {if $language.iso_code != $lang_iso}
                                            {assign var=indice_lang value=$language.id_lang}
                                            {if isset($lang_rewrite_urls.$indice_lang)}
                                                <a href="{$lang_rewrite_urls.$indice_lang|escape:htmlall}" title="{$language.name}">
                                            {else}
                                                <a href="{$link->getLanguageLink($language.id_lang)|escape:htmlall}" title="{$language.name}">
                            
                                            {/if}
                                        {/if}
                                                <span>{$language.name}</span>
                                        {if $language.iso_code != $lang_iso}
                                            </a>
                                        {/if}
                                        </li>
                                    {/foreach}
                                    </ul>
                                </div>
                            </div>
                            <script type="text/javascript">
								$(document).ready(function(){
									$('#countries .current span, #countries .countries_ul li span').each(function() {
										var h = $(this).html();
										var index = h.indexOf(' ');
											if(index == -1) {
												index = h.length;
											}
										$(this).html('<span class="firstWord">'+ h.substring(index, h.length) + '</span>' + h.substring(0, index));
									});
								}); 
							</script>
                            {/if}
                            <!-- /languages -->
                            <!-- Currencies -->
                            {if count($currencies) > 1}
                                <div id="currencies-block-top">
                                    <form id="setCurrency" action="{$request_uri}" method="post">
                                        <div class="current">
                                            <input type="hidden" name="id_currency" id="id_currency" value=""/>
                                            <input type="hidden" name="SubmitCurrency" value="" />
                                            <span class="cur-label">{l s='Currency' mod='blockcurrencies'} :</span>
                                            {foreach from=$currencies key=k item=f_currency}
                                                {if $cookie->id_currency == $f_currency.id_currency}<strong>{$f_currency.iso_code}</strong>{/if}
                                            {/foreach}
                                        </div>
                                        <ul id="first-currencies" class="currencies_ul toogle_content">
                                            {foreach from=$currencies key=k item=f_currency}
                                                <li {if $cookie->id_currency == $f_currency.id_currency}class="selected"{/if}>
                                                    <a href="javascript:setCurrency({$f_currency.id_currency});" title="{$f_currency.name}" rel="nofollow">{$f_currency.name}</a>
                                                </li>
                                            {/foreach}
                                        </ul>
                                    </form>
                                </div>
                            {/if}
							<!-- /Currencies -->
                            <!--Contact link-->
                            <div id="contact-link">
                                <a href="{$link->getPageLink('contact', true)|escape:'html'}" title="{l s='contact' mod='blockpermanentlinks'}">{l s='Contact Us' mod='blockpermanentlinks'}</a>
                            </div>
                            <span class="shop-phone"><i class="icon-phone"></i>{l s='Call us now toll free:'} <strong>{$shop_phone}(800) 2345-6789</strong></span>
                        </div>
                    </div>
                	<div class="container header-row-2">
                        <a id="header_logo" href="{$base_dir}" title="{$shop_name|escape:'htmlall':'UTF-8'}">
                            <img class="logo img-responsive" src="{$logo_url}" alt="{$shop_name|escape:'htmlall':'UTF-8'}" {if $logo_image_width}width="{$logo_image_width}"{/if} {if $logo_image_height}height="{$logo_image_height}" {/if}/>
                        </a>
                        <div id="header_right">
                            {$HOOK_TOP}
                        </div>
                    </div>
                </header>
            </div>
            <div class="columns-container">
                <div id="columns" class="container">
                    {if $page_name !='index' && $page_name !='pagenotfound'}
                        {include file="$tpl_dir./breadcrumb.tpl"}
                    {/if}
                    <div class="row">
                        {assign var='LeftColumn' value=0}
                        {assign var='RightColumn' value=0}
                        {if isset($HOOK_LEFT_COLUMN) && (str_replace(" ","",$HOOK_LEFT_COLUMN)) !=''}{assign var='LeftColumn' value=3}{/if}
                        {if isset($HOOK_RIGHT_COLUMN) && (str_replace(" ","",$HOOK_RIGHT_COLUMN)) !=''}{assign var='RightColumn' value=3}{/if}
                    <!-- Left -->
                        {if isset($LeftColumn) && $LeftColumn !=0}
                            <div id="left_column" class="column col-xs-12 col-sm-3">
                                {$HOOK_LEFT_COLUMN}
                            </div>
                        {/if}
                    <!-- Center -->
                    	<div id="center_column" class="center_column col-xs-12 col-sm-{12 - $LeftColumn - $RightColumn}">
	{/if}
