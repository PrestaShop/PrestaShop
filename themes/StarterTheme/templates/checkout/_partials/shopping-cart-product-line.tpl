<span class="product-quantity">{$product.quantity}</span>
<span class="product-name">{$product.name}</span>
<span class="product-price">{$product.price}</span>
<a class="remove-from-cart" rel="nofollow" href="{$product.remove_from_cart_url}">{l s="Remove" mod="blockcart"}</a>
{if $product.customizations|count}
  <div class="customizations">
    <ul>
      {foreach from=$product.customizations item="customization"}
        <li>
          <span class="product-quantity">{$customization.quantity}</span>
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
