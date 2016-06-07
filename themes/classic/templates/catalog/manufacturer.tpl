{extends file=$layout}

{block name='content'}
  <section id="main">

    {block name='manufacturer_header'}
      <h1>{l s='List of products by manufacturer %s' d='Shop.Theme.Catalog' sprintf=$manufacturer.name}</h1>
      <div id="manufacturer-short_description">{$manufacturer.short_description nofilter}</div>
      <div id="manufacturer-description">{$manufacturer.description nofilter}</div>
    {/block}

    {block name='manufacturer_products'}
      {include file='catalog/_partials/products.tpl' products=$products}
    {/block}

  </section>
{/block}
