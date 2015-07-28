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
		{if (isset($path) AND $path) && !$path|strpos:'span'}
			<span class="navigation-pipe" {if isset($category) && isset($category->id_category) && $category->id_category == 1}style="display:none;"{/if}>{$navigationPipe|escape:'html':'UTF-8'}</span>
		{/if}
	</li>
	{if isset($path) AND $path}
		{if !$path|strpos:'span'}
			{assign var=breadcrumbPosition value="<meta"|explode:$path}
			{assign var=breadcrumbPosition value=$breadcrumbPosition|@count}
			<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
				<span class="navigation_page" itemprop="item">
					<span itemprop="name">{$path}</span></a>
					{if isset($breadcrumbPosition) && $breadcrumbPosition > 0}
					<meta itemprop="position" content="{if $breadcrumbPosition == 1}{$breadcrumbPosition + 1}{else}{$breadcrumbPosition}{/if}" />
					{/if}
				</span>
			</li>
		{else}
			{$path}
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
