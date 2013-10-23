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

{include file="$tpl_dir./errors.tpl"}

{if !isset($errors) OR !sizeof($errors)}
	{capture assign='page_title'}{l s='Top sellers'}{/capture}
	{include file='./page-title.tpl'}

	<div data-role="content" id="content">
	{if !empty($manufacturer->description) || !empty($manufacturer->short_description)}
		<div class="category_desc clearfix">
			{if !empty($manufacturer->short_description)}
				<p>{$manufacturer->short_description}</p>
				<p class="hide_desc">{$manufacturer->description}</p>
				<a href="#" data-theme="a" data-role="button" data-mini="true" data-inline="true" data-icon="arrow-d" class="lnk_more" onclick="$(this).prev().slideDown('slow'); $(this).hide(); return false;" data-ajax="false">{l s='More'}</a>
			{else}
				<p>{$manufacturer->description}</p>
			{/if}
		</div><!-- .category_desc -->
	{/if}
	
	{if $products}
			<div class="clearfix">
				{include file="./category-product-sort.tpl" container_class="container-sort"}
			</div>
			<hr/>
			{include file="./pagination.tpl"}
			{include file="./category-product-list.tpl" products=$products}
			{include file="./pagination.tpl"}
			
	{else}
		<p class="alert alert-warning">{l s='No top sellers for the moment.'}</p>
	{/if}
		{include file='./sitemap.tpl'}
	</div><!-- #content -->
{/if}
