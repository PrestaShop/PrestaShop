<div class="product-line-grid">
  <!--  product left content: image-->
  <div class="product-line-grid-left col-md-2">
    <span class="product-image media-middle">
      <img class="" src="{$product.cover.small.url}">
    </span>
  </div>

  <!--  product left body: description -->
  <div class="product-line-grid-body col-md-6">
    <div class="product-line-info">
      <a class="label" href="{$product.url}">{$product.name}</a>
    </div>

    <div class="product-line-info">
      <span class="value">{$product.total}</span>
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
  <div class="product-line-grid-right product-line-actions col-md-4">
    {*if $product.down_quantity_url}<a href="{$product.down_quantity_url}" data-link-action="update-quantity">-</a>{/if*}
    {*if $product.up_quantity_url}<a href="{$product.up_quantity_url}" data-link-action="update-quantity">+</a>{/if*}
    <input class="cart-line-product-quantity" productid="{$product.id_product}" type="text" value="{$product.quantity}" name="product-quantity-spin">
    <div class="cart-line-product-actions ">
      <a
          class                       = "remove-from-cart"
          rel                         = "nofollow"
          href                        = "{$product.remove_from_cart_url}"
          data-link-action            = "delete-from-cart"
          data-id-product             = "{$product.id_product|escape:'javascript'}"
          data-id-product-attribute   = "{$product.id_product_attribute|escape:'javascript'}"
      >
        <i class="material-icons pull-xs-left _gray-darker _vertical-align">delete</i>
      </a>
    </div>

    <span class="product-price pull-xs-left _vertical-align">{$product.total}</span>

    {if $product.customizations|count}
      <div class="customizations">
        <ul>
          {foreach from=$product.customizations item="customization"}
            <li>
              {if $customization.down_quantity_url}<a href="{$customization.down_quantity_url}" data-link-action="update-quantity">-</a>{/if}
              <span class="product-quantity">{$customization.quantity}</span>
              {if $customization.up_quantity_url}<a href="{$customization.up_quantity_url}" data-link-action="update-quantity">+</a>{/if}
              <a href="{$customization.remove_from_cart_url}" class="remove-from-cart" rel="nofollow">{l s='Remove'}</a>
              <ul>
                {foreach from=$customization.fields item="field"}
                  <li>
                    <label>{$field.label}</label>
                    {if $field.type == 'text'}
                      <span>{$field.text}</span>
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
