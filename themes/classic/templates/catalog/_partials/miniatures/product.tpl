<article class="product-miniature js-product-miniature" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}" itemscope itemtype="http://schema.org/Product">
  <div class="thumbnail-container">
    {block name='product_thumbnail'}
      <a href="{$product.url}" class="thumbnail product-thumbnail">
        <img
          src = "{$product.cover.bySize.home_default.url}"
          alt = "{$product.cover.legend}"
          data-full-size-image-url = "{$product.cover.large.url}"
        >
      </a>
    {/block}

    <div class="product-description">
      {block name='product_name'}
        <h1 class="h3 product-title" itemprop="name"><a href="{$product.url}">{$product.name|truncate:30:'...'}</a></h1>
      {/block}

      {block name='product_description_short'}
        <div class="product-description-short" itemprop="description">{$product.description_short nofilter}</div>
      {/block}

      {block name='product_list_actions'}
        <div class="product-list-actions">
          {if $product.add_to_cart_url}
              <a
                class = "add-to-cart btn btn-primary"
                href  = "{$product.add_to_cart_url}"
                rel   = "nofollow"
                data-id-product="{$product.id_product}"
                data-id-product-attribute="{$product.id_product_attribute}"
                data-link-action="add-to-cart"
                title = "{l s='Add to cart'}"
              >{l s='Add to cart'}</a>
          {/if}
          {hook h='displayProductListFunctionalButtons' product=$product}
        </div>
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

          {hook h='displayProductPriceBlock' product=$product type='price'}
          {hook h='displayProductPriceBlock' product=$product type='unit_price'}
          {hook h='displayProductPriceBlock' product=$product type='list_taxes'}
          {hook h='displayProductPriceBlock' product=$product type='after_price'}

          {if !$product.is_virtual}
            {hook h='displayProductDeliveryTime' product=$product}
          {/if}

          {hook h='displayProductPriceBlock' product=$product type='weight'}
        </div>
      {/block}

      {block name='product_flags'}
        <ul class="product-flags">
          {foreach from=$product.flags item=flag}
            <li class="{$flag.type}">{$flag.label}</li>
          {/foreach}
        </ul>
      {/block}

      {block name='product_availability'}
        {if $product.show_availability}
          {* availability may take the values "available" or "unavailable" *}
          <span class='product-availability {$product.availability}'>{$product.availability_message}</span>
        {/if}
      {/block}

    </div>
    <div class="highlighted-informations">
      <a
        href="#"
        title="{l s='Quick view'}"
        class="quick-view"
        data-link-action="quickview"
      >
        <i class="material-icons search">&#xE8B6;</i> {l s='Quick view'}
      </a>
      {block name='product_variants'}
        {include file='catalog/_partials/variant-links.tpl' variants=$product.main_variants}
      {/block}
    </div>

  </div>
</article>
