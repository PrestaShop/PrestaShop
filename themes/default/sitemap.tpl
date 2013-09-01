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

{capture name=path}{l s='Sitemap'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h1>{l s='Sitemap'}</h1>
<div id="sitemap_content" class="clearfix">
	<div class="sitemap_block">
		<h3>{l s='Our offers'}</h3>
		<ul>
			<li><a href="{$link->getPageLink('new-products')|escape:'html'}" title="{l s='View a new product'}">{l s='New products'}</a></li>
			{if !$PS_CATALOG_MODE}
			<li><a href="{$link->getPageLink('best-sales')|escape:'html'}" title="{l s='View best seller products'}">{l s='Best sellers'}</a></li>
			<li><a href="{$link->getPageLink('prices-drop')|escape:'html'}" title="{l s='View products with a price drop'}">{l s='Price drop'}</a></li>
			{/if}
			{if $display_manufacturer_link OR $PS_DISPLAY_SUPPLIERS}<li><a href="{$link->getPageLink('manufacturer')|escape:'html'}" title="{l s='View a list of manufacturers'}">{l s='Manufacturers'}</a></li>{/if}
			{if $display_supplier_link OR $PS_DISPLAY_SUPPLIERS}<li><a href="{$link->getPageLink('supplier')|escape:'html'}" title="{l s='View a list of suppliers'}">{l s='Suppliers'}</a></li>{/if}
		</ul>
	</div>
	<div class="sitemap_block">
		<h3>{l s='Custommer account'}</h3>
		<ul>
		{if $logged}
			<li><a href="{$link->getPageLink('my-account', true)|escape:'html'}" title="{l s='Manage your customer account'}" rel="nofollow">{l s='Your Account'}</a></li>
			<li><a href="{$link->getPageLink('identity', true)|escape:'html'}" title="{l s='Manage your personal account information'}" rel="nofollow">{l s='My personal information'}</a></li>
			<li><a href="{$link->getPageLink('addresses', true)|escape:'html'}" title="{l s='Manage your shipping and billing addresses'}" rel="nofollow">{l s='My addresses'}</a></li>
			<li><a href="{$link->getPageLink('history', true)|escape:'html'}" title="{l s='View list of the orders you\'ve created'}" rel="nofollow">{l s='Order history and details'}</a></li>
{*
			{if $returnAllowed}
				<li><a href="{$link->getPageLink('order-follow', true)|escape:'html'}" title="{l s='View list of your merchandise returns'}" rel="nofollow">{l s='My merchandise returns'}</a></li>
			{/if}
*}
			<li><a href="{$link->getPageLink('order-slip', true)|escape:'html'}" title="{l s='View list of your credit slips'}" rel="nofollow">{l s='My credit slips'}</a></li>
			{if $voucherAllowed}
			<li><a href="{$link->getPageLink('discount', true)|escape:'html'}" title="{l s='View list of your discount vouchers'}" rel="nofollow">{l s='My discount vouchers'}</a></li>
			{/if}
			<li><a href="{$link->getPageLink('index', true, NULL, "mylogout")|escape:'html'}" title="{l s='Log out from customer account'}" rel="nofollow">{l s='Log out'}</a></li>
		{else}
			<li><a href="{$link->getPageLink('authentication', true)|escape:'html'}" title="{l s='Login into your customer account'}" rel="nofollow">{l s='Login'}</a></li>
			<li><a href="{$link->getPageLink('password', true)|escape:'html'}" title="{l s='Recovery your lost password'}" rel="nofollow">{l s='Recovery lost password'}</a></li>
			<li><a href="{$link->getPageLink('authentication', true)|escape:'html'}" title="{l s='Create a new customer account'}" rel="nofollow">{l s='Create a new account'}</a></li>
		{/if}
		</ul>
	</div>
	<br class="clear" />
</div>
<div id="listpage_content">
	<div class="categTree">
		<h3>{l s='Product categories'}</h3>
		<div class="tree_top"><a href="{$base_dir_ssl}" title="{$categoriesTree.name|escape:'htmlall':'UTF-8'}">{$categoriesTree.name|escape:'htmlall':'UTF-8'}</a></div>
		<ul class="tree">
		{if isset($categoriesTree.children)}
			{foreach $categoriesTree.children as $child}
				{if $child@last}
					{include file="$tpl_dir./category-tree-branch.tpl" node=$child last='true'}
				{else}
					{include file="$tpl_dir./category-tree-branch.tpl" node=$child}
				{/if}
			{/foreach}
		{/if}
		</ul>
	</div>
	<div class="categTree">
		<h3>{l s='Information pages'}</h3>
		<div class="tree_top"><a href="{$categoriescmsTree.link}" title="{l s='Page category:'} {$categoriescmsTree.name|escape:'htmlall':'UTF-8'}">{$categoriescmsTree.name|escape:'htmlall':'UTF-8'}</a></div>
		<ul class="tree">
			{if isset($categoriescmsTree.children)}
				{foreach $categoriescmsTree.children as $child}
					{if (isset($child.children) && $child.children|@count > 0) || $child.cms|@count > 0}
						{include file="$tpl_dir./category-cms-tree-branch.tpl" node=$child}
					{/if}
				{/foreach}
			{/if}
			{foreach from=$categoriescmsTree.cms item=cms name=cmsTree}
				<li><a href="{$cms.link|escape:'htmlall':'UTF-8'}" title="{l s='Page:'} {$cms.meta_title|escape:'htmlall':'UTF-8'}">{$cms.meta_title|escape:'htmlall':'UTF-8'}</a></li>
			{/foreach}
			{if $display_store}<li><a href="{$link->getPageLink('stores')|escape:'html'}" title="{l s='List of our stores'}">{l s='Our stores'}</a></li>{/if}
			<li class="last"><a href="{$link->getPageLink('contact', true)|escape:'html'}" title="{l s='Do you need help? Contact us.'}">{l s='Contact and customer support'}</a></li>
		</ul>
	</div>
</div>
