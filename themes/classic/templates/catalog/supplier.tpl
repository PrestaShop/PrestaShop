{extends file=$layout}

{block name='content'}
  <section id="main">

    {block name='supplier_header'}
      <h1>{l s='List of products by supplier %name%' d='Shop.Theme.Catalog' sprintf=['%name%' => $supplier.name]}</h1>
      <div id="supplier-description">{$supplier.description nofilter}</div>
    {/block}

    {block name='supplier_products'}
      {include file='catalog/_partials/products.tpl' products=$products}
    {/block}

  </section>
{/block}
