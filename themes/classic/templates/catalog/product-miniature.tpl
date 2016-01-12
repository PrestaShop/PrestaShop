<article class="product-miniature col-md-3" itemscope itemtype="http://schema.org/Product">

  {block name='product_thumbnail'}
    <a href="{$product.url}" class="thumbnail product-thumbnail">
      <img
        src = "{$product.cover.medium.url}"
        alt = "{$product.cover.legend}"
        data-full-size-image-url = "{$product.cover.large.url}"
      >
    </a>
  {/block}

  {block name='product_name'}
    <h1 class="h2" itemprop="name"><a href="{$product.url}">{$product.name}</a></h1>
  {/block}

  {block name='product_description_short'}
    <div class="product-description-short" itemprop="description">{$product.description_short nofilter}</div>
  {/block}

  {block name='product_list_actions'}
    <div class="product-list-actions">
      {if $product.add_to_cart_url}
          <a
            class = "add-to-cart"
            href  = "{$product.add_to_cart_url}"
            rel   = "nofollow"
            data-id-product="{$product.id_product}"
            data-id-product-attribute="{$product.id_product_attribute}"
            data-link-action="add-to-cart"
          >{l s='Add to cart'}</a>
      {/if}
      {hook h='displayProductListFunctionalButtons' product=$product}
    </div>
  {/block}

  {block name='product_variants'}
    {include file='catalog/_partials/variant-links.tpl' variants=$product.main_variants}
  {/block}

  {block name='product_price_and_shipping'}
    <div class="product-price-and-shipping">
      {if $product.has_discount}
        {hook h='displayProductPriceBlock' product=$product type="old_price"}

        <span class="regular-price">{$product.regular_price}</span>
        {if $product.discount_type === 'percentage'}
          <span class="discount-percentage">{$product.discount_percentage}</span>
        {/if}
      {/if}

      {hook h='displayProductPriceBlock' product=$product type="before_price"}

      <span itemprop="price" class="price">{$product.price}</span>

      {hook h='displayProductPriceBlock' product=$product type="price"}
      {hook h='displayProductPriceBlock' product=$product type="unit_price"}
      {hook h='displayProductPriceBlock' product=$product type="after_price"}

      {if !$product.is_virtual}
        {hook h='displayProductDeliveryTime' product=$product}
      {/if}

      {hook h='displayProductPriceBlock' product=$product type="weight"}
    </div>
  {/block}

  {block name='product_labels'}
    <ul class="product-labels">
      {foreach from=$product.labels item=label}
        <li class="{$label.type}">{$label.label}</li>
      {/foreach}
    </ul>
  {/block}

  {block name='product_availability'}
    {if $product.show_availability}
      {* availability may take the values "available" or "unavailable" *}
      <span class='product-availability {$product.availability}'>{$product.availability_message}</span>
    {/if}
  {/block}

  {hook h='displayProductListReviews' product=$product}

</article>
