{*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if isset($category)}
	{if $category->id AND $category->active}
{capture assign='page_title'}
	{strip}
		{$category->name|escape:'htmlall':'UTF-8'}
		{if isset($categoryNameComplement)}
			{$categoryNameComplement|escape:'htmlall':'UTF-8'}
		{/if}
	{/strip}
{/capture}
{include file='./page-title.tpl'}
	<div data-role="content" id="content">
		{if $category->description}
			<div class="category_desc clearfix">
				{if !empty($category->short_description)}
					<p>{$category->short_description}</p>
					<p class="hide_desc">{$category->description}</p>
					<a href="#" data-theme="a" data-role="button" data-mini="true" data-inline="true" data-icon="arrow-d" class="lnk_more" onclick="$(this).prev().slideDown('slow'); $(this).hide(); return false;" data-ajax="false">{l s='More'}</a>
				{else}
					<p>{$category->description}</p>
				{/if}
			</div>
			<hr class="margin_less"/>
		{/if}
		<div class="clearfix">
			{include file="./category-product-sort.tpl" container_class="container-sort"}
			<p class="nbr_result">{include file="$tpl_dir./category-count.tpl"}</p>
		</div>
		
		{* layered ? *}
		{* ===================================== *}
		{*<p><a href="layered.html" data-ajax="false">Affiner la recherche</a></p>*}
		{* ===================================== *}
		<hr class="margin_less"/>
		
		{include file="./pagination.tpl"}
		{include file="./category-product-list.tpl" products=$products}
		{include file="./pagination.tpl"}
		
		{include file='./sitemap.tpl'}
	{elseif $category->id}
		<p class="warning">{l s='This category is currently unavailable.'}</p>
	{/if}
	</div><!-- #content -->
{/if}
