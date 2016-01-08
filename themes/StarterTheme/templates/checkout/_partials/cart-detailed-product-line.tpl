<span class="product-image"><img src="{$product.cover.small.url}"></span>
<span class="product-name"><a href="{$product.url}">{$product.name}</a></span>
<span class="product-name">{$product.attributes}</span>
<span class="product-availability">{$product.availability}</span>
<span class="product-price">{$product.price}</span>
{if $product.down_quantity_url}<a href="{$product.down_quantity_url}">-</a>{/if}
<span class="product-quantity">{$product.quantity}</span>
{if $product.up_quantity_url}<a href="{$product.up_quantity_url}">+</a>{/if}
<a
  class                       = "remove-from-cart"
  rel                         = "nofollow"
  href                        = "{$product.remove_from_cart_url}"
  data-link-action            = "remove-from-cart"
  data-id-product             = "{$product.id_product|escape:'javascript'}"
  data-id-product-attribute   = "{$product.id_product_attribute|escape:'javascript'}"
 >
  {l s="Remove" mod="blockcart"}
</a>

<span class="product-price">{$product.total}</span>
{if $product.customizations|count}
    <div class="customizations">
        <ul>
            {foreach from=$product.customizations item="customization"}
                <li>
                    {if $customization.down_quantity_url}<a href="{$customization.down_quantity_url}">-</a>{/if}
                    <span class="product-quantity">{$customization.quantity}</span>
                    {if $customization.up_quantity_url}<a href="{$customization.up_quantity_url}">+</a>{/if}
                    <a href="{$customization.remove_from_cart_url}" class="remove-from-cart" rel="nofollow">{l s="Remove"}</a>
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
