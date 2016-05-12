{extends file=$layout}

{block name='content'}
  <section id="main">

    {block name='category_header'}
      {if $category.description}
        <div class="block-category card">
          <h1 class="h1">{$category.name}</h1>
          <div id="category-description" class="text-muted">{$category.description nofilter}</div>
          <div class="category-cover">
            <img src="{$category.image.large.url}" alt="{$category.image.legend}">
          </div>
        </div>
      {/if}
    {/block}

    {block name='category_products'}
      {include file='catalog/_partials/products.tpl' products=$products label=$category.name}
    {/block}

  </section>

{/block}
