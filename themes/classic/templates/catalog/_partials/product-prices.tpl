{if $product.show_price}
  <div class="product-prices">
    {block name='product_discount'}
      {if $product.has_discount}
        <p class="product-discount">
          {hook h='displayProductPriceBlock' product=$product type="old_price"}
          <span class="regular-price">{$product.regular_price}</span>
        </p>
      {/if}
    {/block}

    {block name='product_price'}
      <p
        class="product-price h5 {if $product.has_discount}has-discount{/if}"
        itemprop="offers"
        itemscope
        itemtype="https://schema.org/Offer"
      >
        <link itemprop="availability" href="https://schema.org/InStock"/>
        <meta itemprop="priceCurrency" content="{$currency.iso_code}">
        <span itemprop="price" content="{$product.price_amount}">{$product.price}</span>
        {if $feature_active.display_taxes_label}
          <small class="text-capitalize">{$product.labels.tax_short}</small>
        {/if}

        {hook h='displayProductPriceBlock' product=$product type="price"}

        {if $product.has_discount}
          {if $product.discount_type === 'percentage'}
            <span class="discount discount-percentage">{l s='Save %s' sprintf=$product.discount_percentage}</span>
          {else}
            <span class="discount discount-amount">{l s='Save %s' sprintf=$product.discount_amount}</span>
          {/if}
        {/if}
      </p>
    {/block}

    {block name='product_without_taxes'}
      {if $priceDisplay == 2}
        <p class="product-without-taxes">{l s='%s tax excl.' sprintf=$product.price_tax_exc}</p>
      {/if}
    {/block}

    {block name='product_pack_price'}
      {if $displayPackPrice}
        <p class="product-pack-price">{l s='Instead of %s' sprintf=$noPackPrice}</span></p>
      {/if}
    {/block}

    {block name='product_ecotax'}
      {if $product.ecotax.amount > 0}
        <p class="price-ecotax">{l s='Including %s for ecotax' sprintf=$product.ecotax.value}
          {if $product.has_discount}
            {l s='(not impacted by the discount)'}
          {/if}
        </p>
      {/if}
    {/block}

    {block name='product_unit_price'}
      {if $displayUnitPrice}
        <p class="product-unit-price sub">{l s='(%s)' sprintf=[$product.unit_price_full]}</p>
      {/if}
    {/block}

    {hook h='displayProductPriceBlock' product=$product type="weight" hook_origin='product_sheet'}
    {hook h='displayProductPriceBlock' product=$product type="after_price"}
  </div>
{/if}
