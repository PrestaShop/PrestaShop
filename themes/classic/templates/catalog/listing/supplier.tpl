{extends file='catalog/listing/product-list.tpl'}

{block name='product_list_header'}
  <h1>{l s='List of products by supplier %s' sprintf=$supplier.name d='Shop.Theme.Catalog'}</h1>
  <div id="supplier-description">{$supplier.description nofilter}</div>
{/block}
