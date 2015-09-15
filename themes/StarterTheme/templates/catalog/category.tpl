{extends "layout.tpl"}

{block name="content"}
  <section id="main">
    <h1>{$category.name}</h1>

    <div class="category-cover">
      <img src="{$category.image.large.url}" alt="{$category.image.legend}">
    </div>

    <div id="category-description">{$category.description nofilter}</div>

    {if $subcategories|count}
      <section id="subcategories">
        <h1>{l s='Subcategories'}</h1>
        <div class="subcategories">
          {foreach from=$subcategories item="subcategory"}
            {include './category-miniature.tpl' category=$subcategory}
          {/foreach}
        </div>
      </section>
    {/if}

    {if $products|count}
      <section id="products">
        <h1>{l s='Products'}</h1>

        {include './_partials/sort-by.tpl' options=$sort_options}

        <div class="products">
          {foreach from=$products item="product"}
            {include './product-miniature.tpl' product=$product}
          {/foreach}
        </div>
      </section>
    {/if}
  </section>
{/block}
