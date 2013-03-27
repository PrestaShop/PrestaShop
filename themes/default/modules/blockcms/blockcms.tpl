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

{if $block == 1}
	<!-- Block CMS module -->
	{foreach from=$cms_titles key=cms_key item=cms_title}
		<div id="informations_block_left_{$cms_key}" class="block informations_block_left">
			<p class="title_block"><a href="{$cms_title.category_link}">{if !empty($cms_title.name)}{$cms_title.name}{else}{$cms_title.category_name}{/if}</a></p>
			<ul class="block_content">
				{foreach from=$cms_title.categories item=cms_page}
					{if isset($cms_page.link)}<li class="bullet"><b style="margin-left:2em;">
					<a href="{$cms_page.link}" title="{$cms_page.name|escape:html:'UTF-8'}">{$cms_page.name|escape:html:'UTF-8'}</a>
					</b></li>{/if}
				{/foreach}
				{foreach from=$cms_title.cms item=cms_page}
					{if isset($cms_page.link)}<li><a href="{$cms_page.link}" title="{$cms_page.meta_title|escape:html:'UTF-8'}">{$cms_page.meta_title|escape:html:'UTF-8'}</a></li>{/if}
				{/foreach}
				{if $cms_title.display_store}<li><a href="{$link->getPageLink('stores')}" title="{l s='Our stores' mod='blockcms'}">{l s='Our stores' mod='blockcms'}</a></li>{/if}
			</ul>
		</div>
	{/foreach}
	<!-- /Block CMS module -->
{else}
	<!-- MODULE Block footer -->
	<div class="block_various_links" id="block_various_links_footer">
		<p class="title_block">{l s='Information' mod='blockcms'}</p>
		<ul>
			{if !$PS_CATALOG_MODE}<li class="first_item"><a href="{$link->getPageLink('prices-drop')}" title="{l s='Specials' mod='blockcms'}">{l s='Specials' mod='blockcms'}</a></li>{/if}
			<li class="{if $PS_CATALOG_MODE}first_{/if}item"><a href="{$link->getPageLink('new-products')}" title="{l s='New products' mod='blockcms'}">{l s='New products' mod='blockcms'}</a></li>
			{if !$PS_CATALOG_MODE}<li class="item"><a href="{$link->getPageLink('best-sales')}" title="{l s='Top sellers' mod='blockcms'}">{l s='Top sellers' mod='blockcms'}</a></li>{/if}
			{if $display_stores_footer}<li class="item"><a href="{$link->getPageLink('stores')}" title="{l s='Our stores' mod='blockcms'}">{l s='Our stores' mod='blockcms'}</a></li>{/if}
			<li class="item"><a href="{$link->getPageLink($contact_url, true)}" title="{l s='Contact us' mod='blockcms'}">{l s='Contact us' mod='blockcms'}</a></li>
			{foreach from=$cmslinks item=cmslink}
				{if $cmslink.meta_title != ''}
					<li class="item"><a href="{$cmslink.link|addslashes}" title="{$cmslink.meta_title|escape:'htmlall':'UTF-8'}">{$cmslink.meta_title|escape:'htmlall':'UTF-8'}</a></li>
				{/if}
			{/foreach}
			<li><a href="{$link->getPageLink('sitemap')}" title="{l s='sitemap' mod='blockcms'}">{l s='Sitemap' mod='blockcms'}</a></li>
			{if $display_poweredby}<li class="last_item">{l s='Powered by' mod='blockcms'} <a class="_blank" href="http://www.prestashop.com">PrestaShop</a>&trade;</li>{/if}
		</ul>
	{$footer_text}
	</div>
	<!-- /MODULE Block footer -->
{/if}
