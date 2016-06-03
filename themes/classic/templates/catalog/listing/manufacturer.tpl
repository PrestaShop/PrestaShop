{extends file='catalog/listing/product-list.tpl'}

{block name='product_list_header'}
  <h1>{l s='List of products by manufacturer %s' sprintf=$manufacturer.name}</h1>
  <div id="manufacturer-short_description">{$manufacturer.short_description nofilter}</div>
  <div id="manufacturer-description">{$manufacturer.description nofilter}</div>
{/block}
