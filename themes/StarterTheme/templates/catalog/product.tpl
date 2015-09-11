{extends "layout.tpl"}

{block name="content"}

  <section id="main" itemscope itemtype="https://schema.org/Product">
    <meta itemprop="url" content="{$link->getProductLink($product)}">

    {block name="page_header_container"}
      <header class="page-header">
        {block name="page_header"}
          <h1 itemprop="name">{block name="page_title"}{$product.name}{/block}</h1>
        {/block}
      </header>
    {/block}

    {block name="page_content_container"}
      <section id="content" class="page-content">
        {block name="page_content"}
          {block name="product_labels"}
            <ul class="product-labels">
              {foreach from=$labels item=label}
                <li>{$label.label}</li>
              {/foreach}
            </ul>
          {/block}

          {block name="product_images"}
            <ul class="product-images">
              {foreach from=$images item=image}
                <li><img src="{$image.urls.default.link}" alt="{$image.legend}" title="{$image.legend}" width="{$image.urls.default.width}" height="{$image.urls.default.height}" itemprop="image" /></li>
              {/foreach}
            </ul>
          {/block}

          {block name="product_reference"}
            {if $product.reference}
              <p id="product-reference">
                <label>{l s='Reference:'} </label>
                <span itemprop="sku">{$product.reference}</span>
              </p>
            {/if}
          {/block}

          {block name="product_condition"}
            {if $product.condition}
              <p id="product-condition">
                <label>{l s='Condition:'} </label>
                <link itemprop="itemCondition" href="{$product_conditions.{$product.condition}.schema_url}"/>
                <span>{$product_conditions.{$product.condition}.label}</span>
              </p>
            {/if}
          {/block}

          {block name="product_description_short"}
            <div id="product-description-short" itemprop="description">{$product.description_short}</div>
          {/block}

          {block name="product_description"}
            <div id="product-description">{$product.description}</div>
          {/block}

          {block name="product_quantities"}
            {if $display_quantities}
              <p id="product-quantities"></p>
            {/if}
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
