<div id="blockcart-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title h6 text-xs-center" id="myModalLabel"><i class="material-icons">&#xE876;</i>{l s='Product Successfully Added to Your Shopping Cart' mod='blockcart'}</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6 divide-right">
            <div class="row">
              <div class="col-md-6">
                <img class="product-image" src="{$product.cover.medium.url}" alt="{$product.cover.legend}" title="{$product.cover.legend}" itemprop="image">
              </div>
              <div class="col-md-6">
                <h6 class="h6 product-name">{$product.name}</h6>
                <p>{$product.price}</p>
                {foreach from=$product.attributes item="property_value" key="property"}
                  <span><strong>{$property}</strong>: {$property_value}</span><br>
                {/foreach}
                <p><strong>{l s='Quantity:' mod='blockcart'}</strong>&nbsp;{$product.cart_quantity}</p>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="cart-content">
              {if $cart.products_count > 1}
                <p class="cart-products-count">{l s='There are %s items in your cart.' sprintf=$cart.products_count}</p>
              {else}
                <p class="cart-products-count">{l s='There is %s item in your cart.' sprintf=$cart.products_count}</p>
              {/if}
              <p><strong>{l s='Total products:' mod='blockcart'}</strong>&nbsp;{$cart.subtotals.products.amount}</p>
              <p><strong>{l s='Total shipping:' mod='blockcart'}</strong>&nbsp;{$cart.subtotals.shipping.amount}</p>
              <p><strong>{l s='Total:' mod='blockcart'}</strong>&nbsp;{$cart.total.amount}</p>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">{l s='Continue shopping' mod='blockcart'}</button>
              <a href="{$cart_url}" class="btn btn-primary"><i class="material-icons">&#xE876;</i>{l s='proceed to checkout' mod='blockcart'}</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
