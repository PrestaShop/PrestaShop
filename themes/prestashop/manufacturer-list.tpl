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

{capture name=path}{l s='Manufacturers'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h1>{l s='Manufacturers'}</h1>

{if isset($errors) AND $errors}
	{include file="$tpl_dir./errors.tpl"}
{else}
	<p>{strip}
		<span class="bold">
			{if $nbManufacturers == 0}{l s='There are no manufacturers.'}
			{else}
				{if $nbManufacturers == 1}{l s='There is'}{else}{l s='There are'}{/if}&#160;
				{$nbManufacturers}&#160;
				{if $nbManufacturers == 1}{l s='manufacturer.'}{else}{l s='manufacturers.'}{/if}
			{/if}
		</span>{/strip}
	</p>

	{if $nbManufacturers > 0}
		<ul id="manufacturers_list">
		{foreach from=$manufacturers item=manufacturer name=manufacturers}
			<li class="{if $smarty.foreach.manufacturers.first}first_item{elseif $smarty.foreach.manufacturers.last}last_item{else}item{/if}"> 
				<div class="left_side">
					<!-- logo -->
					<div class="logo">
					{if $manufacturer.nb_products > 0}<a href="{$link->getmanufacturerLink($manufacturer.id_manufacturer, $manufacturer.link_rewrite)|escape:'htmlall':'UTF-8'}" title="{$manufacturer.name|escape:'htmlall':'UTF-8'}">{/if}
						<img src="{$img_manu_dir}{$manufacturer.image|escape:'htmlall':'UTF-8'}-medium.jpg" alt="" width="{$mediumSize.width}" height="{$mediumSize.height}" />
					{if $manufacturer.nb_products > 0}</a>{/if}
					</div>
					<!-- name -->
					<h3>
						{if $manufacturer.nb_products > 0}<a href="{$link->getmanufacturerLink($manufacturer.id_manufacturer, $manufacturer.link_rewrite)|escape:'htmlall':'UTF-8'}">{/if}
						{$manufacturer.name|truncate:60:'...'|escape:'htmlall':'UTF-8'}
						{if $manufacturer.nb_products > 0}</a>{/if}
					</h3>
					<p class="description rte">
					{if $manufacturer.nb_products > 0}<a href="{$link->getmanufacturerLink($manufacturer.id_manufacturer, $manufacturer.link_rewrite)|escape:'htmlall':'UTF-8'}">{/if}
					{$manufacturer.description}
					{if $manufacturer.nb_products > 0}</a>{/if}
					</p>
				</div>

				<div class="right_side">
				{if $manufacturer.nb_products > 0}<a href="{$link->getmanufacturerLink($manufacturer.id_manufacturer, $manufacturer.link_rewrite)|escape:'htmlall':'UTF-8'}">{/if}
					<span>{$manufacturer.nb_products|intval} {if $manufacturer.nb_products == 1}{l s='product'}{else}{l s='products'}{/if}</span>
				{if $manufacturer.nb_products > 0}</a>{/if}

				{if $manufacturer.nb_products > 0}
					<a class="button" href="{$link->getmanufacturerLink($manufacturer.id_manufacturer, $manufacturer.link_rewrite)|escape:'htmlall':'UTF-8'}">{l s='view products'}</a>
				{/if}
				</div>
				<br class="clear"/>
			</li>
		{/foreach}
		</ul>
		{include file="$tpl_dir./pagination.tpl"}
	{/if}
{/if}
