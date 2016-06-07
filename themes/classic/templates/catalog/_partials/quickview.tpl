<div id="quickview-modal-{$product.id}-{$product.id_product_attribute}" class="modal fade quickview" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
   <div class="modal-content">
     <div class="modal-header">
       <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
       </button>
     </div>
     <div class="modal-body">
      <div class="row">
        <div class="col-md-6">
          {block name='product_cover_tumbnails'}
            {include file='catalog/_partials/product-cover-thumbnails.tpl'}
          {/block}
          <div class="arrows js-arrows">
            <i class="material-icons arrow-up js-arrow-up'">&#xE316;</i>
            <i class="material-icons arrow-down js-arrow-down'">&#xE313;</i>
          </div>
        </div>
        <div class="col-md-6">
          <h1 class="h1">{$product.name}</h1>
          {block name='product_prices'}
            {include file='catalog/_partials/product-prices.tpl'}
          {/block}
          {block name='product_description_short'}
            <div id="product-description-short" itemprop="description">{$product.description_short nofilter}</div>
          {/block}
          {block name='product_buy'}
            <form action="{$urls.pages.cart}" method="post" id="add-to-cart-or-refresh">
              <input type="hidden" name="token" value="{$static_token}">
              <input type="hidden" name="id_product" value="{$product.id}" id="product_page_product_id">
            {block name='product_variants'}
              {include file='catalog/_partials/product-variants.tpl'}
            {/block}
            {block name='product_quantity'}
              <p class="product-quantity">
                <label for="quantity_wanted">{l s='Quantity' d='Shop.Theme.Catalog'}</label>
                <input type="text" name="qty" id="quantity_wanted" value="{$product.quantity_wanted}" class="input-group">
              </p>
            {/block}

            <button class="btn btn-primary add-to-cart" data-button-action="add-to-cart" type="submit" {if !$product.add_to_cart_url}disabled{/if}>
              <i class="material-icons shopping-cart">&#xE547;</i>
              {l s='Add to cart' d='Shop.Theme.Actions'}
            </button>
            {block name='product_refresh'}
              <input class="product-refresh hidden-xs-up" name="refresh" type="submit" value="{l s='Refresh' d='Shop.Theme.Actions'}">
            {/block}
          </form>
        {/block}
        </div>
      </div>
     </div>
     <div class="modal-footer">
       {hook h='displayProductButtons' product=$product}
    </div>
   </div>
 </div>
</div>
