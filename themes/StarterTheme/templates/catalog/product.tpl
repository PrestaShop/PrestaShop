{extends "layout.tpl"}

{block name="content"}

  <section id="main" itemscope itemtype="https://schema.org/Product">
    <meta itemprop="url" content="{$link->getProductLink($product)}">

    {block name="page_header_container"}
      <header class="page-header">
        {block name="page_header"}
          <h1 itemprop="name">{block name="page_title"}{$product->name}{/block}</h1>
        {/block}
      </header>
    {/block}

    {block name="page_content_container"}
      <section id="content" class="page-content">
        {block name="page_content"}
          {block name="product_images"}
            <ul class="product-images">
              {foreach from=$images item=image}
                <li><img src="{$image.urls.default.link}" alt="{$image.legend}" title="{$image.legend}" width="{$image.urls.default.width}" height="{$image.urls.default.height}" itemprop="image" /></li>
              {/foreach}
            </ul>
          {/block}
        {/block}
      </section>
    {/block}

    {block name="page_footer_container"}
      <footer class="page-footer">
        {block name="page_footer"}
          <!-- Footer content -->
        {/block}
      </footer>
    {/block}

  </section>

{/block}
