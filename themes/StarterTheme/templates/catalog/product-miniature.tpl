<article class="product-miniature" itemscope itemtype="http://schema.org/Product">

  {block name="product_thumbnail"}
    <a href="{$product.url}" class="thumbnail product-thumbnail">
      <img
        src = "{$product.cover.medium.url}"
        alt = "{$product.cover.legend}"
        data-full-size-image-url = "{$product.cover.large.url}"
      >
    </a>
  {/block}

  {block name="product_name"}
    <h1 class="h2" itemprop="name"><a href="{$product.url}">{$product.name}</a></h1>
  {/block}

  {block name="product_description_short"}
    <div class="product-description-short" itemprop="description">{$product.description_short}</div>
  {/block}

  {block name="product_add_to_cart"}
    {if $product.add_to_cart_url}
        <a
          class = "add-to-cart"
          href  = "{$product.add_to_cart_url}"
          rel   = "nofollow"
          data-id-product="{$product.id_product}"
          data-id-product-attribute="{$product.id_product_attribute}"
        >{l s='Add to cart'}</a>
    {/if}
  {/block}

  {block name="product_variants"}
    {include './_partials/variant-links.tpl' variants=$product.main_variants}
  {/block}

  {block name="product_price"}
    <div class="price-container">
      {if $product.has_discount}
        <span class="regular-price">{$product.regular_price}</span>
        {if $product.discount_type === 'percentage'}
          <span class="discount-percentage">{$product.discount_percentage}</span>
        {/if}
      {/if}
      <span itemprop="price" class="price">{$product.price}</span>
    </div>
  {/block}

  {block name="product_labels"}
    <ul class="product-labels">
      {foreach from=$product.labels item=label}
        <li class="{$label.type}">{$label.label}</li>
      {/foreach}
    </ul>
  {/block}

  {block name="product_availability"}
    {if $product.show_availability}
      {* availability may take the values "available" or "unavailable" *}
      <span class='product-availability {$product.availability}'>{$product.availability_message}</span>
    {/if}
  {/block}

  {* TODO: Hooks *}

</article>
