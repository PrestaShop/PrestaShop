{extends $layout}

{block name="content"}
  <section id="main">

    {block name="category_header"}
      <h1>{$category.name}</h1>
      <div class="category-cover">
        <img src="{$category.image.large.url}" alt="{$category.image.legend}">
      </div>
      <div id="category-description">{$category.description nofilter}</div>
    {/block}

    {block name="category_subcategories"}
      {if $subcategories|count}
        <div class="subcategories">
            {foreach from=$subcategories item="subcategory"}
              {block name="category_miniature"}
                {include './category-miniature.tpl' category=$subcategory}
              {/block}
            {/foreach}
          </div>
      {/if}
    {/block}

    {block name="category_products"}
      {include file='./products.tpl' products=$products}
    {/block}

  </section>
{/block}
