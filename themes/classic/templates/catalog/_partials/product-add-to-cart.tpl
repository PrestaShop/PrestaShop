<div class="product-add-to-cart">
  {block name='product_quantity'}
    <p class="product-quantity">
      <label for="quantity_wanted">{l s='Quantity'}</label>
      <input type="text" name="qty" id="quantity_wanted" value="{$product.quantity_wanted}" class="input-group">
    </p>
  {/block}

  {block name='product_minimal_quantity'}
    {if $product.minimal_quantity > 1}
      <p class="product-minimal-quantity">
        {l s='The minimum purchase order quantity for the product is %s.' sprintf=$product.minimal_quantity}
      </p>
    {/if}
  {/block}

  <button class="btn btn-primary add-to-cart" data-button-action="add-to-cart" type="submit" {if !$product.add_to_cart_url}disabled{/if}>
    <i class="material-icons shopping-cart">&#xE547;</i>
    {l s='Add to cart'}
  </button>

  {block name='product_availability'}
   {if $product.show_availability}
      <p id="product-availability"><i class="material-icons product-available">&#xE5CA;</i>{$product.availability_message}</p>
   {/if}
  {/block}
  {hook h='displayProductButtons' product=$product}
</div>
