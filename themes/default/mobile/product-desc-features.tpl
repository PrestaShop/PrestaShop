<div data-role="content" id="more_info_block" class="clearfix">
	<div data-role="collapsible-set" data-content-theme="a">
		
		<!-- full description -->
		{if isset($product) && $product->description}
		<div data-role="collapsible" data-theme="a" data-content-theme="a">
			<h3>{l s='More info'}</h3>
			<div>{$product->description}</div>
		</div>
		{/if}
		
		<!-- product's features -->
		{if isset($features) && $features && $features|@count > 0}
		<div data-role="collapsible" data-theme="a" data-content-theme="a">
			<h3>{l s='Data sheet'}</h3>
			<ul>
				{foreach from=$features item=feature}
				{if isset($feature.value)}
					<li><span>{$feature.name|escape:'htmlall':'UTF-8'}</span> {$feature.value|escape:'htmlall':'UTF-8'}</li>
				{/if}
			{/foreach}
			</ul>
		</div>
		{/if}
		
		<!-- attachments -->
		{if isset($attachments) && $attachments}
		<div data-role="collapsible" data-theme="a" data-content-theme="a">
			<h3>{l s='Download'}</h3>
			<ul>
				{foreach from=$attachments item=attachment}
					<li><a href="{$link->getPageLink('attachment', true, NULL, "id_attachment={$attachment.id_attachment}")}" data-ajax="false">{$attachment.name|escape:'htmlall':'UTF-8'}</a><br />{$attachment.description|escape:'htmlall':'UTF-8'}</li>
				{/foreach}
			</ul>
		</div>
		{/if}
		
		<!-- accessories -->
		{if isset($accessories) && $accessories}
		<div data-role="collapsible" data-theme="a" data-content-theme="a" class="accessories_block">
			<h3>{l s='Accessories'}</h3>
			<ul>
				{foreach from=$accessories item=accessory name=accessories_list}
					{assign var='accessoryLink' value=$link->getProductLink($accessory.id_product, $accessory.link_rewrite, $accessory.category)}
					<li class="ajax_block_product {if $smarty.foreach.accessories_list.last}last_item{else}item{/if} product_accessories_description clearfix">
						<a href="{$accessoryLink|escape:'htmlall':'UTF-8'}" data-ajax="false">
							<div class="clearfix" >
								<div class="col-left" style="width:{$mediumSize.width+10}px;">
									<img src="{$link->getImageLink($accessory.link_rewrite, $accessory.id_image, 'medium_default')}" alt="{$accessory.legend|escape:'htmlall':'UTF-8'}" width="{$mediumSize.width}" height="{$mediumSize.height}" />
								</div><!-- .col-left -->
								<div class="col-right">
									<div class="inner">
										<p class="s_title_block">{$accessory.name|escape:'htmlall':'UTF-8'}</p>
										<p>{$accessory.description_short|strip_tags|truncate:70:'...'}</p>
									</div>
								</div>
							</div>
						</a>
						{if $accessory.show_price AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}
							<div class="price">
								{if $priceDisplay != 1}{displayWtPrice p=$accessory.price}{else}{displayWtPrice p=$accessory.price_tax_exc}{/if}
							</div>
						{/if}
						<div class="btn-row">
							<a class="" data-theme="a" data-role="button" data-mini="true" data-inline="true" data-icon="arrow-r" href="{$accessoryLink|escape:'htmlall':'UTF-8'}" title="{l s='View'}" data-ajax="false">{l s='View'}</a>
							{assign var="btn_more" value=""}
							{assign var="btn_href" value=""}
							{assign var="btn_class" value=""}
							{if ($accessory.allow_oosp || $accessory.quantity > 0) AND $accessory.available_for_order AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}
								{assign var="btn_href" value=$link->getPageLink('cart', true, NULL, "qty=1&amp;id_product={$accessory.id_product|intval}&amp;token={$static_token}&amp;add")}
							{else}
								{assign var="btn_class" value="disabled"}
								{capture assign="btn_more"}<span class="availability">{if (isset($accessory.quantity_all_versions) && $accessory.quantity_all_versions > 0)}{l s='Product available with different options'}{else}{if !$PS_CATALOG_MODE}{l s='Out of stock'}{/if}{/if}</span>{/capture}
							{/if}
							<a class="{$btn_class}" data-role="button" data-inline="true" data-theme="e" data-icon="plus" data-mini="true" class="exclusive button ajax_add_to_cart_button" href="{$btn_href}" rel="ajax_id_product_{$accessory.id_product|intval}" title="{l s='Add to cart'}" data-ajax="false">{l s='Add to cart'}</a>
							{$btn_more}
						</div><!-- .btn-row -->
					</li>
				{/foreach}
			</ul>
		</div>
		{/if}
	</div><!-- role:collapsible-set-->
</div><!-- #more_info_block -->
