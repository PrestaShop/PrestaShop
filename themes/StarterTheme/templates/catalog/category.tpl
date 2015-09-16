{extends "layout.tpl"}

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
        <section id="subcategories">
          <h1>{l s='Subcategories'}</h1>
          <div class="subcategories">
            {foreach from=$subcategories item="subcategory"}
              {block name="category_miniature"}
                {include './category-miniature.tpl' category=$subcategory}
              {/block}
            {/foreach}
          </div>
        </section>
      {/if}
    {/block}

    {block name="category_products"}
      {if $products|count}
        <section id="products">
          <h1>{l s='Products'}</h1>

          {block name="sort_by"}
            {include './_partials/sort-by.tpl' options=$sort_options}
          {/block}

          <div class="products">
            {foreach from=$products item="product"}
              {block name="product_miniature"}
                {include './product-miniature.tpl' product=$product}
              {/block}
            {/foreach}
          </div>
        </section>
      {/if}
    {/block}

  </section>
{/block}
