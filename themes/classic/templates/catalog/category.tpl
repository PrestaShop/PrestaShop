{extends file=$layout}

{block name='content'}
  <section id="main">

    {block name='category_header'}
      <div class="block-category">
        <h1 class="h4 text-uppercase _bolder">{$category.name}</h1>
        <div id="category-description" class="text-muted">{$category.description nofilter}</div>
        <div class="category-cover">
          <img src="{$category.image.large.url}" alt="{$category.image.legend}">
        </div>

      </div>
    {/block}

    {block name='category_products'}
      {include file='catalog/products.tpl' products=$products}
    {/block}

  </section>

{/block}
