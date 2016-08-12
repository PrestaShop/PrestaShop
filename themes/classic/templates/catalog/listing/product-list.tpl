{extends file=$layout}

{block name='content'}
  <section id="main">

    {block name='product_list_header'}
      <h2 class="h2">{$listing.label}</h2>
    {/block}

    {block name='product_list'}
      {include file='catalog/_partials/products.tpl' listing=$listing}
    {/block}

  </section>
{/block}
