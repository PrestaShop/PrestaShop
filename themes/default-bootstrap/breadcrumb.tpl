{*
* 2007-2015 PrestaShop
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
* @author    PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2015 PrestaShop SA
* @license   http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}

<!-- Breadcrumb -->
{if isset($smarty.capture.path)}{assign var='path' value=$smarty.capture.path}{/if}
<ol class="breadcrumb clearfix" itemscope itemtype="http://schema.org/BreadcrumbList">
	<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
		<a class="home" href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}" title="{l s='Return to Home'}" itemprop="item"><i class="icon-home"></i><meta itemprop="name" content="{l s='Home'}" /><meta itemprop="position" content="1" /></a>
		{if (isset($path) AND $path)}
			<span class="navigation-pipe" >{$navigationPipe|escape:'html':'UTF-8'}</span>
		{/if}
	</li>
	{if isset($path) AND $path}
		{if isset($path.0.name)}
			{foreach from=$path item=path_element name="path"}
				{assign var=breadcrumbPosition value=$smarty.foreach.path.iteration+1}
				<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
				{if isset($path_element.link) && $path_element.link!='' && !$smarty.foreach.path.last}
					<a href="{$path_element.link}" title="{l s='Return to Home'} {$path_element.name}" class="navigation_page" itemprop="item">
				{else}
					<span class="navigation_page" itemprop="item">
				{/if}
						<span itemprop="name">{$path_element.name}</span>
						<meta itemprop="position" content="{$breadcrumbPosition}" />
				{if isset($path_element.link) && $path_element.link!=''}
					</a>
				{else}
					</span>
				{/if}
				{if !$smarty.foreach.path.last}
					<span class="navigation-pipe">{$navigationPipe|escape:'html':'UTF-8'}</span>
				{/if}
				</li>
			{/foreach}
		{else}
			<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
				<span class="navigation_page" itemprop="item">
					<span itemprop="name">{$path}</span>
					<meta itemprop="position" content="2" />
				</span>
			</li>
		{/if}
	{/if}
</ol>
{if isset($smarty.get.search_query) && isset($smarty.get.results) && $smarty.get.results > 1 && isset($smarty.server.HTTP_REFERER)}
<div class="pull-right">
	<strong>
		{capture}{if isset($smarty.get.HTTP_REFERER) && $smarty.get.HTTP_REFERER}{$smarty.get.HTTP_REFERER}{elseif isset($smarty.server.HTTP_REFERER) && $smarty.server.HTTP_REFERER}{$smarty.server.HTTP_REFERER}{/if}{/capture}
		<a href="{$smarty.capture.default|escape:'html':'UTF-8'|secureReferrer|regex_replace:'/[\?|&]content_only=1/':''}" name="back">
			<i class="icon-chevron-left left"></i> {l s='Back to Search results for "%s" (%d other results)' sprintf=[$smarty.get.search_query,$smarty.get.results]}
		</a>
	</strong>
</div>
{/if}
<!-- /Breadcrumb -->
