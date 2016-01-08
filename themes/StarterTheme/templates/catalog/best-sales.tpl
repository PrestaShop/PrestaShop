{extends $layout}

{block name="content"}
  <section id="main">
    {block name="search_products"}
      {include file="catalog/products.tpl" products=$products}
    {/block}
  </section>
{/block}
