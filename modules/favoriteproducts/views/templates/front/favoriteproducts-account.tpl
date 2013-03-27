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

<script type="text/javascript">
$('document').ready(function()
{
	$('img[rel^=ajax_id_favoriteproduct_]').click(function()
	{
		var idFavoriteProduct =  $(this).attr('rel').replace('ajax_id_favoriteproduct_', '');
		var parent = $(this).parent().parent();

		$.ajax({
			url: "{$link->getModuleLink('favoriteproducts', 'actions', ['process' => 'remove'], true)}",
			type: "POST",
			data: {
				'id_product': idFavoriteProduct,
				'ajax': true
			},
			success: function(result)
			{
				if (result == '0')
				{
					parent.fadeOut("normal", function()
					{
						parent.remove();
					});
				}
 		 	}
		});
	});
});
</script>

{capture name=path}
	<a href="{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8'}">
		{l s='My account' mod='favoriteproducts'}</a>
		<span class="navigation-pipe">{$navigationPipe}</span>{l s='My favorite products.' mod='favoriteproducts'}
{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<div id="favoriteproducts_block_account">
	<h2>{l s='My favorite products.' mod='favoriteproducts'}</h2>
	{if $favoriteProducts}
		<div>
			{foreach from=$favoriteProducts item=favoriteProduct}
			<div class="favoriteproduct clearfix">
				<a href="{$link->getProductLink($favoriteProduct.id_product, null, null, null, null, $favoriteProduct.id_shop)|escape:'htmlall':'UTF-8'}" class="product_img_link">
					<img src="{$link->getImageLink($favoriteProduct.link_rewrite, $favoriteProduct.image, 'medium_default')|escape:'htmlall':'UTF-8'}" alt=""/></a>
				<h3><a href="{$link->getProductLink($favoriteProduct.id_product, null, null, null, null, $favoriteProduct.id_shop)|escape:'htmlall':'UTF-8'}">{$favoriteProduct.name|escape:'htmlall':'UTF-8'}</a></h3>
				<div class="product_desc">{$favoriteProduct.description_short|strip_tags|escape:'htmlall':'UTF-8'}</div>

				<div class="remove">
					<img rel="ajax_id_favoriteproduct_{$favoriteProduct.id_product}" src="{$img_dir}icon/delete.gif" alt="" class="icon" />
				</div>
			</div>
			{/foreach}
		</div>
	{else}
		<p class="warning">{l s='No favorite products have been determined just yet. ' mod='favoriteproducts'}</p>
	{/if}

	<ul class="footer_links">
		<li class="fleft">
			<a href="{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8'}"><img src="{$img_dir}icon/my-account.gif" alt="" class="icon" /></a>
			<a href="{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8'}">{l s='Back to your account.' mod='favoriteproducts'}</a></li>
	</ul>
</div>