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

{if isset($no_follow) AND $no_follow}
	{assign var='no_follow_text' value='rel="nofollow"'}
{else}
	{assign var='no_follow_text' value=''}
{/if}

{if isset($p) AND $p}
	{if isset($smarty.get.id_category) && $smarty.get.id_category && isset($category)}
		{if !isset($current_url)}
		{assign var='requestPage' value=$link->getPaginationLink('category', $category, false, false, true, false)}
		{else}
			{assign var='requestPage' value=$current_url}
		{/if}
		{assign var='requestNb' value=$link->getPaginationLink('category', $category, true, false, false, true)}
	{elseif isset($smarty.get.id_manufacturer) && $smarty.get.id_manufacturer && isset($manufacturer)}
		{assign var='requestPage' value=$link->getPaginationLink('manufacturer', $manufacturer, false, false, true, false)}
		{assign var='requestNb' value=$link->getPaginationLink('manufacturer', $manufacturer, true, false, false, true)}
	{elseif isset($smarty.get.id_supplier) && $smarty.get.id_supplier && isset($supplier)}
		{assign var='requestPage' value=$link->getPaginationLink('supplier', $supplier, false, false, true, false)}
		{assign var='requestNb' value=$link->getPaginationLink('supplier', $supplier, true, false, false, true)}
	{else}
		{assign var='requestPage' value=$link->getPaginationLink(false, false, false, false, true, false)}
		{assign var='requestNb' value=$link->getPaginationLink(false, false, true, false, false, true)}
	{/if}
	<!-- Pagination -->
<div class="clearfix">
	<div class="pagination_mobile wrapper_pagination_mobile">
	{if $start!=$stop}
		<ul class="pagination_mobile" data-role="controlgroup" data-type="horizontal">
		{if $p != 1}
			{assign var='p_previous' value=$p-1}
		{/if}
		<li class="pagination_previous">
			<a {$no_follow_text} class="button_prev{if $p == 1} disabled{/if}" data-role="button" data-icon="arrow-l" data-iconpos="left" href="{if isset($p_previous)}{$link->goPage($requestPage, $p_previous)}{/if}" data-ajax="false">{l s='Previous'}</a>
		</li>
		{if $start>3}
			<li><a {$no_follow_text}  href="{$link->goPage($requestPage, 1)}" data-ajax="false">1</a></li>
			<li class="truncate">...</li>
		{/if}
		{section name=pagination start=$start loop=$stop+1 step=1}
			{if $p == $smarty.section.pagination.index}
				<li class="current"><a href="#" data-role="button" class="ui-btn-active" data-ajax="false">{$p|escape:'htmlall':'UTF-8'}</a></li>
			{else}
				<li><a data-role="button" {$no_follow_text} href="{$link->goPage($requestPage, $smarty.section.pagination.index)}" data-ajax="false">{$smarty.section.pagination.index|escape:'htmlall':'UTF-8'}</a></li>
			{/if}
		{/section}
		{if $pages_nb>$stop+2}
			<li class="truncate">...</li>
			<li><a href="{$link->goPage($requestPage, $pages_nb)}" data-ajax="false">{$pages_nb|intval}</a></li>
		{/if}
		{if $pages_nb > 1 AND $p != $pages_nb}
			{assign var='p_next' value=$p+1}
		{/if}
			<li class="pagination_next">
				<a {$no_follow_text} class="button_next{if !isset($p_next)} disabled{/if}" data-role="button" data-icon="arrow-r" data-iconpos="right" href="{if isset($p_next)}{$link->goPage($requestPage, $p_next)}{/if}" data-ajax="false">{l s='Next'}</a>
			</li>
		</ul>
	{/if}
	</div>
</div><!-- .clearfix -->
	<!-- /Pagination -->
{/if}
