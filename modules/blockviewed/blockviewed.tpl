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

<!-- Block Viewed products -->
<div id="viewed-products_block_left" class="block products_block">
	<h4>{l s='Viewed products' mod='blockviewed'}</h4>
	<div class="block_content">
		<ul class="products clearfix">
			{foreach from=$productsViewedObj item=viewedProduct name=myLoop}
				<li class="clearfix{if $smarty.foreach.myLoop.last} last_item{elseif $smarty.foreach.myLoop.first} first_item{else} item{/if}">
					<a href="{$link->getProductLink($viewedProduct->id, $viewedProduct->link_rewrite, $viewedProduct->category_rewrite)}" title="{l s='More about' mod='blockviewed'} {$viewedProduct->name|escape:html:'UTF-8'}"><img src="{$link->getImageLink($viewedProduct->link_rewrite, $viewedProduct->cover, 'medium')}" height="{$mediumSize.height}" width="{$mediumSize.width}" alt="{$viewedProduct->legend|escape:html:'UTF-8'}" /></a>
					<h5><a href="{$link->getProductLink($viewedProduct->id, $viewedProduct->link_rewrite, $viewedProduct->category_rewrite)}" title="{l s='More about' mod='blockviewed'} {$viewedProduct->name|escape:html:'UTF-8'}">{$viewedProduct->name|truncate:14:'...'|escape:html:'UTF-8'}</a></h5>
					<p>{m s=$viewedProduct->description_short|strip_tags:'UTF-8'|truncate:44 n=12}<a href="{$link->getProductLink($viewedProduct->id, $viewedProduct->link_rewrite, $viewedProduct->category_rewrite)}" title="{l s='More about' mod='blockviewed'} {$viewedProduct->name|escape:html:'UTF-8'}"><img src="{$img_dir}bullet.gif" alt="&gt;&gt;" width="10" height="7" /></a></p>
				</li>
			{/foreach}
		</ul>
	</div>
</div>