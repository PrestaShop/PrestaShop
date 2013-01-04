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

<div class="view_product">
	{if isset($images) && count($images) > 0}
	<!-- thumbnails -->
	<div data-role="header" class="ui-bar-a list_view">
		{assign var=image_cover value=$product->getCover($product->id)}
		{assign var=imageIds value="`$product->id`-`$image_cover.id_image`"}
		<img id="bigpic" src="{$link->getImageLink($product->link_rewrite, $imageIds, 'large_default')}" alt="{$product->name|escape:'htmlall':'UTF-8'}" />
		<div class="thumbs_list">
			<ul id="gallery" class="thumbs_list_frame clearfix">
			{foreach from=$images item=image name=thumbnails}
				{assign var=imageIds value="`$product->id`-`$image.id_image`"}
				<li id="thumbnail_{$image.id_image}">
					<img id="thumb_{$image.id_image}" src="{$link->getImageLink($product->link_rewrite, $imageIds, 'medium_default')}" alt="{$image.legend|htmlspecialchars}" height="{$mediumSize.height}" width="{$mediumSize.width}" data-large="{$link->getImageLink($product->link_rewrite, $imageIds, 'thickbox_default')}" />
				</li>
			{/foreach}
			</ul>
		</div>
	</div>
	{/if}
</div>
