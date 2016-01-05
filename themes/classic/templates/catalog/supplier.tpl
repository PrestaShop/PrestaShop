{extends file=$layout}

{block name='content'}
  <section id="main">

    {block name='supplier_header'}
      <h1>{l s='List of products by supplier %s' sprintf=$supplier.name}</h1>
      <div id="supplier-description">{$supplier.description nofilter}</div>
    {/block}

    {block name='supplier_products'}
      {include file='catalog/products.tpl' products=$products}
    {/block}

  </section>
{/block}
