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

{ldelim}
"products": [
{if $products}
{foreach from=$products item=product name='products'}
{assign var='productId' value=$product.id_product}
{assign var='productAttributeId' value=$product.id_product_attribute}
	{ldelim}
		"id":            {$product.id_product},
		"link":          "{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category)|addslashes}",
		"quantity":      {$product.cart_quantity},
		"priceByLine":   "{if $priceDisplay == $smarty.const.PS_TAX_EXC}{displayWtPrice|html_entity_decode:2:'UTF-8' p=$product.total}{else}{displayWtPrice|html_entity_decode:2:'UTF-8' p=$product.total_wt}{/if}",
		"name":          "{$product.name|html_entity_decode:2:'UTF-8'|escape|truncate:15:'...':true}",
		"price":         "{if $priceDisplay == $smarty.const.PS_TAX_EXC}{displayWtPrice|html_entity_decode:2:'UTF-8' p=$product.total}{else}{displayWtPrice|html_entity_decode:2:'UTF-8' p=$product.total_wt}{/if}",
		"idCombination": {if isset($product.attributes_small)}{$productAttributeId}{else}0{/if},
{if isset($product.attributes_small)}
		"hasAttributes": true,
		"attributes":    "{$product.attributes_small|addslashes}",
{else}
		"hasAttributes": false,
{/if}
		"hasCustomizedDatas": {if isset($customizedDatas.$productId.$productAttributeId)}true{else}false{/if},

		"customizedDatas":[
		{if isset($customizedDatas.$productId.$productAttributeId)}
		{foreach from=$customizedDatas.$productId.$productAttributeId key='id_customization' item='customization' name='customizedDatas'}{ldelim}
{* This empty line was made in purpose (product addition debug), please leave it here *}

			"customizationId":	{$id_customization},
			"quantity":			"{$customization.quantity}",
			"datas": [
				{foreach from=$customization.datas key='type' item='datas' name='customization'}
				{ldelim}
					"type":	"{$type}",
					"datas":
					[
					{foreach from=$datas key='index' item='data' name='datas'}
						{ldelim}
						"index":			{$index},
						"value":			"{$data.value|addslashes}",
						"truncatedValue":	"{$data.value|truncate:28:'...'|addslashes}"
						{rdelim}{if !$smarty.foreach.datas.last},{/if}
					{/foreach}]
				{rdelim}{if !$smarty.foreach.customization.last},{/if}
				{/foreach}
			]
		{rdelim}{if !$smarty.foreach.customizedDatas.last},{/if}
		{/foreach}
		{/if}
		]


	{rdelim}{if !$smarty.foreach.products.last},{/if}
{/foreach}{/if}
],

"discounts": [
{if $discounts}{foreach from=$discounts item=discount name='discounts'}
	{ldelim}
		"id":              "{$discount.id_discount}",
		"name":            "{$discount.name|cat:' : '|cat:$discount.description|truncate:18:'...'|addslashes}",
		"description":     "{$discount.description|addslashes}",
		"nameDescription": "{$discount.name|cat:' : '|cat:$discount.description|truncate:18:'...'}",
		"link":            "{$link->getPageLink('order.php', true)}?deleteDiscount={$discount.id_discount}",
		"price":           "-{if $discount.value_real != '!'}{if $priceDisplay == 1}{convertPrice|html_entity_decode:2:'UTF-8' price=$discount.value_tax_exc}{else}{convertPrice|html_entity_decode:2:'UTF-8' price=$discount.value_real}{/if}{/if}"
	{rdelim}
	{if !$smarty.foreach.discounts.last},{/if}
{/foreach}{/if}
],

"shippingCost": "{$shipping_cost|html_entity_decode:2:'UTF-8'}",
{if isset($tax_cost)}
"taxCost": "{$tax_cost|html_entity_decode:2:'UTF-8'}",
{/if}
"wrappingCost": "{$wrapping_cost|html_entity_decode:2:'UTF-8'}",
"nbTotalProducts": "{$nb_total_products}",
"total": "{$total|html_entity_decode:2:'UTF-8'}",
"productTotal": "{$product_total|html_entity_decode:2:'UTF-8'}",

{if isset($errors) && $errors}
"hasError" : true,
"errors" : [
{foreach from=$errors key=k item=error name='errors'}
	"{$error|addslashes|html_entity_decode:2:'UTF-8'}"
	{if !$smarty.foreach.errors.last},{/if}
{/foreach}
]
{else}
"hasError" : false
{/if}

{rdelim}
