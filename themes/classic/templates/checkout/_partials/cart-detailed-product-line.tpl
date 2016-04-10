<div class="product-line-grid">
  <!--  product left content: image-->
  <div class="product-line-grid-left col-md-3">
    <span class="product-image media-middle">
      <img src="{$product.cover.bySize.cart_default.url}" alt="{$product.name|escape:'quotes'}">
    </span>
  </div>

  <!--  product left body: description -->
  <div class="product-line-grid-body col-md-4">
    <div class="product-line-info">
      <a class="label" href="{$product.url}">{$product.name}</a>
    </div>

    <div class="product-line-info">
      <span class="value">{$product.price}</span>
      {if $product.unit_price_full}
        <small class="sub">{$product.unit_price_full}</small>
      {/if}
    </div>

    <br/>

    {foreach from=$product.attributes key="attribute" item="value"}
      <div class="product-line-info">
        <span class="label">{$attribute}:</span>
        <span class="value">{$value}</span>
      </div>
    {/foreach}

    <div class="product-line-info">
      <span class="label">{l s="Quantity"}:</span>
      <span class="value">{$product.quantity}</span>
    </div>

    <br/>

  </div>

  <!--  product left body: description -->
  <div class="product-line-grid-right product-line-actions col-md-5">
    <div class="row">
      <div class="col-md-3">
        {*if $product.down_quantity_url}<a href="{$product.down_quantity_url}" data-link-action="update-quantity">-</a>{/if*}
        {*if $product.up_quantity_url}<a href="{$product.up_quantity_url}" data-link-action="update-quantity">+</a>{/if*}
        <input class="cart-line-product-quantity" data-down-url="{$product.down_quantity_url}" data-up-url="{$product.up_quantity_url}" data-product-id="{$product.id_product}" type="text" value="{$product.quantity}" name="product-quantity-spin">
      </div>
      <div class="col-md-7">
        <span class="product-price pull-xs-left"><strong>{$product.total}</strong></span>
      </div>
      <div class="col-md-2">
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
        </div>
      </div>
      {if $product.customizations|count}
        <div class="customizations">
          <ul>
            {foreach from=$product.customizations item="customization"}
              <li>
                {if count($product.customizations) > 1}
                    {if $customization.down_quantity_url}<a href="{$customization.down_quantity_url}" data-link-action="update-quantity">-</a>{/if}
                    <span class="product-quantity">{$customization.quantity}</span>
                    {if $customization.up_quantity_url}<a href="{$customization.up_quantity_url}" data-link-action="update-quantity">+</a>{/if}
                    <a href="{$customization.remove_from_cart_url}" class="remove-from-cart" rel="nofollow">{l s='Remove'}</a>
                {/if}
                <ul>
                  {foreach from=$customization.fields item="field"}
                    <li>
                      <label>{$field.label}</label>
                      {if $field.type == 'text'}
                        {if (int)$field.id_module}
                          <span>{$field.text nofilter}</span>
                        {else}
                          <span>{$field.text}</span>
                        {/if}
                      {else if $field.type == 'image'}
                        <img src="{$field.image.small.url}">
                      {/if}
                    </li>
                  {/foreach}
                </ul>
              </li>
            {/foreach}
          </ul>
        </div>
      {/if}
      </div>
    </div>
</div>
