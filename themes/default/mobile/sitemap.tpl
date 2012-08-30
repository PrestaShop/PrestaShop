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
*  @version  Release: $Revision: 6594 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div id="hook_mobile_top_site_map">
{hook h="displayMobileTopSiteMap"}
</div>
<hr width="99%" align="center" size="2" class=""/>

{if isset($categoriesTree.children)}
	<h2>{l s='Our offers'}</h2>

	<ul data-role="listview" data-inset="true">
		{for $i=0 to 4}
			{if isset($categoriesTree.children.$i)}
				<li data-icon="arrow-d">
					<a href="{$categoriesTree.children.$i.link|escape:'htmlall':'UTF-8'}" title="{$categoriesTree.children.$i.desc|escape:'htmlall':'UTF-8'}" data-ajax="false">
						{$categoriesTree.children.$i.name|escape:'htmlall':'UTF-8'}
					</a>
				</li>
			{/if}
		{/for}
		<li>
			{l s='All categories'}
			<ul data-role="listview" data-inset="true">
				{foreach $categoriesTree.children as $child}
					{include file="./category-tree-branch.tpl" node=$child last='true'}
				{/foreach}
			</ul>
		</li>
	</ul>
{/if}

<hr width="99%" align="center" size="2" class=""/>
<h2>{l s='Sitemap'}</h2>
<ul data-role="listview" data-inset="true" id="category">
	{if $controller_name != 'index'}<li><a href="{$link->getPageLink('index', true)}" data-ajax="false">{l s='Home'}</a></li>{/if}
	<li>{l s='Our offers'}
		<ul data-role="listview" data-inset="true">
			<li><a href="{$link->getPageLink('new-products')}" title="{l s='New products'}" data-ajax="false">{l s='New products'}</a></li>
			{if !$PS_CATALOG_MODE}
			<li><a href="{$link->getPageLink('prices-drop')}" title="{l s='Price drop'}" data-ajax="false">{l s='Price drop'}</a></li>
			<li><a href="{$link->getPageLink('best-sales', true)}" title="{l s='Top sellers'}" data-ajax="false">{l s='Top sellers'}</a></li>
			{/if}
			{if $display_manufacturer_link OR $PS_DISPLAY_SUPPLIERS}<li><a href="{$link->getPageLink('manufacturer')}" data-ajax="false">{l s='Manufacturers'}</a></li>{/if}
			{if $display_supplier_link OR $PS_DISPLAY_SUPPLIERS}<li><a href="{$link->getPageLink('supplier')}" data-ajax="false">{l s='Suppliers'}</a></li>{/if}
		</ul>
	</li>
	<li>{l s='Your Account'}
		<ul data-role="listview" data-inset="true">
			<li><a href="{$link->getPageLink('my-account', true)}" data-ajax="false">{l s='Your Account'}</a></li>
			<li><a href="{$link->getPageLink('identity', true)}" data-ajax="false">{l s='Personal information'}</a></li>
			<li><a href="{$link->getPageLink('addresses', true)}" data-ajax="false">{l s='Addresses'}</a></li>
			{if $voucherAllowed}<li><a href="{$link->getPageLink('discount', true)}" data-ajax="false">{l s='Discounts'}</a></li>{/if}
			<li><a href="{$link->getPageLink('history', true)}" data-ajax="false">{l s='Order history'}</a></li>
		</ul>
	</li>
	<li>{l s='Pages'}
		<ul data-role="listview" data-inset="true">
			{if isset($categoriescmsTree.children)}
				{foreach $categoriescmsTree.children as $child}
					{if (isset($child.children) && $child.children|@count > 0) || $child.cms|@count > 0}
						{include file="./category-cms-tree-branch.tpl" node=$child}
					{/if}
				{/foreach}
			{/if}
			{foreach from=$categoriescmsTree.cms item=cms name=cmsTree}
			<li><a href="{$cms.link|escape:'htmlall':'UTF-8'}" title="{$cms.meta_title|escape:'htmlall':'UTF-8'}" data-ajax="false">{$cms.meta_title|escape:'htmlall':'UTF-8'}</a></li>
			{/foreach}
			<li><a href="{$link->getPageLink('contact', true)}" data-ajax="false">{l s='Contact'}</a></li>
			{if $display_store}<li><a href="{$link->getPageLink('stores')}" title="{l s='Our stores'}" data-ajax="false">{l s='Our stores'}</a></li>{/if}
		</ul>
	</li>
</ul>
