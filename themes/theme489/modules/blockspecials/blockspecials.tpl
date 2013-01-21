<!-- MODULE Block specials -->
<div id="specials" class="block products_block">
	<h4><a href="{$link->getPageLink('prices-drop')}" title="{l s='Specials' mod='blockspecials'}">{l s='Specials' mod='blockspecials'}</a></h4>
	<div class="block_content">
{if $special}
		<ul>
			<li>
				<a class="products_block_img bordercolor" href="{$special.link}"><img src="{$link->getImageLink($special.link_rewrite, $special.id_image, 'small_default')}" alt="{$special.legend|escape:html:'UTF-8'}" title="{$special.name|escape:html:'UTF-8'}" /></a>
				<div>
				<h5><a class="product_link" href="{$special.link}" title="{$special.name|escape:html:'UTF-8'}">{$special.name|escape:html:'UTF-8'}</a></h5>
				<span class="pricecolor price-discount">{if !$priceDisplay}{displayWtPrice p=$special.price_without_reduction}{else}{displayWtPrice p=$priceWithoutReduction_tax_excl}{/if}</span>
            	{if !$PS_CATALOG_MODE}
					{if $special.specific_prices}
						{assign var='specific_prices' value=$special.specific_prices}
						{if $specific_prices.reduction_type == 'percentage' && ($specific_prices.from == $specific_prices.to OR ($smarty.now|date_format:'%Y-%m-%d %H:%M:%S' <= $specific_prices.to && $smarty.now|date_format:'%Y-%m-%d %H:%M:%S' >= $specific_prices.from))}
							<span class="pricecolor  reduction">(-{$specific_prices.reduction*100|floatval}%)</span></span>
						{/if}
					{/if}
				{/if}
            	<span class="pricecolor price">{if !$priceDisplay}{displayWtPrice p=$special.price}{else}{displayWtPrice p=$special.price_tax_exc}{/if}</span>
				</div>
			</li>
		</ul>
		<a class="button_large" href="{$link->getPageLink('prices-drop')}" title="{l s='All specials' mod='blockspecials'}">{l s='All specials' mod='blockspecials'}</a>
{else}
		<p>{l s='No specials at this time' mod='blockspecials'}</p>
{/if}
	</div>
</div>
<!-- /MODULE Block specials -->