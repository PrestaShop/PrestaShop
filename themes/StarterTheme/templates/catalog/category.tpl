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
      <aside>
        {if $subcategories|count}
          <nav class="subcategories">
            <ul class="category-miniature">
              <li>
                {foreach from=$subcategories item="subcategory"}
                  {block name="category_miniature"}
                    {include './category-miniature.tpl' category=$subcategory}
                  {/block}
                {/foreach}
              </li>
            </ul>
          </nav>
        {/if}
      </aside>
    {/block}

    {block name="category_products"}
      {include file='./products.tpl' products=$products}
    {/block}

  </section>

{/block}
