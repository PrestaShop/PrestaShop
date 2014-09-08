{*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if $wishlists|count == 1}
<p class="buttons_bottom_block no-print">
	<a id="wishlist_button" href="#" onclick="WishlistCart('wishlist_block_list', 'add', '{$id_product|intval}', $('#idCombination').val(), document.getElementById('quantity_wanted').value); return false;" rel="nofollow"  title="{l s='Add to my wishlist' mod='blockwishlist'}">
		{l s='Add to wishlist' mod='blockwishlist'}
	</a>
</p>
{else}
<div class="buttons_bottom_block no-print">
	<div id="wishlist_button">
		<div class="btn-group">
			{foreach name=wl from=$wishlists item=wishlist}
				{if $wishlist.default == 1}
					<button type="button" class="btn btn-default" onclick="WishlistCart('wishlist_block_list', 'add', '{$id_product|intval}', $('#idCombination').val(), document.getElementById('quantity_wanted').value, {$wishlist.id_wishlist}); return false;">
						{l s='Add to %s'|sprintf:$wishlist.name mod='blockwishlist'}
					</button>
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						<span class="caret"></span>
					</button>
				{/if}
			{/foreach}
			{foreach name=wl from=$wishlists item=wishlist}
				{if $smarty.foreach.wl.first}
					<ul class="dropdown-menu" role="menu">
				{/if}
				{if $wishlist.default != 1}
					<li>
						<a onclick="WishlistCart('wishlist_block_list', 'add', '{$id_product|intval}', $('#idCombination').val(), document.getElementById('quantity_wanted').value, {$wishlist.id_wishlist}); return false;">
							{l s='Add to %s'|sprintf:$wishlist.name mod='blockwishlist'}
						</a>
					</li>
				{/if}
				{if $smarty.foreach.wl.last}
					</ul>
				{/if}
			{foreachelse}
				<a href="#" onclick="WishlistCart('wishlist_block_list', 'add', '{$id_product|intval}', $('#idCombination').val(), document.getElementById('quantity_wanted').value); return false;" rel="nofollow"  title="{l s='Add to my wishlist' mod='blockwishlist'}">
					{l s='Add to wishlist' mod='blockwishlist'}
				</a>
			{/foreach}
			</ul>
		</div>
	</div>
</div>
{/if}
