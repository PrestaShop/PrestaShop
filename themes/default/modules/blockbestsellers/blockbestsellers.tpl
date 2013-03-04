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

<!-- MODULE Block best sellers -->
<div id="best-sellers_block_right" class="block products_block">
	<p class="title_block"><a href="{$link->getPageLink('best-sales')}">{l s='Top sellers' mod='blockbestsellers'}</a></p>
	<div class="block_content">
	{if $best_sellers|@count > 0}
		<ul class="product_images">
			{foreach from=$best_sellers item=product name=myLoop}
			<li class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if} clearfix">
				<a href="{$product.link}" title="{$product.legend|escape:'htmlall':'UTF-8'}" class="content_img clearfix">
					<span class="number">{$smarty.foreach.myLoop.iteration}</span>
					<img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'small_default')}" height="{$smallSize.height}" width="{$smallSize.width}" alt="{$product.legend|escape:'htmlall':'UTF-8'}" />
				
				</a>
				<p><a href="{$product.link}" title="{$product.legend|escape:'htmlall':'UTF-8'}">
					{$product.name|strip_tags:'UTF-8'|escape:'htmlall':'UTF-8'}<br />
					<span class="price">{$product.price}</span>
				</a></p>
			</li>
		{/foreach}
		</ul>
		<p class="lnk"><a href="{$link->getPageLink('best-sales')}" title="{l s='All best sellers' mod='blockbestsellers'}" class="button_large">&raquo; {l s='All best sellers' mod='blockbestsellers'}</a></p>
	{else}
		<p>{l s='No best sellers at this time' mod='blockbestsellers'}</p>
	{/if}
	</div>
</div>
<!-- /MODULE Block best sellers -->
