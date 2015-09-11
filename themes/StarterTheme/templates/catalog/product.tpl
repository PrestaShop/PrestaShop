{extends "layout.tpl"}

{block name="content"}

  <section id="main" itemscope itemtype="https://schema.org/Product">
    <meta itemprop="url" content="{$product.url}">

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
              {foreach from=$product.labels item=label}
                <li>{$label.label}</li>
              {/foreach}
            </ul>
          {/block}

          {block name="product_images"}
            <ul class="product-images">
              {foreach from=$product.images item=image}
                <li><img src="{$image.small.url}" alt="{$image.legend}" title="{$image.legend}" width="{$image.small.width}" height="{$image.small.height}" itemprop="image" /></li>
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
              <p id="product-quantities">{$product.quantity} {$quantity_label}</p>
            {/if}
          {/block}

          {block name="product_availability"}
            {if $product.show_availability}
              <p id="product-availability">{$product.availability_message}</p>
            {/if}
          {/block}

          {block name="product_availability_date"}
            {if $product.availability_date}
              <p id="product-availability-date">
                <label>{l s='Availability date:'} </label>
                <span>{$product.availability_date}</span>
              </p>
            {/if}
          {/block}

          {block name="product_out_of_stock"}
            <div class="product-out-of-stock">
              {hook h="actionProductOutOfStock" product=$product}
            </div>
          {/block}

          {block name="product_extra_right"}
            <div class="product-extra-right">
              {hook h='displayRightColumnProduct'}
            </div>
          {/block}

          {* StarterTheme: Content Only *}
          {block name="product_extra_left"}
            <div class="product-extra-left">
              {hook h='displayLeftColumnProduct'}
            </div>
          {/block}
          {* StarterTheme: Content Only End *}

          {block name="product_buy"}
            <form action="{$urls.pages.cart}" method="post">
              <input type="hidden" name="token" value="{$static_token}" />
              <input type="hidden" name="id_product" value="{$product.id}" id="product_page_product_id" />
              <input type="hidden" name="add" value="1" />
              <input type="hidden" name="id_product_attribute" id="idCombination" value="" />

              <p itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                <link itemprop="availability" href="https://schema.org/InStock"/>
                <span itemprop="price" content="{$productPrice}">{$productPriceWithCurrency}</span>
                {if $display_taxes_label}
                 {if $priceDisplay} {l s='tax excl.'}{else} {l s='tax incl.'}{/if}
                {/if}
                <meta itemprop="priceCurrency" content="{$currency.iso_code}" />
                {hook h="displayProductPriceBlock" product=$product type="price"}
              </p>
            </form>
          {/block}

          {* StarterTheme: Content Only *}

          {block name="product_discounts"}
            {if $quantity_discounts}
              <section class="product-discounts">
                <h3>{l s='Volume discounts'}</h3>
              </section>
            {/if}
          {/block}

          {block name="product_features"}
            {if $product.features}
              <section class="product-features">
                <h3>{l s='Data sheet'}</h3>
                <ul>
                  {foreach from=$product.features item=feature}
                  <li>{$feature.name} - {$feature.value}</td>
                  {/foreach}
                </ul>
              </section>
            {/if}
          {/block}

          {block name="product_pack"}
            {if $packItems}
              <section class="product-pack">
                <h3>{l s='Pack content'}</h3>
                {* StarterTheme: Product list *}
            </section>
            {/if}
          {/block}

          {block name="product_accessories"}
            {if $accessories}
              <section class="product-accessories">
                <h3>{l s='Accessories'}</h3>
            </section>
            {/if}
          {/block}

          {block name="product_footer"}
            {hook h="displayFooterProduct" product=$product category=$category}
          {/block}

          {block name="product_attachments"}
            {if $product.attachments}
              <section class="product-attachments">
                <h3>{l s='Download'}</h3>
                {foreach from=$product.attachments item=attachment}
                  <div class="attachment">
                    <h4><a href="{$link->getPageLink('attachment', true, NULL, "id_attachment={$attachment.id_attachment}")}">{$attachment.name}</a></h4>
                    <p>{$attachment.description}</p>
                    <a href="{$link->getPageLink('attachment', true, NULL, "id_attachment={$attachment.id_attachment}")}">
                      {l s="Download"} ({Tools::formatBytes($attachment.file_size, 2)})
                    </a>
                  </div>
                {/foreach}
              </section>
            {/if}
          {/block}

          {* StarterTheme: Content Only End *}
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
