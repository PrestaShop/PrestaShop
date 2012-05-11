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
*  @version  Release: $Revision: 6844 $
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
	<div id="pagination" class="pagination">
	{if $start!=$stop}
		<ul class="pagination">
		{if $p != 1}
			{assign var='p_previous' value=$p-1}
			<li id="pagination_previous"><a {$no_follow_text} href="{$link->goPage($requestPage, $p_previous)}">&laquo;&nbsp;{l s='Previous'}</a></li>
		{else}
			<li id="pagination_previous" class="disabled"><span>&laquo;&nbsp;{l s='Previous'}</span></li>
		{/if}
		{if $start==3}
			<li><a {$no_follow_text}  href="{$link->goPage($requestPage, 1)}">1</a></li>
			<li><a {$no_follow_text}  href="{$link->goPage($requestPage, 2)}">2</a></li>
		{/if}
		{if $start==2}
			<li><a {$no_follow_text}  href="{$link->goPage($requestPage, 1)}">1</a></li>
		{/if}
		{if $start>3}
			<li><a {$no_follow_text}  href="{$link->goPage($requestPage, 1)}">1</a></li>
			<li class="truncate">...</li>
		{/if}
		{section name=pagination start=$start loop=$stop+1 step=1}
			{if $p == $smarty.section.pagination.index}
				<li class="current"><span>{$p|escape:'htmlall':'UTF-8'}</span></li>
			{else}
				<li><a {$no_follow_text} href="{$link->goPage($requestPage, $smarty.section.pagination.index)}">{$smarty.section.pagination.index|escape:'htmlall':'UTF-8'}</a></li>
			{/if}
		{/section}
		{if $pages_nb>$stop+2}
			<li class="truncate">...</li>
			<li><a href="{$link->goPage($requestPage, $pages_nb)}">{$pages_nb|intval}</a></li>
		{/if}
		{if $pages_nb==$stop+1}
			<li><a href="{$link->goPage($requestPage, $pages_nb)}">{$pages_nb|intval}</a></li>
		{/if}
		{if $pages_nb==$stop+2}
			<li><a href="{$link->goPage($requestPage, $pages_nb-1)}">{$pages_nb-1|intval}</a></li>
			<li><a href="{$link->goPage($requestPage, $pages_nb)}">{$pages_nb|intval}</a></li>
		{/if}
		{if $pages_nb > 1 AND $p != $pages_nb}
			{assign var='p_next' value=$p+1}
			<li id="pagination_next"><a {$no_follow_text} href="{$link->goPage($requestPage, $p_next)}">{l s='Next'}&nbsp;&raquo;</a></li>
		{else}
			<li id="pagination_next" class="disabled"><span>{l s='Next'}&nbsp;&raquo;</span></li>
		{/if}
		</ul>
	{/if}
	{if $nb_products > $products_per_page}
		<form action="{if !is_array($requestNb)}{$requestNb}{else}{$requestNb.requestUrl}{/if}" method="get" class="pagination">
			<p>
				{if isset($search_query) AND $search_query}<input type="hidden" name="search_query" value="{$search_query|escape:'htmlall':'UTF-8'}" />{/if}
				{if isset($tag) AND $tag AND !is_array($tag)}<input type="hidden" name="tag" value="{$tag|escape:'htmlall':'UTF-8'}" />{/if}
				<input type="submit" class="button_mini" value="{l s='OK'}" />
				<label for="nb_item">{l s='items:'}</label>
				<select name="n" id="nb_item">
				{assign var="lastnValue" value="0"}
				{foreach from=$nArray item=nValue}
					{if $lastnValue <= $nb_products}
						<option value="{$nValue|escape:'htmlall':'UTF-8'}" {if $n == $nValue}selected="selected"{/if}>{$nValue|escape:'htmlall':'UTF-8'}</option>
					{/if}
					{assign var="lastnValue" value=$nValue}
				{/foreach}
				</select>
				{if is_array($requestNb)}
					{foreach from=$requestNb item=requestValue key=requestKey}
						{if $requestKey != 'requestUrl'}
							<input type="hidden" name="{$requestKey|escape:'htmlall':'UTF-8'}" value="{$requestValue|escape:'htmlall':'UTF-8'}" />
						{/if}
					{/foreach}
				{/if}
			</p>
		</form>
	{/if}
	</div>
	<!-- /Pagination -->
{/if}
