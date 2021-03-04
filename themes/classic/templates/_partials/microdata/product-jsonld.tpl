{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}
<script type="application/ld+json">
  {
    "@context": "https://schema.org/",
    "@type": "Product",
    "name": "{$product.name}",
    "description": "{$page.meta.description|regex_replace:"/[\r\n]/" : " "}",
    "category": "{$product.category_name}",
    {if !empty($product.cover)}"image" :"{$product.cover.bySize.home_default.url}",{/if}
    "sku": "{if $product.reference}{$product.reference}{else}{$product.id}{/if}",
    "mpn": "{if $product.mpn}{$product.mpn}{elseif $product.reference}{$product.reference}{else}{$product.id}{/if}",
    {if $product.ean13}"gtin13": "{$product.ean13}",{else if $product.upc}"gtin13": "{$product.upc}",{/if}
    {if $product_manufacturer->name OR $shop.name}"brand": {
      "@type": "Thing",
      "name": "{if $product_manufacturer->name}{$product_manufacturer->name|escape:'html':'UTF-8'}{else}{$shop.name}{/if}"
    },{/if}
    {if isset($nbComments) && $nbComments && $ratings.avg}"aggregateRating": {
      "@type": "AggregateRating",
      "ratingValue": "{$ratings.avg|round:1|escape:'html':'UTF-8'}",
      "reviewCount": "{$nbComments|escape:'html':'UTF-8'}"
    },{/if}
    {if isset($product.weight) && ($product.weight != 0)}
      "weight": {
        "@context": "https://schema.org",
        "@type": "QuantitativeValue",
        "value": "{$product.weight}",
        "unitCode": "{$product.weight_unit}"
      },{/if}
      {if $product.show_price}
        {if $product.id_product_attribute == 0}
          "offers": {
            "@type": "Offer",
            "priceCurrency": "{$currency.iso_code}",
            "name": "{$product.name|strip_tags:false}",
            "price": "{$product.price_amount}",
            "url": "{$product.url}",
            "priceValidUntil": "{($smarty.now + (int) (60*60*24*15))|date_format:"%Y-%m-%d"}",
            {if $product.images|count > 0}
              "image": {strip}[
                {foreach from=$product.images item=p_img name="p_img_list"}
                  "{$p_img.large.url}"{if not $smarty.foreach.p_img_list.last},{/if}
                {/foreach}
              ]{/strip},
            {/if}
            "sku": "{if $product.reference}{$product.reference}{else}{$product.id}{/if}",
            "mpn": "{if $product.mpn}{$product.mpn}{elseif $product.reference}{$product.reference}{else}{$product.id}{/if}",
            {if $product.ean13}"gtin13": "{$product.ean13}",{else if $product.upc}"gtin13": "0{$product.upc}",{/if}
            {if $product.condition == 'new'}"itemCondition": "https://schema.org/NewCondition",{/if}
            {if $product.show_condition > 0}
              {if $product.condition == 'used'}"itemCondition": "https://schema.org/UsedCondition",{/if}
              {if $product.condition == 'refurbished'}"itemCondition": "https://schema.org/RefurbishedCondition",{/if}
            {/if}
            "availability": "{if $product.quantity > 0 || $product.allow_oosp > 0}https://schema.org/InStock{else}https://schema.org/OutOfStock{/if}",
            "seller": {
              "@type": "Organization",
              "name": "{$shop.name}"
            }
          }
        {else}
          "offers": [
              {foreach key=id_product_combination item=combination from=$combinations}
                {
                  "@type": "Offer",
                  "priceCurrency": "{$currency.iso_code}",
                  "name": "{$product.name|strip_tags:false} - {foreach item=attr from=$combination.attributes_values}{$attr|strip_tags:false}
                    {if !$attr@last} 
                    {/if}
                  {/foreach}",
                  "price": "{Product::getPriceStatic($product->id, true, $id_product_combination)|round:'2'}",
                  "url": "{$product.url}",
                  "priceValidUntil": "{($smarty.now + (int) (60*60*24*15))|date_format:"%Y-%m-%d"}",
                  "image": "{if $combination.id_image > 0}{$link->getImageLink($product->link_rewrite, $combination.id_image, 'home_default')|escape:'html':'UTF-8'}{else}{$product.cover.bySize.home_default.url}{/if}",
                  "sku": "{$combination.reference}",
                  "mpn": "{if $combination.mpn}{$combination.mpn}{else}{$combination.reference}{/if}",
                  {if $combination.ean13}"gtin13": "{$combination.ean13}",{else if $combination.upc}"gtin13": "0{$combination.upc}",{/if}
                  {if $product.condition == 'new'}"itemCondition": "https://schema.org/NewCondition",{/if}
                  {if $product.show_condition > 0}
                    {if $product.condition == 'used'}"itemCondition": "https://schema.org/UsedCondition",{/if}
                    {if $product.condition == 'refurbished'}"itemCondition": "https://schema.org/RefurbishedCondition",{/if}
                  {/if}
                  "availability": "{if $combination.quantity > 0 || $product.allow_oosp > 0}https://schema.org/InStock{else}https://schema.org/OutOfStock{/if}",
                  "seller": {
                    "@type": "Organization",
                    "name": "{$shop.name}"}
                    } {if !$combination@last},{/if}
                  {/foreach}
                ]
              {/if}
            {/if}
          }
</script>
