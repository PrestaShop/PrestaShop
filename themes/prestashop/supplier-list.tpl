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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{capture name=path}{l s='Suppliers'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h1>{l s='Suppliers'}</h1>

{if isset($errors) AND $errors}
	{include file="$tpl_dir./errors.tpl"}
{else}

	<p>{strip}
		<span class="bold">
			{if $nbSuppliers == 0}{l s='There are no suppliers.'}
			{else}
				{if $nbSuppliers == 1}{l s='There is'}{else}{l s='There are'}{/if}&#160;
				{$nbSuppliers}&#160;
				{if $nbSuppliers == 1}{l s='supplier.'}{else}{l s='suppliers.'}{/if}
			{/if}
		</span>{/strip}
	</p>

{if $nbSuppliers > 0}
	<ul id="suppliers_list">
	{foreach from=$suppliers item=supplier name=suppliers}
		<li class="{if $smarty.foreach.suppliers.first}first_item{elseif $smarty.foreach.suppliers.last}last_item{else}item{/if}"> 
			<div class="left_side">
				<!-- logo -->
				<div class="logo">
				{if $supplier.nb_products > 0}
				<a href="{$link->getsupplierLink($supplier.id_supplier, $supplier.link_rewrite)|escape:'htmlall':'UTF-8'}" title="{$supplier.name|escape:'htmlall':'UTF-8'}">
				{/if}
					<img src="{$img_sup_dir}{$supplier.image|escape:'htmlall':'UTF-8'}-medium.jpg" alt="" width="{$mediumSize.width}" height="{$mediumSize.height}" />
				{if $supplier.nb_products > 0}
				</a>
				{/if}
				</div>

				<!-- name -->
				<h3>
					{if $supplier.nb_products > 0}
					<a href="{$link->getsupplierLink($supplier.id_supplier, $supplier.link_rewrite)|escape:'htmlall':'UTF-8'}">
					{/if}
					{$supplier.name|truncate:60:'...'|escape:'htmlall':'UTF-8'}
					{if $supplier.nb_products > 0}
					</a>
					{/if}
				</h3>
				<p class="description">
				{if $supplier.nb_products > 0}
					<a href="{$link->getsupplierLink($supplier.id_supplier, $supplier.link_rewrite)|escape:'htmlall':'UTF-8'}">
				{/if}
				{$supplier.description|escape:'htmlall':'UTF-8'}
				{if $supplier.nb_products > 0}
				</a>
				{/if}
				</p>

			</div>

			<div class="right_side">
			
			{if $supplier.nb_products > 0}
				<a href="{$link->getsupplierLink($supplier.id_supplier, $supplier.link_rewrite)|escape:'htmlall':'UTF-8'}">
			{/if}
				<span>{$supplier.nb_products|intval} {if $supplier.nb_products == 1}{l s='product'}{else}{l s='products'}{/if}</span>
			{if $supplier.nb_products > 0}
				</a>
			{/if}

			{if $supplier.nb_products > 0}
				<a class="button" href="{$link->getsupplierLink($supplier.id_supplier, $supplier.link_rewrite)|escape:'htmlall':'UTF-8'}">{l s='view products'}</a>
			{/if}

			</div>
			<br class="clear"/>
		</li>
	{/foreach}
	</ul>
	{include file="$tpl_dir./pagination.tpl"}
{/if}
{/if}
