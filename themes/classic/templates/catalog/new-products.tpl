{extends $layout}

{block name="content"}
  <section id="main">
    {block name="search_products"}
      {include file='./products.tpl' products=$products}
    {/block}
  </section>
{/block}
