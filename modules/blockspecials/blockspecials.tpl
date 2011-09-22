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
*  @version  Release: $Revision: 7077 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<!-- MODULE Block specials -->
<div id="special_block_right" class="block products_block exclusive blockspecials">
	<h4><a href="{$link->getPageLink('prices-drop')}" title="{l s='Specials' mod='blockspecials'}">{l s='Specials' mod='blockspecials'}</a></h4>
	<div class="block_content">

{if $blockspecials_special}
		<ul class="products">
			<li class="product_image">
				<a href="{$blockspecials_special.link}"><img src="{$link->getImageLink($blockspecials_special.link_rewrite, $blockspecials_special.id_image, 'medium')}" alt="{$blockspecials_special.legend|escape:html:'UTF-8'}" height="{$blockspecials_mediumSize.height}" width="{$blockspecials_mediumSize.width}" title="{$blockspecials_special.name|escape:html:'UTF-8'}" /></a>
			</li>
			<li>

				<h5><a href="{$blockspecials_special.link}" title="{$blockspecials_special.name|escape:html:'UTF-8'}">{$blockspecials_special.name|escape:html:'UTF-8'}</a></h5>
				<span class="price-discount">{if !$priceDisplay}{displayWtPrice p=$blockspecials_special.price_without_reduction}{else}{displayWtPrice p=$blockspecials_priceWithoutReduction_tax_excl}{/if}</span>
    			{if $blockspecials_special.specific_prices}
        			{assign var='specific_prices' value=$blockspecials_special.specific_prices}
        			{if $specific_prices.reduction_type == 'percentage' && ($specific_prices.from == $specific_prices.to OR ($smarty.now|date_format:'%Y-%m-%d %H:%M:%S' <= $specific_prices.to && $smarty.now|date_format:'%Y-%m-%d %H:%M:%S' >= $specific_prices.from))}
	        			<span class="reduction">(-{$specific_prices.reduction*100|floatval}%)</span>
	            	{/if}
	            {/if}
				<span class="price">{if !$priceDisplay}{displayWtPrice p=$blockspecials_special.price}{else}{displayWtPrice p=$blockspecials_special.price_tax_exc}{/if}</span>
			</li>
		</ul>
		<p>
			<a href="{$link->getPageLink('prices-drop')}" title="{l s='All specials' mod='blockspecials'}" class="button_large">{l s='All specials' mod='blockspecials'}</a>
		</p>
{else}
		<p>{l s='No specials at this time' mod='blockspecials'}</p>
{/if}
	</div>
</div>
<!-- /MODULE Block specials -->

