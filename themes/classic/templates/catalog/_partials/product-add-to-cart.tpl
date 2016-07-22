<div class="product-add-to-cart">
  {block name='product_quantity'}
    <p class="product-quantity">
      <label for="quantity_wanted">{l s='Quantity' d='Shop.Theme.Catalog'}</label><br>
      <input type="text" name="qty" id="quantity_wanted" value="{$product.quantity_wanted}" class="input-group">
    </p>
  {/block}

  {block name='product_minimal_quantity'}
    {if $product.minimal_quantity > 1}
      <p class="product-minimal-quantity">
        {l
          s='The minimum purchase order quantity for the product is %quantity%.'
          d='Shop.Theme.Checkout'
          sprintf=['%quantity%' => $product.minimal_quantity]
        }
      </p>
    {/if}
  {/block}

  <button class="btn btn-primary add-to-cart" data-button-action="add-to-cart" type="submit" {if !$product.add_to_cart_url}disabled{/if}>
    <i class="material-icons shopping-cart">&#xE547;</i>
    {l s='Add to cart' d='Shop.Theme.Actions'}
  </button>

  {block name='product_availability'}
   {if $product.show_availability}
      <p id="product-availability">
        {if $product.availability == 'available'}
        <i class="material-icons product-available">&#xE5CA;</i>
        {else}
        <i class="material-icons product-unavailable">&#xE14B;</i>
        {/if}
        {$product.availability_message}
      </p>
   {/if}
  {/block}
  {hook h='displayProductButtons' product=$product}
</div>
