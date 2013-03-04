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

{capture assign='page_title'}{l s='Manufacturers:'}{/capture}
{include file='./page-title.tpl'}

<div data-role="content" id="content">

{if isset($errors) AND $errors}
	{include file="$tpl_dir./errors.tpl"}
{else}
	<p class="nbrmanufacturer">{strip}
		<span class="bold">
			{if $nbManufacturers == 0}{l s='There are no manufacturers.'}
			{else}
				{if $nbManufacturers == 1}
					{l s='There is %d manufacturer.' sprintf=$nbManufacturers}
				{else}
					{l s='There are %d manufacturers.' sprintf=$nbManufacturers}
				{/if}
			{/if}
		</span>{/strip}
	</p>

{if $nbManufacturers > 0}
	<ul id="manufacturers_list" data-role="listview">
	{foreach from=$manufacturers item=manufacturer name=manufacturers}
		<li data-corners="false" data-shadow="false" data-iconshadow="true" data-inline="false" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="clearfix {if $smarty.foreach.manufacturers.first}first_item{elseif $smarty.foreach.manufacturers.last}last_item{else}item{/if}"> 
		{if $manufacturer.nb_products > 0}<a href="{$link->getmanufacturerLink($manufacturer.id_manufacturer, $manufacturer.link_rewrite)|escape:'htmlall':'UTF-8'}" title="{$manufacturer.name|escape:'htmlall':'UTF-8'}" class="lnk_img" data-ajax="false">{/if}
			<img src="{$img_manu_dir}{$manufacturer.image|escape:'htmlall':'UTF-8'}-medium_default.jpg" alt="" width="80" />
			<h3>{$manufacturer.name|truncate:60:'...'|escape:'htmlall':'UTF-8'}</h3>
			<p>
				{if $manufacturer.nb_products == 1}
					{l s='%d product' sprintf=$manufacturer.nb_products|intval}
				{else}
					{l s='%d products' sprintf=$manufacturer.nb_products|intval}
				{/if}
			</p>
		{if $manufacturer.nb_products > 0}</a>{/if}
		</li>
	{/foreach}
	</ul>
	{include file="$tpl_dir./pagination.tpl"}
{/if}
{/if}
	{include file='./sitemap.tpl'}
</div><!-- #content -->
