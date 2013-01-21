{if count($categoryProducts) > 0 && $categoryProducts !== false}
<div id="blockproductscategory" class="clearfix">
	<h2 class="bordercolor">{$categoryProducts|@count} {l s='other products in the same category:' mod='productscategory'}</h2>
	<div id="{if count($categoryProducts) > 5}productscategory{else}productscategory_noscroll{/if}">
	{if count($categoryProducts) > 5}<a id="productscategory_scroll_left" title="{l s='Previous' mod='productscategory'}" href="javascript:{ldelim}{rdelim}">{l s='Previous' mod='productscategory'}</a>{/if}
	<div id="productscategory_list">
		<ul {if count($categoryProducts) > 5}style="width: {math equation="width * nbImages - 20" width=144 nbImages=$categoryProducts|@count}px"{/if}>
			{foreach from=$categoryProducts item='categoryProduct' name=categoryProduct}
				{if count($categoryProducts) < 6}style="width: {math equation="width / nbImages" width=94 nbImages=$categoryProducts|@count}%"{/if}
			<li class="bordercolor{if $smarty.foreach.categoryProduct.last} item_last{/if}">
				<a href="{$link->getProductLink($categoryProduct.id_product, $categoryProduct.link_rewrite, $categoryProduct.category, $categoryProduct.ean13)}" class="lnk_img" title="{$categoryProduct.name|htmlspecialchars}"><img class="bordercolor" src="{$link->getImageLink($categoryProduct.link_rewrite, $categoryProduct.id_image, 'medium_default')}" alt="{$categoryProduct.name|htmlspecialchars}" /></a>
				<h5><a class="product_link" href="{$link->getProductLink($categoryProduct.id_product, $categoryProduct.link_rewrite, $categoryProduct.category, $categoryProduct.ean13)}" title="{$categoryProduct.name|htmlspecialchars}">{$categoryProduct.name|truncate:15:'...'|escape:'htmlall':'UTF-8'}</a></h5>
				{if $ProdDisplayPrice AND $categoryProduct.show_price == 1 AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}
				<p><span class="price pricecolor">{convertPrice price=$categoryProduct.displayed_price}</span></p>
				{/if}
			</li>
			{/foreach}
		</ul>
	</div>
	{if count($categoryProducts) > 5}<a id="productscategory_scroll_right" title="{l s='Next' mod='productscategory'}" href="javascript:{ldelim}{rdelim}">{l s='Next' mod='productscategory'}</a>{/if}
	</div>
	<script type="text/javascript">
		$('#productscategory_list').trigger('goto', [{$middlePosition}-3]);
	</script>
</div>
{/if}