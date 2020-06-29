{*
 * This file allows you to customize your new-product page.
 * You can safely remove it if you want it to appear exactly like all other product listing pages
 *}
{extends file='catalog/listing/product-list.tpl'}

{block name='product_list_header'}
  <h1>{l s='Products on sale' d='Shop.Theme.Catalog'}</h1>
  <p>{l s='Take advantage of our exclusive offers. Once we sell these products out, they are gone forever!' d='Shop.Theme.Catalog'}
{/block}