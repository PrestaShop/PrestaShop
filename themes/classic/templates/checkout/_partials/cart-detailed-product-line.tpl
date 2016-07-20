<div class="product-line-grid">
  <!--  product left content: image-->
  <div class="product-line-grid-left col-md-2 col-xs-4">
    <span class="product-image media-middle">
      <img src="{$product.cover.bySize.cart_default.url}" alt="{$product.name|escape:'quotes'}">
    </span>
  </div>

  <!--  product left body: description -->
  <div class="product-line-grid-body col-md-5 col-xs-5">
    <div class="product-line-info">
      <a class="label" href="{$product.url}">{$product.name}</a>
    </div>

    <div class="product-line-info">
      <span class="value">{$product.price}</span>
      {if $product.unit_price_full}
        <div class="unit-price-cart">{$product.unit_price_full}</div>
      {/if}
    </div>

    <br/>

    {foreach from=$product.attributes key="attribute" item="value"}
      <div class="product-line-info">
        <span class="label">{$attribute}:</span>
        <span class="value">{$value}</span>
      </div>
    {/foreach}

    {if $product.customizations|count}
      {foreach from=$product.customizations item="customization"}
        {foreach from=$customization.fields item="field"}
          <div class="product-line-info">
            <span class="label">{$field.label}:</span>
            <span class="value">
              {if $field.type == 'text'}
                {if (int)$field.id_module}
                  {$field.text nofilter}
                {else}
                  {$field.text}
                {/if}
              {elseif $field.type == 'image'}
                <img src="{$field.image.small.url}">
              {/if}
            </span>
          </div>
        {/foreach}
      {/foreach}
    {/if}
  </div>

  <!--  product left body: description -->
  <div class="product-line-grid-right product-line-actions col-md-5 col-xs-3">
    <div class="row">
      <div class="col-md-1 col-xs-12">
        <input class="cart-line-product-quantity" data-down-url="{$product.down_quantity_url}" data-up-url="{$product.up_quantity_url}" data-product-id="{$product.id_product}" type="text" value="{$product.quantity}" name="product-quantity-spin">
      </div>
      <div class="col-md-5 col-md-offset-3 col-xs-12">
        <span class="product-price"><strong>{$product.total}</strong></span>
      </div>
      <div class="col-md-1 col-xs-12">
        <div class="cart-line-product-actions ">
          <a
              class                       = "remove-from-cart"
              rel                         = "nofollow"
              href                        = "{$product.remove_from_cart_url}"
              data-link-action            = "delete-from-cart"
              data-id-product             = "{$product.id_product|escape:'javascript'}"
              data-id-product-attribute   = "{$product.id_product_attribute|escape:'javascript'}"
              data-id-customization   	  = "{$product.id_customization|escape:'javascript'}"
          >
            <i class="material-icons pull-xs-left">delete</i>
          </a>
          {hook h='displayCartExtraProductActions' product=$product}
        </div>
      </div>
    </div>
  </div>

  <div class="clearfix"></div>
</div>
