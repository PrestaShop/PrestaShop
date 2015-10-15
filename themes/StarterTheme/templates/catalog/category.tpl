{extends $layout}

{block name="content"}
  <header id="main">

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

  </header>
  {block name="category_products"}
    {if $products|count}
      <section id="products">
        <h1>{$category.name}&nbsp;{l s='Product''s'}</h1>

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

{/block}
