<div id="blockbestsellers" class="block products_block">
	<h4><a href="{$link->getPageLink('best-sales')}">{l s='Top sellers' mod='blockbestsellers'}</a></h4>
	<div class="block_content">
	{if $best_sellers|@count > 0}
	<ul>
		{foreach from=$best_sellers item=product name='myLoop'}
		{if $smarty.foreach.myLoop.iteration <= 4}
		<li class="bordercolor">
			{if $smarty.foreach.myLoop.iteration <= 1}
			<a class="products_block_img bordercolor" href="{$product.link}" title="{$product.legend|escape:html:'UTF-8'}"><img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'small_default')}" alt="{$product.legend|escape:html:'UTF-8'}" /></a>
			<div>
			{/if}
				<h5><a class="product_link" href="{$product.link}" title="{$product.name|escape:html:'UTF-8'}">{$product.name|strip_tags|escape:html:'UTF-8'|truncate:25:'...'}</a></h5>
			{if $smarty.foreach.myLoop.iteration <= 1}
				<p><a class="product_descr" href="{$product.link}" title="{$product.description_short|escape:html:'UTF-8'|truncate:30:'...'}">{$product.description_short|strip_tags|escape:html:'UTF-8'|truncate:30:'...'}</a></p>
			</div>
			{/if}
		</li>
		{/if}
		{/foreach}
	</ul>
	<a href="{$link->getPageLink('best-sales')}" title="{l s='All best sellers' mod='blockbestsellers'}" class="button_large">{l s='All best sellers' mod='blockbestsellers'}</a>
	{else}
		<p>{l s='No best sellers at this time' mod='blockbestsellers'}</p>
	{/if}
	</div>
</div>