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

{if count($categoryProducts) > 0 && $categoryProducts !== false}
<section class="clearfix blockproductscategory">
	<h3 class="productscategory_h3 page-product-heading">{$categoryProducts|@count} {l s='other products in the same category:' mod='productscategory'}</h3>
	<div id="{if count($categoryProducts) > 5}productscategory{else}productscategory_noscroll{/if}">

	<div id="productscategory_list">
    
		<article id="owl-carousel1" class="owl-carousel clearfix" {if count($categoryProducts) > 5}style="width: {math equation="width * nbImages" width=107 nbImages=$categoryProducts|@count}px"{/if} >
		 {foreach from=$categoryProducts item='categoryProduct' name=categoryProduct}
			<div class="product-box item" {if count($categoryProducts) < 6}{/if}>
				<a href="{$link->getProductLink($categoryProduct.id_product, $categoryProduct.link_rewrite, $categoryProduct.category, $categoryProduct.ean13)}" class="lnk_img product-image product_image" title="{$categoryProduct.name|htmlspecialchars}"><img src="{$link->getImageLink($categoryProduct.link_rewrite, $categoryProduct.id_image, 'home_default')|escape:'html'}" alt="{$categoryProduct.name|htmlspecialchars}" /></a>
                
				<h5 class="name-product">
					<a href="{$link->getProductLink($categoryProduct.id_product, $categoryProduct.link_rewrite, $categoryProduct.category, $categoryProduct.ean13)|escape:'html'}" title="{$categoryProduct.name|htmlspecialchars}">{$categoryProduct.name|truncate:14:'...'|escape:'htmlall':'UTF-8'}</a>
				</h5>
				{if $ProdDisplayPrice AND $categoryProduct.show_price == 1 AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}
				<p class="price_display">
					<span class="price">{convertPrice price=$categoryProduct.displayed_price}</span>
				</p>
				{else}
				<br />
				{/if}
			</div>
		{/foreach}
		</article>
	</div>
	</div>
	<script type="text/javascript">
		$('#productscategory_list').trigger('goto', [{$middlePosition}-3]);
	</script>
</section>
{/if}


<script type="text/javascript">
$(document).ready(function() {
 $("#owl-carousel1").owlCarousel({
		items :6,
		lazyLoad : true,
		navigation : true,
		navigationText : ["",""],
		pagination : false,
		
		dragBeforeAnimFinish : false,
		mouseDrag : false,
		touchDrag : false,

		addClassActive : true,
		transitionStyle : true
}); 

	});
	</script>